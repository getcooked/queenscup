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
            // pos = rung up at the till, reservation = ordered from the app or
            // the web and settled at the counter. Omit for both.
            'source' => ['nullable', Rule::in(['pos', 'reservation'])],
        ]);

        $source = $filters['source'] ?? null;

        $sales = Reservation::with(['items', 'paidBy'])
            ->where('payment_status', 'paid')
            ->where('status', '!=', Reservation::STATUS_CANCELLED)
            ->when($source === 'pos', fn ($query) => $query->where('source', 'pos'))
            ->when($source === 'reservation', fn ($query) => $query->where('source', '!=', 'pos'))
            // A till sale is settled the moment it completes; a reservation is
            // settled when it is paid for, so rank both by when money moved.
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->whereDate('paid_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->whereDate('paid_at', '<=', $to))
            ->orderByDesc('paid_at')
            ->limit(500)
            ->get();

        return response()->json([
            'data' => $sales->map(fn (Reservation $sale) => array_merge($this->present($sale), [
                'cashier' => $sale->paidBy?->name ?? 'Counter',
                'completed_at' => optional($sale->paid_at ?? $sale->completed_at)->toIso8601String(),
                'channel' => $sale->source === 'pos' ? 'Point of Sale' : 'Reservation',
            ]))->all(),
            'totals' => [
                'count' => $sales->count(),
                'revenue' => round((float) $sales->sum('total'), 2),
                'cups' => (int) $sales->sum(fn (Reservation $sale) => $sale->items->sum('quantity')),
                'pos' => round((float) $sales->where('source', 'pos')->sum('total'), 2),
                'reservation' => round((float) $sales->where('source', '!=', 'pos')->sum('total'), 2),
            ],
        ]);
    }

    /**
     * Real sales for the dashboard: everything actually paid for, whether it
     * was rung up at the till or reserved in the app and settled at the
     * counter. Cancelled orders never count.
     *
     * The shape mirrors the browser's old local-storage order records so the
     * dashboard's existing charts can consume it unchanged.
     */
    public function dashboardSales(Request $request): JsonResponse
    {
        $since = now()->subDays(30)->startOfDay();

        $sales = Reservation::with(['items.inventory'])
            ->where('payment_status', 'paid')
            ->where('status', '!=', Reservation::STATUS_CANCELLED)
            ->where(function ($query) use ($since) {
                $query->where('paid_at', '>=', $since)->orWhere('created_at', '>=', $since);
            })
            ->orderByDesc('paid_at')
            ->limit(1000)
            ->get();

        return response()->json([
            'data' => $sales->map(function (Reservation $sale) {
                $stamp = $sale->paid_at ?? $sale->completed_at ?? $sale->created_at;

                return [
                    'id' => $sale->reference,
                    'at' => optional($stamp)->toIso8601String(),
                    'customer' => $sale->customer_name,
                    'branch' => $sale->branch,
                    'total' => (float) $sale->total,
                    'status' => $sale->status,
                    'paymentStatus' => $sale->payment_status,
                    'payment' => strtoupper((string) $sale->payment_method),
                    'source' => $sale->source,
                    'items' => $sale->items->map(fn ($item) => [
                        'id' => $item->inventory_id,
                        'name' => $item->name,
                        // Items keep the name they were sold under; the
                        // category still lives on the product record.
                        'category' => $item->inventory->category ?? 'Uncategorized',
                        'qty' => (int) $item->quantity,
                        'price' => (float) $item->unit_price,
                    ])->all(),
                ];
            })->all(),
        ]);
    }

    /**
     * Counts for the sidebar badges.
     *
     * Deliberately tiny: the sidebar polls this on every panel page, and it
     * only ever needs two numbers. Reading the whole order list to count it
     * in the browser is what left the badge showing a stale zero, since the
     * counter's real orders live here rather than in local storage.
     */
    public function counts(Request $request): JsonResponse
    {
        $branch = $request->query('branch');

        $scoped = fn () => Reservation::query()
            ->when($branch, fn ($query) => $query->where('branch', $branch));

        return response()->json([
            // Deliberately the same two the Orders screen counts, so the
            // sidebar badge and that page can never disagree.
            'active' => $scoped()->whereIn('status', [
                Reservation::STATUS_PENDING,
                Reservation::STATUS_PREPARING,
            ])->count(),

            'cash_pending' => $scoped()
                ->where('payment_status', '!=', 'paid')
                ->whereNotIn('status', [Reservation::STATUS_CANCELLED, Reservation::STATUS_COMPLETED])
                ->count(),
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
