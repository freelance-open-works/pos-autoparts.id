<?php

namespace App\Http\Controllers;

use App\Models\Default\Setting;
use App\Models\Sale;
use App\Services\PdfMergerService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SaleDeliveryController extends Controller
{
    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'expedition_id' => 'required|exists:expeditions,id',
            'sd_date' => 'required|date',
            'qty' => 'nullable|numeric',
            'qty_unit' => 'nullable|string',
            'volume' => 'nullable|numeric',
            'volume_unit' => 'nullable|string',
            'note' => 'nullable|string',
            'note_manual' => 'nullable|string',
            'service' => 'nullable|string',
        ]);

        $sale->delivery()->updateOrCreate([
            'sale_id' => $sale->id,
        ], [
            'expedition_id' => $request->expedition_id,
            'sd_date' => $request->sd_date,
            'qty' => $request->qty,
            'qty_unit' => $request->qty_unit,
            'volume' => $request->volume,
            'volume_unit' => $request->volume_unit,
            'note' => $request->note,
            'note_manual' => $request->note_manual,
            'service' => $request->service,
        ]);

        return redirect()->route('sales.index')
            ->with('message', ['type' => 'success', 'message' => 'Item has beed created']);
    }

    public function print(Sale $sale, PdfMergerService $pdfMergerService)
    {
        $data = [
            'sale' => $sale->load(['creator', 'purchase']),
            'delivery' => $sale->delivery->load(['expedition']),
            'setting' => new Setting(),
        ];

        $pdf = Pdf::loadView('print.delivery', $data)->setPaper('a4');
        $pdf->save($sale->id . '.pdf', 'public');
        $pdf = Pdf::loadView('print.delivery-label', $data)->setPaper('a6');
        $pdf->save($sale->id . '-label.pdf', 'public');


        $pdfFiles = [
            storage_path('app/public/' . $sale->id . '.pdf'),
            storage_path('app/public/' . $sale->id . '-label.pdf'),
        ];

        $output = storage_path('app/public/' . $sale->id . '-merge.pdf');

        $pdfMergerService->merge($pdfFiles, $output);

        return response()->file($output);
    }
}
