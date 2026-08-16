<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventory::query()->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        try {
            $items = $query->paginate(10)->withQueryString();
            $totalItems = Inventory::count();
            $lowStock = Inventory::where('stock', '>', 0)->where('stock', '<=', 10)->count();
            $outOfStock = Inventory::where('stock', 0)->count();
        } catch (QueryException $exception) {
            $items = new LengthAwarePaginator([], 0, 10);
            $totalItems = 0;
            $lowStock = 0;
            $outOfStock = 0;

            return view('inventory', compact('items', 'totalItems', 'lowStock', 'outOfStock'))
                ->withErrors(['database' => 'We could not connect to the database. Please try again later.']);
        }

        return view('inventory', compact('items', 'totalItems', 'lowStock', 'outOfStock'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['image_path'] = $this->storeImage($request);

        try {
            Inventory::create($data);
        } catch (QueryException $exception) {
            return back()
                ->withInput()
                ->withErrors(['database' => 'We could not connect to the database. Please try again later.']);
        }

        return redirect('/inventory')->with('success', 'Inventory item added.');
    }

    public function update(Request $request, Inventory $inventory)
    {
        $data = $this->validatedData($request);

        if ($request->hasFile('image')) {
            if ($inventory->image_path) {
                Storage::disk('public')->delete($inventory->image_path);
            }
            $data['image_path'] = $this->storeImage($request);
        }

        try {
            $inventory->update($data);
        } catch (QueryException $exception) {
            return back()
                ->withInput()
                ->withErrors(['database' => 'We could not connect to the database. Please try again later.']);
        }

        return redirect('/inventory')->with('success', 'Inventory item updated.');
    }

    public function destroy(Inventory $inventory)
    {
        if ($inventory->image_path) {
            Storage::disk('public')->delete($inventory->image_path);
        }

        try {
            $inventory->delete();
        } catch (QueryException $exception) {
            return back()
                ->withErrors(['database' => 'We could not connect to the database. Please try again later.']);
        }

        return redirect('/inventory')->with('success', 'Inventory item deleted.');
    }

    private function validatedData(Request $request)
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'regular_price' => ['required', 'numeric', 'min:0'],
            'large_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
        ]);
    }

    private function storeImage(Request $request)
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        return $request->file('image')->store('inventory', 'public');
    }
}
