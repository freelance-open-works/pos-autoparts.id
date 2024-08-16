<?php

namespace App\Actions;

use App\Models\StoreOrder;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StoreOrderAction
{
    const PREFIX = '/ORD-ASI/';

    public static function generate_code($date)
    {
        $fallback = 99999;
        $date = Carbon::parse($date);
        $purchase = StoreOrder::orderBy('created_at', 'desc')
            ->whereYear('so_date', $date->format('Y'))
            ->first();

        $num = 1;
        if ($purchase !== null) {
            try {
                $lastnum = explode('/', $purchase->so_code);
                $num = is_numeric($lastnum[0])  ? $lastnum[0] + 1 : $fallback;
            } catch (Exception $e) {
                $num = $fallback;
            }
        }

        $code = formatNumZero($num) . self::PREFIX . $date->format('m/Y');
        return $code;
    }
}
