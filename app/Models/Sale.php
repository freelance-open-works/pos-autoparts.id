<?php

namespace App\Models;

use App\Actions\SaleCodeAction;
use App\Models\Default\Model;
use Illuminate\Support\Carbon;

class Sale extends Model
{
    const STATUS_DRAFT = 'draft';
    const STATUS_PROCESS = 'proses';
    const STATUS_SUBMIT = 'submit';
    const STATUS_DONE = 'selesai';

    protected $fillable = [
        'purchase_id',
        'customer_id',
        's_code',
        's_date',
        'status',
        'amount_cost',
        'amount_discount',
        'amount_net',
        'amount_ppn',
        'ppn_percent_applied',
        'address',
        'note',
    ];

    protected static function booted(): void
    {
        static::creating(function (Sale $model) {
            $model->s_date = $model->s_date == null ? now()->format('Y-m-d') : Carbon::parse($model->s_date)->format('Y-m-d');
            $model->s_code = SaleCodeAction::generate_code($model->s_date);
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function delivery()
    {
        return $this->hasOne(SaleDelivery::class);
    }
}
