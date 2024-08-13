<?php

namespace App\Models;

use App\Models\Default\Model;

class StoreOrderItem extends Model
{
    protected $fillable = [
        'store_order_id',
        'product_id',
        'qty',
        'cost',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }
}
