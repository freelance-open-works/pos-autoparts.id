<?php

namespace App\Actions;

use App\Models\ProductStock;
use App\Models\ProductStockHistory;
use App\Models\Sale;
use App\Models\SaleItem;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SaleCodeAction
{
    const PREFIX = 'ASI';

    public static function generate_code($date)
    {
        $fallback = '-';
        $date = Carbon::parse($date);
        $sale = Sale::orderBy('created_at', 'desc')->first();

        $num = 1;
        if ($sale !== null) {
            try {
                $lastnum = explode('/', $sale->s_code);
                $lastnum = explode('-', $lastnum[0]);
                $num = is_numeric($lastnum[1])  ? $lastnum[1] + 1 : $fallback;
            } catch (Exception $e) {
                $num = $fallback;
            }
        }

        $code = self::PREFIX . '-' . formatNumZero($num)  . '/' .  now()->format('m/Y');
        return $code;
    }

    public static function update_stocks(Sale $sale)
    {
        foreach ($sale->items as $item) {
            // update stock
            $start_stock = 0;
            $stock = ProductStock::where('product_id', $item->product_id)->first();
            if ($stock != null) {
                if (($stock->stock - $item->qty) < 0) {
                    throw new Exception('stok barang tidak cukup');
                }

                $start_stock = $stock->stock;
                $stock->update(['stock' => $stock->stock - $item->qty]);
            } else {
                throw new Exception('stok barang tidak cukup');
            }

            // check stock fifo 
            $fifo = $stock->fifos()->where(['product_id' => $item->product_id])->orderBy('created_at', 'asc')->get();

            $need_qty = $item->qty;
            foreach ($fifo as $ff) {
                $sisa = $ff->stock - $need_qty;
                if ($sisa == 0) {
                    $item->costs()->create([
                        'qty' => $need_qty,
                        'cost' => $ff->cost,
                    ]);
                    $ff->delete();
                    break;
                }

                if ($sisa > 0) {
                    $item->costs()->create([
                        'qty' => $need_qty,
                        'cost' => $ff->cost,
                    ]);
                    $need_qty -= $ff->stock;
                    $ff->update(['qty' => $sisa]);
                }

                if ($sisa < 0) {
                    $item->costs()->create([
                        'qty' => $ff->stock,
                        'cost' => $ff->cost,
                    ]);
                    $need_qty -= $ff->stock;
                    $ff->delete();
                }
            }

            // create stock history
            ProductStockHistory::create([
                'product_id' => $item->product_id,
                'product_stock_id' => $stock->id,
                'start' => $start_stock,
                'in' => 0,
                'out' => $item->qty,
                'last' => $stock->stock,
            ]);
        }
    }
}
