<?php

namespace App\Actions;

use App\Models\ProductStock;
use App\Models\ProductStockHistory;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseOrder;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PurchaseOrderAction
{
    const PREFIX = '/PO-ASI/';

    public static function generate_code($date)
    {
        $fallback = 99999;
        $date = Carbon::parse($date);
        $purchase = PurchaseOrder::orderBy('created_at', 'desc')
            ->whereYear('po_date', $date->format('Y'))
            ->first();

        $num = 1;
        if ($purchase !== null) {
            try {
                $lastnum = explode('/', $purchase->po_code);
                $num = is_numeric($lastnum[0])  ? $lastnum[0] + 1 : $fallback;
            } catch (Exception $e) {
                $num = $fallback;
            }
        }

        $code = formatNumZero($num) . self::PREFIX . $date->format('m/Y');
        return $code;
    }

    public static function update_stocks(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->type != PurchaseOrder::type_pemesanan_stock) {
            return;
        }

        foreach ($purchaseOrder->items as $item) {
            // update stock
            $start_stock = 0;
            $stock = ProductStock::where('product_id', $item->product_id)->first();
            if ($stock == null) {
                ProductStock::create([
                    'product_id' => $item->product_id,
                    'stock' => $item->qty,
                ]);
                $start_stock = $item->qty;
            } else {
                $start_stock = $stock->stock;
                $stock->update(['stock' => $stock->stock + $item->qty]);
            }

            // create stock fifo 
            $stock->fifos()->create([
                'model' => PurchaseItem::class,
                'model_id' => $item->id,
                'product_id' => $item->product_id,
                'stock' => $item->qty,
                'cost' => $item->cost,
            ]);

            // create stock history
            ProductStockHistory::create([
                'product_id' => $item->product_id,
                'product_stock_id' => $stock->id,
                'start' => $start_stock,
                'in' => $item->qty,
                'out' => 0,
                'last' => $stock->stock,
            ]);
        }
    }
}
