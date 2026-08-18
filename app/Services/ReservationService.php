<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\Inventory;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservationService
{
    public function __construct(private PushNotifier $push)
    {
    }

    /**
     * Prices every line from the catalogue and applies the take-out surcharge.
     *
     * Nothing here trusts the caller for money: the client sends product ids,
     * sizes and quantities, and every peso is derived from the inventories
     * table. A tampered price in the request body is simply ignored.
     *
     * @param  array  $lines  [['inventory_id' => int, 'size' => string, 'quantity' => int], ...]
     * @return array{items: array, cup_count: int, subtotal: float, takeout_fee: float, total: float}
     */
    public function quote(array $lines, string $serviceType): array
    {
        if ($lines === []) {
            throw ValidationException::withMessages([
                'items' => 'Add at least one drink to your reservation.',
            ]);
        }

        $products = Inventory::whereIn('id', array_column($lines, 'inventory_id'))->get()->keyBy('id');

        $items = [];
        $cupCount = 0;
        $subtotal = 0.0;

        foreach ($lines as $index => $line) {
            $product = $products->get($line['inventory_id'] ?? null);

            if (! $product) {
                throw ValidationException::withMessages([
                    "items.{$index}.inventory_id" => 'That drink is no longer on the menu.',
                ]);
            }

            $size = ($line['size'] ?? ReservationItem::SIZE_REGULAR) === ReservationItem::SIZE_LARGE
                ? ReservationItem::SIZE_LARGE
                : ReservationItem::SIZE_REGULAR;

            $quantity = max(1, (int) ($line['quantity'] ?? 1));

            $unitPrice = (float) ($size === ReservationItem::SIZE_LARGE
                ? $product->large_price
                : $product->regular_price);

            $lineTotal = round($unitPrice * $quantity, 2);

            $items[] = [
                'inventory_id' => $product->id,
                'name' => $product->name,
                'size' => $size,
                'unit_price' => $unitPrice,
                'quantity' => $quantity,
                'line_total' => $lineTotal,
            ];

            $cupCount += $quantity;
            $subtotal += $lineTotal;
        }

        $takeoutFee = $serviceType === Reservation::SERVICE_TAKE_OUT
            ? round($this->takeoutFeePerCup() * $cupCount, 2)
            : 0.0;

        return [
            'items' => $items,
            'cup_count' => $cupCount,
            'subtotal' => round($subtotal, 2),
            'takeout_fee' => $takeoutFee,
            'total' => round($subtotal + $takeoutFee, 2),
        ];
    }

    /**
     * Creates a reservation and its lines in one transaction.
     */
    public function create(array $data): Reservation
    {
        $serviceType = in_array($data['service_type'] ?? null, Reservation::SERVICE_TYPES, true)
            ? $data['service_type']
            : Reservation::SERVICE_DINE_IN;

        $quote = $this->quote($data['items'] ?? [], $serviceType);

        return DB::transaction(function () use ($data, $serviceType, $quote) {
            $reservation = Reservation::create([
                'reference' => Reservation::generateReference(),
                'user_id' => $data['user_id'] ?? null,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'] ?? null,
                'customer_contact' => $data['customer_contact'] ?? null,
                'branch' => $data['branch'] ?? 'kotapark',
                'service_type' => $serviceType,
                'status' => Reservation::STATUS_PENDING,
                'cup_count' => $quote['cup_count'],
                'subtotal' => $quote['subtotal'],
                'takeout_fee' => $quote['takeout_fee'],
                'total' => $quote['total'],
                'payment_method' => $data['payment_method'] ?? null,
                'payment_status' => $data['payment_status'] ?? 'unpaid',
                'source' => $data['source'] ?? 'web',
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($quote['items'] as $item) {
                $reservation->items()->create($item);
            }

            return $reservation->load('items');
        });
    }

    /**
     * Moves a reservation along the counter workflow, stamping the matching
     * timestamp and pushing to the customer's devices when it becomes ready.
     *
     * Transitions are validated against Reservation::ALLOWED_TRANSITIONS so a
     * completed order cannot be quietly reopened, or a cancelled one served.
     */
    public function transition(Reservation $reservation, string $status): Reservation
    {
        if (! in_array($status, Reservation::STATUSES, true)) {
            throw ValidationException::withMessages(['status' => 'Unknown reservation status.']);
        }

        if ($reservation->status === $status) {
            return $reservation;
        }

        if (! $reservation->canTransitionTo($status)) {
            throw ValidationException::withMessages([
                'status' => "A {$reservation->status} reservation cannot be moved to {$status}.",
            ]);
        }

        $reservation->status = $status;

        match ($status) {
            Reservation::STATUS_READY => $reservation->ready_at = now(),
            Reservation::STATUS_COMPLETED => $reservation->completed_at = now(),
            Reservation::STATUS_CANCELLED => $reservation->cancelled_at = now(),
            default => null,
        };

        $reservation->save();

        if ($status === Reservation::STATUS_READY) {
            $this->notifyReady($reservation);
        }

        return $reservation;
    }

    /**
     * Records how the customer paid at the counter. This is a record of a
     * completed hand-to-hand payment, not a gateway charge.
     */
    public function recordPayment(Reservation $reservation, string $method, ?User $actor = null): Reservation
    {
        if (! in_array($method, Reservation::PAYMENT_METHODS, true)) {
            throw ValidationException::withMessages([
                'payment_method' => 'Payment must be cash, GCash or PayMaya.',
            ]);
        }

        if ($reservation->status === Reservation::STATUS_CANCELLED) {
            throw ValidationException::withMessages([
                'payment_method' => 'A cancelled reservation cannot be paid.',
            ]);
        }

        $reservation->forceFill([
            'payment_method' => $method,
            'payment_status' => 'paid',
            'paid_by' => $actor?->id,
            'paid_at' => now(),
        ])->save();

        return $reservation;
    }

    private function notifyReady(Reservation $reservation): void
    {
        $tokens = DeviceToken::query()
            ->where('reservation_reference', $reservation->reference)
            ->when($reservation->user_id, fn ($query) => $query->orWhere('user_id', $reservation->user_id))
            ->pluck('token')
            ->all();

        $pickup = $reservation->isTakeOut() ? 'take-out' : 'dine-in';

        $this->push->send(
            $tokens,
            'Your order is ready',
            "Reservation {$reservation->reference} is ready for pick up at the counter ({$pickup}).",
            [
                'reference' => $reservation->reference,
                'status' => $reservation->status,
                'type' => 'reservation_ready',
            ]
        );
    }

    public function takeoutFeePerCup(): float
    {
        return (float) config('queenscup.takeout_fee_per_cup', 5.00);
    }
}
