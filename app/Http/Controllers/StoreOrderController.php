<?php

namespace App\Http\Controllers;

use App\Models\Default\Setting;
use App\Models\StoreOrder;
use App\Models\StoreOrderItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Response;

class StoreOrderController extends Controller
{
    public function index(Request $request): Response
    {
        $query = StoreOrder::with(['customer']);

        if ($request->q) {
            $query->where(function ($query) use ($request) {
                $query->where('so_code', 'like', "%{$request->q}%")
                    ->orWhere('status', 'like', "%{$request->q}%");
            });
        }

        $query->orderBy('created_at', 'desc');

        return inertia('StoreOrder/Index', [
            'data' => $query->paginate(),
        ]);
    }

    public function create(): Response
    {
        return inertia('StoreOrder/Form');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'so_date' => 'required|date',
            'type' => 'required|string',
            'status' => 'required|string',
            'address' => 'nullable|string',
            'note' => 'nullable|string',
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.cost' => 'required|numeric|min:1',
            'items.*.qty' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();
        $items = collect($request->items);
        $order = StoreOrder::create([
            'customer_id' => $request->customer_id,
            'so_date' => $request->so_date,
            'type' => $request->type,
            'status' => $request->status,
            'amount_cost' => $items->reduce(fn($_, $item) => $item['cost'] * $item['qty'], 0),
            'address' => $request->address,
            'note' => $request->note,
        ]);

        $order->items()->saveMany($items->mapInto(StoreOrderItem::class));

        DB::commit();

        return redirect()->route('store-orders.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed created']);
    }

    public function show(StoreOrder $storeOrder): Response
    {
        return inertia('StoreOrder/Show', [
            'storeOrder' => $storeOrder->load(['customer', 'items.product.brand']),
        ]);
    }

    public function edit(StoreOrder $storeOrder): Response
    {
        return inertia('StoreOrder/Form', [
            'storeOrder' => $storeOrder->load(['customer', 'items.product.brand']),
        ]);
    }

    public function update(Request $request, StoreOrder $storeOrder): RedirectResponse
    {
        $request->validate([
            'so_date' => 'required|date',
            'type' => 'required|string',
            'status' => 'required|string',
            'address' => 'nullable|string',
            'note' => 'nullable|string',
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.cost' => 'required|numeric|min:1',
            'items.*.qty' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();

        $items = collect($request->items);
        $storeOrder->update([
            'customer_id' => $request->customer_id,
            'so_date' => $request->so_date,
            'type' => $request->type,
            'status' => $request->status,
            'amount_cost' => $items->reduce(fn($_, $item) => $item['cost'] * $item['qty'], 0),
            'address' => $request->address,
            'note' => $request->note,
        ]);

        $storeOrder->items()->delete();
        $storeOrder->items()->saveMany($items->mapInto(StoreOrderItem::class));

        DB::commit();

        return redirect()->route('store-orders.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed updated']);
    }

    public function destroy(StoreOrder $storeOrder): RedirectResponse
    {
        $storeOrder->delete();

        return redirect()->route('store-orders.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed deleted']);
    }

    public function patch(Request $request, StoreOrder $storeOrder)
    {
        $storeOrder->update([
            $request->key => $request->value
        ]);

        return redirect()->route('store-orders.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed updated']);
    }

    public function print(StoreOrder $storeOrder)
    {
        $pdf = Pdf::loadView('print.store_order', [
            'store_order' => $storeOrder->load(['creator']),
            'items' => $storeOrder->items()->with(['product.brand'])->get(),
            'setting' => new Setting(),
        ])->setPaper('a4');

        return $pdf->stream();
    }
}
