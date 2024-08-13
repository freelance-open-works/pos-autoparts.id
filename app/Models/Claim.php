<?php

namespace App\Models;

use App\Actions\ClaimAction;
use App\Models\Default\Model;
use App\Models\Traits\HasStatusWithAllowChange;
use Illuminate\Support\Carbon;

class Claim extends Model
{
    use HasStatusWithAllowChange;

    const STATUS_DRAFT = 'draft';
    const STATUS_PROCESS = 'proses';
    const STATUS_SUBMIT = 'submit';
    const STATUS_DONE = 'selesai';

    protected $fillable = [
        'sale_id',
        'customer_id',
        'c_code',
        'c_date',
        'status',
        'reason',
    ];

    protected static function booted(): void
    {
        static::creating(function (Claim $model) {
            $model->c_date = $model->c_date == null ? now()->format('Y-m-d') : Carbon::parse($model->c_date)->format('Y-m-d');
            $model->c_code = ClaimAction::generate_code($model->c_date);
        });
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class)->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function items()
    {
        return $this->hasMany(ClaimItem::class);
    }
}
