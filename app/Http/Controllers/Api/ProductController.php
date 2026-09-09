<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * The public drinks catalogue, read from the same inventories table the admin
 * panel manages, so the Android menu never drifts from the counter's stock.
 */
class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $products = Inventory::query()
            ->when($request->query('category'), fn ($query, $category) => $query->where('category', $category))
            ->when($request->boolean('in_stock'), fn ($query) => $query->where('stock', '>', 0))
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->map(fn (Inventory $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category,
                'regular_price' => (float) $product->regular_price,
                'large_price' => (float) $product->large_price,
                'stock' => (int) $product->stock,
                'available' => $product->stock > 0,
                'description' => $product->description,
                'image_url' => $product->image_path
                    ? asset('storage/'.ltrim($product->image_path, '/'))
                    : null,
            ]);

        return response()->json([
            'data' => $products,
            'categories' => $products->pluck('category')->filter()->unique()->values(),
            'takeout_fee_per_cup' => (float) config('queenscup.takeout_fee_per_cup', 5.00),
        ]);
    }
}
