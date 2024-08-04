<?php

namespace App\Models;

use App\Actions\PurchaseAction;
use App\Models\Default\Model;
use Illuminate\Support\Carbon;

class Purchase extends Model
{
    const STATUS_DRAFT = 'draft';
    const STATUS_PROCESS = 'proses';
    const STATUS_SUBMIT = 'submit';
    const STATUS_DONE = 'selesai';

    protected $fillable = [
        'purchase_order_id',
        'supplier_id',
        'p_code',
        'p_date',
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
        static::creating(function (Purchase $model) {
            $model->p_date = $model->p_date == null ? now()->format('Y-m-d') : Carbon::parse($model->p_date)->format('Y-m-d');
            $model->p_code = PurchaseAction::generate_code($model->p_date);
        });
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class)->withTrashed();
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
