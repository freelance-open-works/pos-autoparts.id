<?php

namespace App\Http\Controllers;

use App\Actions\ClaimAction;
use App\Models\Claim;
use App\Models\ClaimItem;
use App\Models\Default\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Response;

class ClaimController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Claim::query()->with(['sale', 'customer']);

        if ($request->q) {
            $query->where(function ($query) use ($request) {
                $query->where('c_code', 'like', "%{$request->q}%");
            });
        }

        $query->orderBy('created_at', 'desc');

        return inertia('Claim/Index', [
            'data' => $query->paginate(),
        ]);
    }

    public function create(): Response
    {
        return inertia('Claim/Form');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'customer_id' => 'required|exists:customers,id',
            'c_date' => 'required|date',
            'reason' => 'nullable|string',
            'status' => 'required|string',
            'items' => 'required|array',
            'items.*.sale_item_id' => 'required|exists:sale_items,id',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric',
        ]);

        DB::beginTransaction();

        $claim = Claim::create([
            'sale_id' => $request->sale_id,
            'customer_id' => $request->customer_id,
            'c_date' => $request->c_date,
            'reason' => $request->reason,
            'status' => $request->status,
        ]);

        $claim->items()->saveMany(collect($request->items)->mapInto(ClaimItem::class));
        // also save on sale item 
        // also return the stock is status submit

        DB::commit();

        return redirect()->route('claims.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed created']);
    }

    public function edit(Claim $claim): Response
    {
        return inertia('Claim/Form', [
            'claim' => $claim->load(['items.product.brand', 'items.saleItem', 'customer', 'sale']),
        ]);
    }

    public function show(Claim $claim): Response
    {
        return inertia('Claim/Show', [
            'claim' => $claim->load(['items.product.brand', 'items.saleItem', 'customer', 'sale']),
        ]);
    }

    public function update(Request $request, Claim $claim): RedirectResponse
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'customer_id' => 'required|exists:customers,id',
            'c_date' => 'required|date',
            'reason' => 'nullable|string',
            'status' => 'required|string',
            'items' => 'required|array',
            'items.*.sale_item_id' => 'required|exists:sale_items,id',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric',
        ]);

        DB::beginTransaction();

        if (in_array($claim->status, [Claim::STATUS_SUBMIT, Claim::STATUS_DONE]) && !in_array($request->status, [Claim::STATUS_SUBMIT, Claim::STATUS_DONE])) {
            return redirect()->route('claims.index')
                ->with('message', ['type' => 'error', 'message' => 'Sudah disubmit hanya boleh di selesai']);
        }

        $claim->update([
            'sale_id' => $request->sale_id,
            'customer_id' => $request->customer_id,
            'c_date' => $request->c_date,
            'reason' => $request->reason,
            'status' => $request->status,
        ]);

        $claim->items()->delete();
        $claim->items()->saveMany(collect($request->items)->mapInto(ClaimItem::class));

        DB::commit();

        return redirect()->route('claims.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed updated']);
    }

    public function destroy(Claim $claim): RedirectResponse
    {
        if (in_array($claim->status, [Claim::STATUS_SUBMIT, Claim::STATUS_DONE])) {
            return redirect()->route('claims.index')
                ->with('message', ['type' => 'error', 'message' => 'Sudah disubmit hanya boleh di selesai']);
        }

        $claim->delete();

        return redirect()->route('claims.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed deleted']);
    }

    public function patch(Request $request, Claim $claim)
    {
        if ($request->key == 'status') {
            if ($claim->status != Claim::STATUS_SUBMIT && $request->value == Claim::STATUS_SUBMIT) {
                ClaimAction::update_stocks($claim);
            }
        }

        $claim->update([
            $request->key => $request->value
        ]);

        return redirect()->route('claims.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed updated']);
    }

    public function print_invoice(Claim $claim)
    {
        $pdf = Pdf::loadView('print.claim', [
            'claim' => $claim->load(['customer', 'sale']),
            'items' => $claim->items()->with(['product.brand'])->get(),
            'setting' => new Setting(),
        ])->setPaper('a4');

        return $pdf->stream();
    }
}
