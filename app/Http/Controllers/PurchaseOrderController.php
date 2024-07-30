<?php

namespace App\Http\Controllers;

use App\Models\Default\Setting;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Response;

class PurchaseOrderController extends Controller
{
    public function index(Request $request): Response
    {
        $query = PurchaseOrder::with(['supplier']);

        if ($request->q) {
            $query->where(function ($query) use ($request) {
                $query->where('po_code', 'like', "%{$request->q}%")
                    ->orWhere('status', 'like', "%{$request->q}%");
            });
        }

        $query->orderBy('created_at', 'desc');

        return inertia('PurchaseOrder/Index', [
            'data' => $query->paginate(),
        ]);
    }

    public function create(): Response
    {
        return inertia('PurchaseOrder/Form');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'po_date' => 'required|date',
            'type' => 'required|string',
            'status' => 'required|string',
            'address' => 'nullable|string',
            'note' => 'nullable|string',
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.cost' => 'required|numeric|min:1',
            'items.*.qty' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();
        $items = collect($request->items);
        $purchaseOrder = PurchaseOrder::create([
            'supplier_id' => $request->supplier_id,
            'po_date' => $request->po_date,
            'type' => $request->type,
            'status' => $request->status,
            'amount_cost' => $items->reduce(fn ($_, $item) => $item['cost'] * $item['qty'], 0),
            'address' => $request->address,
            'note' => $request->note,
        ]);

        $purchaseOrder->items()->saveMany($items->mapInto(PurchaseOrderItem::class));

        DB::commit();

        return redirect()->route('purchase-orders.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed created']);
    }

    public function edit(PurchaseOrder $purchaseOrder): Response
    {
        return inertia('PurchaseOrder/Form', [
            'purchaseOrder' => $purchaseOrder->load(['supplier', 'items.product.brand']),
        ]);
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $request->validate([
            'po_date' => 'required|date',
            'type' => 'required|string',
            'status' => 'required|string',
            'address' => 'nullable|string',
            'note' => 'nullable|string',
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.cost' => 'required|numeric|min:1',
            'items.*.qty' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();

        $items = collect($request->items);
        $purchaseOrder->update([
            'supplier_id' => $request->supplier_id,
            'po_date' => $request->po_date,
            'type' => $request->type,
            'status' => $request->status,
            'amount_cost' => $items->reduce(fn ($_, $item) => $item['cost'] * $item['qty'], 0),
            'address' => $request->address,
            'note' => $request->note,
        ]);

        $purchaseOrder->items()->delete();
        $purchaseOrder->items()->saveMany($items->mapInto(PurchaseOrderItem::class));

        DB::commit();

        return redirect()->route('purchase-orders.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed updated']);
    }

    public function destroy(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $purchaseOrder->delete();

        return redirect()->route('purchase-orders.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed deleted']);
    }

    public function patch(Request $request, PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->update([
            $request->key => $request->value
        ]);

        return redirect()->route('purchase-orders.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed updated']);
    }

    public function print(PurchaseOrder $purchaseOrder)
    {
        $pdf = Pdf::loadView('print.purchase_order', [
            'purchase_order' => $purchaseOrder->load(['creator']),
            'items' => $purchaseOrder->items()->with(['product.brand'])->get(),
            'setting' => new Setting(),
        ])->setPaper('a4', 'landscape');

        return $pdf->stream();
    }
}
