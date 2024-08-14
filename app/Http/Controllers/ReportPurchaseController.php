<?php

namespace App\Http\Controllers;

use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

class ReportPurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseItem::query()->with(['purchase.supplier', 'purchase.creator', 'purchase.purchaseOrder', 'product']);

        if ($request->q) {
            $query->whereHas('purchase', function ($query) use ($request) {
                $query->where('p_code', 'like', "%{$request->q}%")
                    ->orWhere('status', 'like', "%{$request->q}%");
            });
        }

        if ($request->supplier_id != '') {
            $query->whereHas('purchase.supplier', function ($query) use ($request) {
                $query->where('supplier_id', $request->supplier_id);
            });
        }

        if ($request->startDate != '' && $request->endDate != '') {
            $query->whereHas('purchase', function ($query) use ($request) {
                $startDate = Carbon::parse($request->startDate);
                $endDate = Carbon::parse($request->endDate);

                $query->whereBetween(DB::raw('DATE(p_date)'), [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            });
        }

        $query->orderBy('created_at', 'desc');

        return inertia('PurchaseReport/Index', [
            'data' => $query->paginate(),
        ]);
    }

    public function export(Request $request)
    {
        $query = PurchaseItem::query()->with(['purchase.supplier', 'purchase.creator', 'product']);

        if ($request->q) {
            $query->whereHas('purchase', function ($query) use ($request) {
                $query->where('p_code', 'like', "%{$request->q}%")
                    ->orWhere('status', 'like', "%{$request->q}%");
            });
        }

        if ($request->supplier_id != '') {
            $query->whereHas('purchase.supplier', function ($query) use ($request) {
                $query->where('supplier_id', $request->supplier_id);
            });
        }

        if ($request->startDate != '' && $request->endDate != '') {
            $query->whereHas('purchase', function ($query) use ($request) {
                $startDate = Carbon::parse($request->startDate);
                $endDate = Carbon::parse($request->endDate);

                $query->whereBetween(DB::raw('DATE(p_date)'), [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            });
        }

        $query->orderBy('created_at', 'desc');

        $data = $query->get()->map(function ($item) {
            return [
                "Invoice Number" => $item->purchase->p_code,
                "Tanggal" => formatDate($item->purchase->p_date),
                "Supplier Kode" => $item->purchase->supplier->code,
                "Supplier Nama" => $item->purchase->supplier->name,
                "Salesman Kode" => $item->purchase->creator->fields?->code,
                "Salesman Nama" => $item->purchase->creator->name,
                "Part No" => $item->product->code,
                "Part Nama" => $item->product->name,
                "Qty" => formatIDR($item->qty),
                "Harga Jual" => formatIDR($item->cost),
                "Total Diskon" => formatIDR($item->discount_total),
                "DPP" => formatIDR($item->subtotal_net),
                "PPN" => formatIDR($item->subtotal_ppn),
                "Total" => formatIDR($item->subtotal_discount),
                "Status" => $item->purchase->status,
            ];
        });

        $date = now()->format('d-m-Y');

        return (new FastExcel($data))->download("laporan-pembelian-$date.xlsx");
    }
}
