<?php

namespace App\Models;

use App\Models\Default\Model;

class SaleDelivery extends Model
{
    protected $fillable = [
        'sale_id',
        'expedition_id',
        'sd_code',
        'sd_date',
        'qty',
        'qty_unit',
        'volume',
        'volume_unit',
        'note',
        'service',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
