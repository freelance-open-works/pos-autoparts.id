<?php

namespace App\Models;

use App\Actions\SaleDeliveryAction;
use App\Models\Default\Model;
use Illuminate\Support\Carbon;

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

    protected static function booted(): void
    {
        static::creating(function (SaleDelivery $model) {
            $model->sd_date = $model->sd_date == null ? now()->format('Y-m-d') : Carbon::parse($model->sd_date)->format('Y-m-d');
            $model->sd_code = SaleDeliveryAction::generate_code($model->sd_date);
        });
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function expedition()
    {
        return $this->belongsTo(Expedition::class);
    }
}
