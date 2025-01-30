<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Http\Request;

class ProductStockController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with(['brand'])
            ->leftJoin('product_stocks', 'products.id', '=', 'product_stocks.product_id')
            ->where('product_stocks.stock', '!=', '0');

        if ($request->q) {
            $query->where(function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->q}%")
                    ->orWhere('part_code', 'like', "%{$request->q}%");
            });
        }

        $query->orderBy('product_stocks.stock', 'desc');

        return inertia('ProductStock/Index', [
            'data' => $query->paginate(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        // NOTE : from here is i dont care with the fifos system im created before 
        $request->validate(['stock' => ['required', 'numeric']]);

        $product->stock()->update(['stock' => $request->stock]);

        return redirect()->route('product-stocks.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed updated']);
    }
}
