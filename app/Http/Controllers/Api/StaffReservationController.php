<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\User;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Counter-side endpoints: the queue, status changes and recording payment.
 * Guarded by Sanctum plus the staff check in the route definition.
 */
class StaffReservationController extends Controller
{
    public function __construct(private ReservationService $reservations)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::in(Reservation::STATUSES)],
            'branch' => ['nullable', 'string', 'max:40'],
            'active' => ['nullable', 'boolean'],
        ]);

        $reservations = Reservation::with('items')
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['branch'] ?? null, fn ($query, $branch) => $query->where('branch', $branch))
            ->when($filters['active'] ?? false, fn ($query) => $query->active())
            ->latest()
            ->limit(200)
            ->get()
            ->map(fn (Reservation $reservation) => $this->present($reservation));

        return response()->json(['data' => $reservations]);
    }

    public function updateStatus(Request $request, Reservation $reservation): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(Reservation::STATUSES)],
        ]);

        $this->reservations->transition($reservation, $data['status']);

        return response()->json($this->present($reservation->fresh('items')));
    }

    public function recordPayment(Request $request, Reservation $reservation): JsonResponse
    {
        $data = $request->validate([
            'payment_method' => ['required', Rule::in(Reservation::PAYMENT_METHODS)],
        ]);

        $this->reservations->recordPayment($reservation, $data['payment_method'], $this->actor($request));

        return response()->json($this->present($reservation->fresh('items')));
    }

    /**
     * The session guard hands the staff member over as a request attribute,
     * while a Sanctum token surfaces on the request itself. Accept either so
     * paid_by is recorded no matter which door the call came through.
     */
    private function actor(Request $request): ?User
    {
        $staff = $request->attributes->get('staff_user');

        return $staff instanceof User ? $staff : $request->user();
    }

    /**
     * Rings up a walk-in sale at the counter. The basket is priced server side
     * exactly like an app reservation, so a till cannot invent its own totals.
     */
    public function storeSale(Request $request): JsonResponse
    {
        $data = $request->validate([
            'service_type' => ['required', Rule::in(Reservation::SERVICE_TYPES)],
            'payment_method' => ['required', Rule::in(Reservation::PAYMENT_METHODS)],
            'customer_name' => ['nullable', 'string', 'max:120'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.inventory_id' => ['required', 'integer'],
            'items.*.size' => ['nullable', Rule::in(['regular', 'large'])],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $sale = $this->reservations->recordWalkInSale($data, $this->actor($request));

        return response()->json($this->present($sale), 201);
    }

    /**
     * Every walk-in sale rung up at the till, newest first, for the sales
     * report. Optional from/to bound it to a period the report is showing.
     */
    public function salesLog(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $sales = Reservation::with(['items', 'paidBy'])
            ->where('source', 'pos')
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->whereDate('completed_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->whereDate('completed_at', '<=', $to))
            ->orderByDesc('completed_at')
            ->limit(500)
            ->get();

        return response()->json([
            'data' => $sales->map(fn (Reservation $sale) => array_merge($this->present($sale), [
                'cashier' => $sale->paidBy?->name ?? 'Counter',
                'completed_at' => optional($sale->completed_at)->toIso8601String(),
            ]))->all(),
            'totals' => [
                'count' => $sales->count(),
                'revenue' => round((float) $sales->sum('total'), 2),
                'cups' => (int) $sales->sum(fn (Reservation $sale) => $sale->items->sum('quantity')),
            ],
        ]);
    }

    private function present(Reservation $reservation): array
    {
        return [
            'id' => $reservation->id,
            'reference' => $reservation->reference,
            'customer_name' => $reservation->customer_name,
            'customer_contact' => $reservation->customer_contact,
            'customer_email' => $reservation->customer_email,
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
            'source' => $reservation->source,
            'notes' => $reservation->notes,
            'placed_at' => optional($reservation->created_at)->toIso8601String(),
            'ready_at' => optional($reservation->ready_at)->toIso8601String(),
            'next_statuses' => Reservation::ALLOWED_TRANSITIONS[$reservation->status] ?? [],
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
