<?php

namespace App\Actions;

use App\Models\Sale;
use Exception;
use Illuminate\Support\Facades\DB;

class SaleCodeAction
{
    const PREFIX = 'ASI';

    public static function generate_code($date)
    {
        $fallback = '-';
        $purchase = Sale::where(DB::raw("DATE(s_date)"), $date)
            ->orderBy('created_at', 'desc')
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

        $code = self::PREFIX . '-' . formatNumZero($num)  . '/' .  now()->format('m/Y');
        return $code;
    }
}
