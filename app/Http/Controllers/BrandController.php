<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class BrandController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Brand::query();

        if ($request->q) {
            $query->where('name', 'like', "%{$request->q}%");
        }

        $query->orderBy('created_at', 'desc');

        return inertia('Brand/Index', [
            'data' => $query->paginate(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Brand::create([
            'name' => $request->name
        ]);

        return redirect()->route('brands.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed created']);
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $brand->fill([
            'name' => $request->name,
        ]);

        $brand->save();

        return redirect()->route('brands.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed updated']);
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        $brand->delete();

        return redirect()->route('brands.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed deleted']);
    }
}
