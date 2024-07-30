<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Customer::query();

        if ($request->q) {
            $query->where(function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->q}%")
                    ->orWhere('code', 'like', "%{$request->q}%");
            });
        }

        $query->orderBy('created_at', 'desc');

        return inertia('Customer/Index', [
            'data' => $query->paginate(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:customers,code',
            'address' => 'nullable|string',
            'type' => 'nullable|string',
        ]);

        Customer::create([
            'name' => $request->name,
            'code' => $request->code,
            'address' => $request->address,
            'type' => $request->type,
        ]);

        return redirect()->route('customers.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed created']);
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:customers,code,' . $customer->id,
            'address' => 'nullable|string',
            'type' => 'nullable|string',
        ]);

        $customer->fill([
            'name' => $request->name,
            'code' => $request->code,
            'address' => $request->address,
            'type' => $request->type,
        ]);

        $customer->save();

        return redirect()->route('customers.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed updated']);
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed deleted']);
    }
}
