<?php

namespace App\Http\Controllers;

use App\Actions\PurchaseAction;
use App\Models\Default\Setting;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Response;

class PurchaseController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Purchase::with(['supplier', 'purchaseOrder']);

        if ($request->q) {
            $query->where(function ($query) use ($request) {
                $query->where('p_code', 'like', "%{$request->q}%")
                    ->orWhere('status', 'like', "%{$request->q}%");
            });
        }

        $query->orderBy('created_at', 'desc');

        return inertia('Purchase/Index', [
            'data' => $query->paginate(),
        ]);
    }

    public function create(): Response
    {
        return inertia('Purchase/Form', [
            'ppn_percent' => Setting::getByKey('ppn_percent'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'purchase_order_id' => [
                'required',
                'exists:purchase_orders,id',
                Rule::unique('purchases', 'purchase_order_id')
                    ->where(fn($q) => $q->where('deleted_at', null))
            ],
            'supplier_id' => 'required|exists:suppliers,id',
            'p_date' => 'required|date',
            'status' => 'required|string',
            'address' => 'nullable|string',
            'note' => 'nullable|string',
            'amount_cost' => 'required|numeric',
            'amount_discount' => 'required|numeric',
            'amount_net' => 'required|numeric',
            'amount_ppn' => 'required|numeric',
            'ppn_percent_applied' => 'required|numeric',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.cost' => 'required|numeric|min:1',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.subtotal' => 'required|numeric',
            'items.*.discount_percent_1' => 'required|numeric',
            'items.*.discount_percent_2' => 'required|numeric',
            'items.*.discount_total' => 'required|numeric',
            'items.*.subtotal_discount' => 'required|numeric',
            'items.*.subtotal_net' => 'required|numeric',
            'items.*.subtotal_ppn' => 'required|numeric',
        ]);

        DB::beginTransaction();
        $items = collect($request->items);
        $purchase = Purchase::create([
            'purchase_order_id' => $request->purchase_order_id,
            'supplier_id' => $request->supplier_id,
            'p_date' => $request->p_date,
            'status' => $request->status,
            'amount_cost' => $request->amount_cost,
            'amount_discount' => $request->amount_discount,
            'amount_net' => $request->amount_net,
            'amount_ppn' => $request->amount_ppn,
            'address' => $request->address,
            'note' => $request->note,
            'ppn_percent_applied' => $request->ppn_percent_applied,
        ]);

        $purchase->items()->saveMany($items->mapInto(PurchaseItem::class));

        DB::commit();

        return redirect()->route('purchases.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed created']);
    }

    public function edit(Purchase $purchase): Response
    {
        return inertia('Purchase/Form', [
            'purchase' => $purchase->load(['items.product.brand', 'supplier', 'purchaseOrder']),
            'ppn_percent' => Setting::getByKey('ppn_percent'),
        ]);
    }

    public function show(Purchase $purchase): Response
    {
        return inertia('Purchase/Show', [
            'purchase' => $purchase->load(['items.product.brand', 'supplier', 'purchaseOrder']),
            'ppn_percent' => Setting::getByKey('ppn_percent'),
        ]);
    }

    public function update(Request $request, Purchase $purchase): RedirectResponse
    {
        $request->validate([
            'purchase_order_id' => [
                'required',
                'exists:purchase_orders,id',
                Rule::unique('purchases', 'purchase_order_id')
                    ->where(fn($q) => $q->where('deleted_at', null))
                    ->ignore($purchase->id)
            ],
            'supplier_id' => 'required|exists:suppliers,id',
            'p_date' => 'required|date',
            'status' => 'required|string',
            'address' => 'nullable|string',
            'note' => 'nullable|string',
            'amount_cost' => 'required|numeric',
            'amount_discount' => 'required|numeric',
            'amount_net' => 'required|numeric',
            'amount_ppn' => 'required|numeric',
            'ppn_percent_applied' => 'required|numeric',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.cost' => 'required|numeric|min:1',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.subtotal' => 'required|numeric',
            'items.*.discount_percent_1' => 'required|numeric',
            'items.*.discount_percent_2' => 'required|numeric',
            'items.*.discount_total' => 'required|numeric',
            'items.*.subtotal_discount' => 'required|numeric',
            'items.*.subtotal_net' => 'required|numeric',
            'items.*.subtotal_ppn' => 'required|numeric',
        ]);

        DB::beginTransaction();
        $items = collect($request->items);

        $purchase->update([
            'purchase_order_id' => $request->purchase_order_id,
            'supplier_id' => $request->supplier_id,
            'p_date' => $request->p_date,
            'status' => $request->status,
            'amount_cost' => $request->amount_cost,
            'amount_discount' => $request->amount_discount,
            'amount_net' => $request->amount_net,
            'amount_ppn' => $request->amount_ppn,
            'address' => $request->address,
            'note' => $request->note,
            'ppn_percent_applied' => $request->ppn_percent_applied,
        ]);

        $purchase->items()->delete();
        $purchase->items()->saveMany($items->mapInto(PurchaseItem::class));

        DB::commit();

        return redirect()->route('purchases.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed updated']);
    }

    public function destroy(Purchase $purchase): RedirectResponse
    {
        $purchase->delete();

        return redirect()->route('purchases.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed deleted']);
    }

    public function patch(Request $request, Purchase $purchase)
    {
        // if key is status and it submit update stock to up
        if ($request->key == 'status' && $request->value == Purchase::STATUS_SUBMIT) {
            PurchaseAction::update_stocks($purchase);
        }

        $purchase->update([
            $request->key => $request->value
        ]);

        return redirect()->route('purchases.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed updated']);
    }

    public function print(Purchase $purchase)
    {
        $pdf = Pdf::loadView('print.purchase', [
            'purchase' => $purchase->load(['creator', 'purchaseOrder']),
            'items' => $purchase->items()->with(['product.brand'])->get(),
            'setting' => new Setting(),
        ])->setPaper('a4');

        return $pdf->stream();
    }
}
