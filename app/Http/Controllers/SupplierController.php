<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class SupplierController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Supplier::query();

        if ($request->q) {
            $query->where(function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->q}%")
                    ->orWhere('code', 'like', "%{$request->q}%");
            });
        }

        $query->orderBy('created_at', 'desc');

        return inertia('Supplier/Index', [
            'data' => $query->paginate(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:suppliers,code',
            'address' => 'nullable|string',
            'type' => 'nullable|string',
        ]);

        Supplier::create([
            'name' => $request->name,
            'code' => $request->code,
            'address' => $request->address,
            'type' => $request->type,
        ]);

        return redirect()->route('suppliers.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed created']);
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:suppliers,code' . $supplier->id,
            'address' => 'nullable|string',
            'type' => 'nullable|string',
        ]);

        $supplier->fill([
            'name' => $request->name,
            'code' => $request->code,
            'address' => $request->address,
            'type' => $request->type,
        ]);

        $supplier->save();

        return redirect()->route('suppliers.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed updated']);
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed deleted']);
    }
}
