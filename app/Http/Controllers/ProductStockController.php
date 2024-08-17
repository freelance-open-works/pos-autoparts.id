<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Http\Request;

class ProductStockController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with(['brand', 'stock'])
            ->leftJoin('product_stocks', 'products.id', '=', 'product_stocks.product_id');

        if ($request->q) {
            $query->where(function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->q}%")
                    ->orWhere('part_code', 'like', "%{$request->q}%");
            });
        }

        $query->orderBy('products.updated_at', 'desc');
        $query->orderBy('product_stocks.stock', 'desc');

        return inertia('ProductStock/Index', [
            'data' => $query->paginate(),
        ]);
    }
}
