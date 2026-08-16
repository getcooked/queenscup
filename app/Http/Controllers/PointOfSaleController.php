<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\PointOfSale;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class PointOfSaleController extends Controller
{
    public function index(Request $request)
    {
        $query = PointOfSale::with('cashier')->latest();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($builder) use ($search) {
                $builder->where('receipt_no', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('item_name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        try {
            $sales = $query->paginate(10)->withQueryString();
            $totalSales = PointOfSale::sum('total_amount');
            $todaySales = PointOfSale::whereDate('created_at', now()->toDateString())->sum('total_amount');
            $todayTransactions = PointOfSale::whereDate('created_at', now()->toDateString())->count();
            $inventoryItems = Inventory::query()->orderBy('name')->get();
        } catch (QueryException $exception) {
            $sales = new LengthAwarePaginator([], 0, 10);
            $totalSales = 0;
            $todaySales = 0;
            $todayTransactions = 0;
            $inventoryItems = collect();

            return view('pos', compact('sales', 'totalSales', 'todaySales', 'todayTransactions', 'inventoryItems'))
                ->withErrors(['database' => 'We could not connect to the database. Please try again later.']);
        }

        return view('pos', compact('sales', 'totalSales', 'todaySales', 'todayTransactions', 'inventoryItems'));
    }

    public function create()
    {
        return view('point-of-sales.create', [
            'sale' => new PointOfSale([
                'receipt_no' => $this->generateReceiptNo(),
                'payment_method' => 'Cash',
                'payment_status' => 'paid',
                'order_status' => 'completed',
                'quantity' => 1,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['receipt_no'] = $data['receipt_no'] ?: $this->generateReceiptNo();
        $data['total_amount'] = $data['quantity'] * $data['unit_price'];
        $data['cashier_id'] = $request->session()->get('staff_user_id');

        try {
            PointOfSale::create($data);
        } catch (QueryException $exception) {
            return back()
                ->withInput()
                ->withErrors(['database' => 'We could not connect to the database. Please try again later.']);
        }

        return redirect()->route('point-of-sales.index')->with('success', 'POS transaction created.');
    }

    public function show(PointOfSale $pointOfSale)
    {
        $pointOfSale->load('cashier');

        return view('point-of-sales.show', compact('pointOfSale'));
    }

    public function edit(PointOfSale $pointOfSale)
    {
        return view('point-of-sales.edit', ['sale' => $pointOfSale]);
    }

    public function update(Request $request, PointOfSale $pointOfSale)
    {
        $data = $this->validatedData($request, $pointOfSale->id);
        $data['total_amount'] = $data['quantity'] * $data['unit_price'];

        try {
            $pointOfSale->update($data);
        } catch (QueryException $exception) {
            return back()
                ->withInput()
                ->withErrors(['database' => 'We could not connect to the database. Please try again later.']);
        }

        return redirect()->route('point-of-sales.index')->with('success', 'POS transaction updated.');
    }

    public function destroy(PointOfSale $pointOfSale)
    {
        try {
            $pointOfSale->delete();
        } catch (QueryException $exception) {
            return back()
                ->withErrors(['database' => 'We could not connect to the database. Please try again later.']);
        }

        return redirect()->route('point-of-sales.index')->with('success', 'POS transaction deleted.');
    }

    private function validatedData(Request $request, $saleId = null)
    {
        return $request->validate([
            'receipt_no' => ['nullable', 'string', 'max:50', 'unique:point_of_sales,receipt_no,' . $saleId],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'item_name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'size' => ['required', 'string', 'max:20'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:Cash,GCash QR,Maya QR'],
            'payment_status' => ['required', 'in:pending,paid,refunded'],
            'order_status' => ['required', 'in:pending,preparing,completed,cancelled'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function generateReceiptNo()
    {
        return 'POS-' . now()->format('YmdHis') . '-' . random_int(100, 999);
    }
}
