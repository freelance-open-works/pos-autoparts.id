<?php

namespace App\Http\Controllers;

use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Response;
use Rap2hpoutre\FastExcel\FastExcel;

class ReportSaleController extends Controller
{
    public function index(Request $request): Response
    {
        $query = SaleItem::query()->with(['sale.customer', 'sale.creator', 'product']);

        if ($request->q) {
            $query->whereHas('sale', function ($query) use ($request) {
                $query->where('s_code', 'like', "%{$request->q}%")
                    ->orWhere('status', 'like', "%{$request->q}%");
            });
        }

        if ($request->customer_id != '') {
            $query->whereHas('sale.customer', function ($query) use ($request) {
                $query->where('customer_id', $request->customer_id);
            });
        }

        if ($request->startDate != '' && $request->endDate != '') {
            $query->whereHas('sale', function ($query) use ($request) {
                $startDate = Carbon::parse($request->startDate);
                $endDate = Carbon::parse($request->endDate);

                $query->whereBetween(DB::raw('DATE(s_date)'), [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            });
        }

        $query->orderBy('created_at', 'desc');

        return inertia('SaleReport/Index', [
            'data' => $query->paginate(),
        ]);
    }

    public function export(Request $request)
    {
        $query = SaleItem::query()->with(['sale.customer', 'sale.creator', 'product']);

        if ($request->q) {
            $query->whereHas('sale', function ($query) use ($request) {
                $query->where('s_code', 'like', "%{$request->q}%")
                    ->orWhere('status', 'like', "%{$request->q}%");
            });
        }

        if ($request->customer_id != '') {
            $query->whereHas('sale.customer', function ($query) use ($request) {
                $query->where('customer_id', $request->customer_id);
            });
        }

        if ($request->startDate != '' && $request->endDate != '') {
            $query->whereHas('sale', function ($query) use ($request) {
                $startDate = Carbon::parse($request->startDate);
                $endDate = Carbon::parse($request->endDate);

                $query->whereBetween(DB::raw('DATE(s_date)'), [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            });
        }

        $query->orderBy('created_at', 'desc');

        $data = $query->get()->map(function ($item) {
            return [
                "Invoice Number" => $item->sale->s_code,
                "Tanggal" => formatDate($item->sale->s_date),
                "Customer Kode" => $item->sale->customer->code,
                "Customer Nama" => $item->sale->customer->name,
                "Salesman Kode" => $item->sale->creator->fields?->code,
                "Salesman Nama" => $item->sale->creator->name,
                "Part No" => $item->product->code,
                "Part Nama" => $item->product->name,
                "Qty" => formatIDR($item->qty),
                "Harga Jual" => formatIDR($item->price),
                "Total Diskon" => formatIDR($item->discount_total),
                "DPP" => formatIDR($item->subtotal_net),
                "PPN" => formatIDR($item->subtotal_ppn),
                "Total" => formatIDR($item->subtotal_discount),
                "Status" => $item->sale->status,
            ];
        });

        $date = now()->format('d-m-Y');

        return (new FastExcel($data))->download("laporan-penjualan-$date.xlsx");
    }
}
