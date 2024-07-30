<?php

namespace App\Actions;

use App\Models\ProductStock;
use App\Models\ProductStockHistory;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Exception;
use Illuminate\Support\Facades\DB;

class PurchaseAction
{
    const PREFIX = '/P-ASI/';

    public static function generate_code($date)
    {
        $fallback = '-';
        $purchase = Purchase::where(DB::raw("DATE(p_date)"), $date)
            ->orderBy('created_at', 'desc')
            ->first();

        $num = 1;
        if ($purchase !== null) {
            try {
                $lastnum = explode('/', $purchase->p_code);
                $num = is_numeric($lastnum[0])  ? $lastnum[0] + 1 : $fallback;
            } catch (Exception $e) {
                $num = $fallback;
            }
        }

        $code = formatNumZero($num) . self::PREFIX . now()->format('m/Y');
        return $code;
    }

    public static function update_stocks(Purchase $purchase)
    {
        foreach ($purchase->items as $item) {
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
                'last' => $stock->stock + $item->qty,
            ]);
        }
    }

    public static function revert_stocks(Purchase $purchase)
    {
        foreach ($purchase->items as $item) {
            // update stock
            $start_stock = 0;
            $stock = ProductStock::where('product_id', $item->product_id)->first();
            if ($stock != null) {
                $start_stock = $stock->stock;
                $stock->update(['stock' => $stock->stock - $item->qty]);
            }

            // delete stock fifo 
            $stockFifo = $stock->fifos()->where([
                'model' => PurchaseItem::class,
                'model_id' => $item->id,
                'product_id' => $item->product_id,
            ])
                ->first();

            if ($stockFifo && $stockFifo->stock != $item->qty) {
                throw new Exception('tidak dapat menghapus transaksi karena stock sudah digunakan');
            }

            $stockFifo?->delete();

            // create stock history
            ProductStockHistory::create([
                'product_id' => $item->product_id,
                'product_stock_id' => $stock->id,
                'start' => $start_stock,
                'in' => 0,
                'out' => $item->qty,
                'last' => $stock->stock - $item->qty,
            ]);
        }
    }
}
