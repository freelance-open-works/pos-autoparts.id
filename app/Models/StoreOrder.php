<?php

namespace App\Models;

use App\Actions\StoreOrderAction;
use App\Models\Default\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;

class StoreOrder extends Model
{
    const STATUS_DRAFT = 'draft';
    const STATUS_PROCESS = 'proses';
    const STATUS_SUBMIT = 'submit';
    const STATUS_DONE = 'selesai';

    protected $fillable = [
        'customer_id',
        'so_code',
        'so_date',
        'type',
        'status',
        'amount_cost',
        'address',
        'note',
    ];

    protected $appends = ['allow_change'];

    protected static function booted(): void
    {
        static::creating(function (StoreOrder $model) {
            $model->so_date = $model->so_date == null ? now()->format('Y-m-d') : Carbon::parse($model->so_date)->format('Y-m-d');
            $model->so_code = StoreOrderAction::generate_code($model->so_date);
        });
    }

    public function items()
    {
        return $this->hasMany(StoreOrderItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function allowChange(): Attribute
    {
        return Attribute::make(get: fn() => $this->status !== self::STATUS_SUBMIT);
    }
}
