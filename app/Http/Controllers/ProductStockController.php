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
            ->leftJoin('product_stocks', 'products.id', '=', 'product_stocks.product_id');

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
}
