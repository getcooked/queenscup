<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Customer-facing reservation endpoints. Shared by the web customer flow and
 * the Android app so both behave identically.
 */
class ReservationController extends Controller
{
    public function __construct(private ReservationService $reservations)
    {
    }

    /**
     * Prices a basket without saving it, so the customer sees the take-out
     * surcharge before committing.
     */
    public function quote(Request $request): JsonResponse
    {
        $data = $this->validateBasket($request);

        $quote = $this->reservations->quote($data['items'], $data['service_type']);

        return response()->json([
            'service_type' => $data['service_type'],
            'cup_count' => $quote['cup_count'],
            'subtotal' => $quote['subtotal'],
            'takeout_fee' => $quote['takeout_fee'],
            'takeout_fee_per_cup' => $this->reservations->takeoutFeePerCup(),
            'total' => $quote['total'],
            'items' => $quote['items'],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateBasket($request, [
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['nullable', 'email', 'max:180'],
            'customer_contact' => ['nullable', 'string', 'max:40'],
            'branch' => ['nullable', 'string', 'max:40'],
            'notes' => ['nullable', 'string', 'max:500'],
            'source' => ['nullable', Rule::in(['web', 'android'])],
            'device_token' => ['nullable', 'string', 'max:4096'],
        ]);

        $reservation = $this->reservations->create([
            'user_id' => $request->user()?->id,
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'] ?? null,
            'customer_contact' => $data['customer_contact'] ?? null,
            'branch' => $data['branch'] ?? 'kotapark',
            'service_type' => $data['service_type'],
            'items' => $data['items'],
            'notes' => $data['notes'] ?? null,
            'source' => $data['source'] ?? 'web',
        ]);

        // Bind the device straight away so this reservation can be pushed to
        // even when the customer never signed in.
        if (! empty($data['device_token'])) {
            DeviceToken::register($data['device_token'], [
                'user_id' => $request->user()?->id,
                'reservation_reference' => $reservation->reference,
                'platform' => $data['source'] ?? 'web',
            ]);
        }

        return response()->json($this->present($reservation), 201);
    }

    /**
     * Public tracking by reference code. Deliberately requires no login: the
     * reference is the secret, which is why it is randomly generated rather
     * than sequential.
     */
    public function show(string $reference): JsonResponse
    {
        $reservation = Reservation::with('items')
            ->where('reference', strtoupper(trim($reference)))
            ->first();

        if (! $reservation) {
            return response()->json(['message' => 'No reservation found with that code.'], 404);
        }

        return response()->json($this->present($reservation));
    }

    /**
     * Every reservation belonging to the signed-in customer.
     */
    public function mine(Request $request): JsonResponse
    {
        $reservations = Reservation::with('items')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (Reservation $reservation) => $this->present($reservation));

        return response()->json(['data' => $reservations]);
    }

    public function cancel(Request $request, string $reference): JsonResponse
    {
        $reservation = Reservation::where('reference', strtoupper(trim($reference)))->firstOrFail();

        // A customer may only call off an order the counter has not started.
        if ($reservation->status !== Reservation::STATUS_PENDING) {
            return response()->json([
                'message' => 'This reservation is already being prepared. Please talk to the counter.',
            ], 422);
        }

        $this->reservations->transition($reservation, Reservation::STATUS_CANCELLED);

        return response()->json($this->present($reservation->fresh('items')));
    }

    private function validateBasket(Request $request, array $extra = []): array
    {
        return $request->validate(array_merge([
            'service_type' => ['required', Rule::in(Reservation::SERVICE_TYPES)],
            'items' => ['required', 'array', 'min:1'],
            'items.*.inventory_id' => ['required', 'integer'],
            'items.*.size' => ['nullable', Rule::in(['regular', 'large'])],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:50'],
        ], $extra));
    }

    private function present(Reservation $reservation): array
    {
        return [
            'reference' => $reservation->reference,
            'customer_name' => $reservation->customer_name,
            'branch' => $reservation->branch,
            'service_type' => $reservation->service_type,
            'status' => $reservation->status,
            'status_label' => $reservation->statusLabel(),
            'cup_count' => $reservation->cup_count,
            'subtotal' => (float) $reservation->subtotal,
            'takeout_fee' => (float) $reservation->takeout_fee,
            'total' => (float) $reservation->total,
            'payment_method' => $reservation->payment_method,
            'payment_status' => $reservation->payment_status,
            'placed_at' => optional($reservation->created_at)->toIso8601String(),
            'ready_at' => optional($reservation->ready_at)->toIso8601String(),
            'items' => $reservation->items->map(fn ($item) => [
                'name' => $item->name,
                'size' => $item->size,
                'size_label' => $item->sizeLabel(),
                'unit_price' => (float) $item->unit_price,
                'quantity' => $item->quantity,
                'line_total' => (float) $item->line_total,
            ])->all(),
        ];
    }
}
