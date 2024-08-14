<?php

namespace App\Http\Controllers;

use App\Actions\SaleCodeAction;
use App\Models\Default\Setting;
use App\Models\Sale;
use App\Models\SaleItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Response;

class SaleController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Sale::query()->with(['customer', 'delivery.expedition']);

        if ($request->q) {
            $query->where(function ($query) use ($request) {
                $query->where('s_code', 'like', "%{$request->q}%")
                    ->orWhere('status', 'like', "%{$request->q}%");
            });
        }

        $query->orderBy('created_at', 'desc');

        return inertia('Sale/Index', [
            'data' => $query->paginate(),
        ]);
    }

    public function create(): Response
    {
        return inertia('Sale/Form', [
            'ppn_percent' => Setting::getByKey('ppn_percent'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'purchase_id' => [
                'nullable',
                'exists:purchases,id',
                Rule::unique('sales', 'purchase_id')
                    ->where(fn($q) => $q->where('deleted_at', null))
            ],
            'customer_id' => 'required|exists:customers,id',
            's_date' => 'required|date',
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
            'items.*.price' => 'required|numeric|min:1',
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
        $sale = Sale::create([
            'purchase_id' => $request->purchase_id,
            'customer_id' => $request->customer_id,
            's_date' => $request->s_date,
            'status' => $request->status,
            'amount_cost' => $request->amount_cost,
            'amount_discount' => $request->amount_discount,
            'amount_net' => $request->amount_net,
            'amount_ppn' => $request->amount_ppn,
            'address' => $request->address,
            'note' => $request->note,
            'ppn_percent_applied' => $request->ppn_percent_applied,
        ]);

        $sale->items()->saveMany($items->mapInto(SaleItem::class));

        DB::commit();

        return redirect()->route('sales.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed created']);
    }

    public function show(Sale $sale): Response
    {
        return inertia('Sale/Show', [
            'sale' => $sale->load(['items.product.brand', 'customer', 'purchase']),
            'ppn_percent' => Setting::getByKey('ppn_percent'),
        ]);
    }

    public function edit(Sale $sale): Response
    {
        return inertia('Sale/Form', [
            'sale' => $sale->load(['items.product.brand', 'customer', 'purchase']),
            'ppn_percent' => Setting::getByKey('ppn_percent'),
        ]);
    }

    public function update(Request $request, Sale $sale): RedirectResponse
    {
        $request->validate([
            'purchase_id' => [
                'nullable',
                'exists:purchases,id',
                Rule::unique('sales', 'purchase_id')
                    ->where(fn($q) => $q->where('deleted_at', null))
                    ->ignore($sale->id)
            ],
            'customer_id' => 'required|exists:customers,id',
            's_date' => 'required|date',
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
            'items.*.price' => 'required|numeric|min:1',
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

        $sale->update([
            'purchase_id' => $request->purchase_id,
            'customer_id' => $request->customer_id,
            's_date' => $request->s_date,
            'status' => $request->status,
            'amount_cost' => $request->amount_cost,
            'amount_discount' => $request->amount_discount,
            'amount_net' => $request->amount_net,
            'amount_ppn' => $request->amount_ppn,
            'address' => $request->address,
            'note' => $request->note,
            'ppn_percent_applied' => $request->ppn_percent_applied,
        ]);

        $sale->items()->delete();
        $sale->items()->saveMany(collect($request->items)->mapInto(SaleItem::class));

        DB::commit();

        return redirect()->route('sales.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed updated']);
    }

    public function destroy(Sale $sale): RedirectResponse
    {
        $sale->delete();

        return redirect()->route('sales.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed deleted']);
    }

    public function patch(Request $request, Sale $sale)
    {
        if ($request->key == 'status' && $request->value == Sale::STATUS_SUBMIT) {
            try {
                SaleCodeAction::update_stocks($sale);
            } catch (Exception $e) {
                return redirect()->route('sales.index')
                    ->with('message', ['type' => 'error', 'message' => $e->getMessage()]);
            }
        }

        $sale->update([
            $request->key => $request->value
        ]);

        return redirect()->route('sales.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed updated']);
    }

    public function print_invoice(Sale $sale)
    {
        $pdf = Pdf::loadView('print.sale', [
            'sale' => $sale->load(['creator', 'purchase']),
            'items' => $sale->items()->with(['product.brand'])->get(),
            'setting' => new Setting(),
        ])->setPaper('a4', 'landscape');

        return $pdf->stream();
    }
}
