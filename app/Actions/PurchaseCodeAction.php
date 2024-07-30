<?php

namespace App\Actions;

use App\Models\PurchaseOrder;
use Exception;
use Illuminate\Support\Facades\DB;

class PurchaseCodeAction
{
    const PREFIX = '/PO-ASI/';

    public static function run($date)
    {
        $fallback = 99999;
        $purchase = PurchaseOrder::where(DB::raw("DATE(po_date)"), $date)
            ->orderBy('created_at', 'desc')
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

        $code = self::format($num) . self::PREFIX . now()->format('m/Y');
        return $code;
    }

    private static function format($n)
    {
        $max = 4; // 1000

        $number = '';
        foreach (range(0, $max - strlen($n)) as $zero) {
            $number .= '0';
        }

        return $number . $n;
    }
}
