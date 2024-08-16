<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Product::with(['brand']);

        if ($request->q) {
            $query->where(function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->q}%")
                    ->orWhere('part_code', 'like', "%{$request->q}%");
            });
        }

        $query->orderBy('created_at', 'desc');

        return inertia('Product/Index', [
            'data' => $query->paginate(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'part_code' => 'required|string|unique:products,part_code',
            'type' => 'nullable|string',
            'discount' => 'nullable|numeric|max:100',
            'cost' => 'nullable|numeric',
            'price' => 'nullable|numeric',
            'brand_id' => 'required|exists:brands,id',
        ]);

        Product::create([
            'name' => $request->name,
            'part_code' => $request->part_code,
            'type' => $request->type,
            'discount' => $request->discount ?? 0,
            'cost' => $request->cost ?? 0,
            'price' => $request->price ?? 0,
            'brand_id' => $request->brand_id,
        ]);

        return redirect()->route('products.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed created']);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'part_code' => 'required|string|unique:products,part_code,' . $product->id,
            'type' => 'nullable|string',
            'discount' => 'nullable|numeric|max:100',
            'cost' => 'nullable|numeric',
            'price' => 'nullable|numeric',
            'brand_id' => 'required|exists:brands,id',
        ]);

        $product->fill([
            'name' => $request->name,
            'part_code' => $request->part_code,
            'type' => $request->type,
            'discount' => $request->discount ?? 0,
            'cost' => $request->cost ?? 0,
            'price' => $request->price ?? 0,
            'brand_id' => $request->brand_id,
        ]);

        $product->save();

        return redirect()->route('products.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed updated']);
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed deleted']);
    }
}
