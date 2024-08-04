<?php

namespace App\Models;

use App\Actions\PurchaseOrderAction;
use App\Models\Default\Model;
use Illuminate\Support\Carbon;

class PurchaseOrder extends Model
{
    const STATUS_DRAFT = 'draft';
    const STATUS_PROCESS = 'proses';
    const STATUS_SUBMIT = 'submit';
    const STATUS_DONE = 'selesai';

    protected $fillable = [
        'supplier_id',
        'po_code',
        'po_date',
        'type',
        'status',
        'amount_cost',
        'address',
        'note',
    ];

    protected static function booted(): void
    {
        static::creating(function (PurchaseOrder $model) {
            $model->po_date = $model->po_date == null ? now()->format('Y-m-d') : Carbon::parse($model->po_date)->format('Y-m-d');
            $model->po_code = PurchaseOrderAction::generate_code($model->po_date);
        });
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class)->withTrashed();
    }
}
