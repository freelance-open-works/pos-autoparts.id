<?php

namespace App\Actions;

use App\Models\SaleDelivery;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SaleDeliveryAction
{
    const PREFIX = '/LOG-ASI/';

    public static function generate_code($date)
    {
        $fallback = '-';
        $date = Carbon::parse($date);
        $saled = SaleDelivery::orderBy('created_at', 'desc')->first();

        $num = 1;
        if ($saled !== null) {
            try {
                $lastnum = explode('/', $saled->sd_code);
                $num = is_numeric($lastnum[0])  ? $lastnum[0] + 1 : $fallback;
            } catch (Exception $e) {
                $num = $fallback;
            }
        }

        $code =  formatNumZero($num)  . self::PREFIX .  now()->format('m/Y');
        return $code;
    }
}
