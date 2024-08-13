<?php

namespace App\Actions;

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
        $purchase = PurchaseOrder::orderBy('created_at', 'desc')->first();

        $num = 1;
        if ($purchase !== null) {
            try {
                $lastnum = explode('/', $purchase->po_code);
                $num = is_numeric($lastnum[0])  ? $lastnum[0] + 1 : $fallback;
            } catch (Exception $e) {
                $num = $fallback;
            }
        }

        $code = formatNumZero($num) . self::PREFIX . now()->format('m/Y');
        return $code;
    }
}
