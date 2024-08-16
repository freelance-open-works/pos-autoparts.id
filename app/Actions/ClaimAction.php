<?php

namespace App\Actions;

use App\Models\Claim;
use App\Models\ProductStock;
use App\Models\ProductStockHistory;
use Exception;
use Illuminate\Support\Carbon;

class ClaimAction
{
    const PREFIX = '/CLAIM-ASI/';

    public static function generate_code($date)
    {
        $fallback = '-';
        $date = Carbon::parse($date);
        $purchase = Claim::orderBy('created_at', 'desc')
            ->whereYear('c_date', $date->format('Y'))
            ->first();

        $num = 1;
        if ($purchase !== null) {
            try {
                $lastnum = explode('/', $purchase->s_code);
                $lastnum = explode('-', $lastnum[0]);
                $num = is_numeric($lastnum[1])  ? $lastnum[1] + 1 : $fallback;
            } catch (Exception $e) {
                $num = $fallback;
            }
        }

        $code = formatNumZero($num)  . self::PREFIX  . $date->format('m/Y');
        return $code;
    }

    public static function update_stocks(Claim $claim)
    {
        foreach ($claim->items as $item) {
            // update stock
            $start_stock = 0;
            $stock = ProductStock::where('product_id', $item->product_id)->first();
            $start_stock = $stock->stock;
            $stock->update(['stock' => $stock->stock + $item->qty]);

            // record sale item
            $saleItem = $item->saleItem;
            $saleItem->update([
                'qty_return' => $saleItem->qty_return + $item->qty,
            ]);

            // check stock fifo 
            $itemCost = $item->saleItem->costs()->get();
            $need_qty = $item->qty;
            foreach ($itemCost as $iCost) {
                $sisa = $iCost->qty - $need_qty;
                if ($sisa == 0) {
                    $stock->fifos()->create([
                        'model' => Claim::class,
                        'model_id' => $item->id,
                        'product_id' => $item->product_id,
                        'stock' => $item->qty,
                        'cost' => $iCost->cost,
                    ]);
                    $iCost->delete();
                    break;
                }

                if ($sisa > 0) {
                    $iCost->update(['qty' => $sisa]);
                    $stock->fifos()->create([
                        'model' => Claim::class,
                        'model_id' => $item->id,
                        'product_id' => $item->product_id,
                        'stock' => $item->qty,
                        'cost' => $iCost->cost,
                    ]);
                    $need_qty -= $item->qty;
                }

                if ($sisa < 0) {
                    $iCost->delete();
                    $stock->fifos()->create([
                        'model' => Claim::class,
                        'model_id' => $item->id,
                        'product_id' => $item->product_id,
                        'stock' => $item->qty,
                        'cost' => $iCost->cost,
                    ]);
                    $need_qty -= $item->qty;
                }
            }

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
