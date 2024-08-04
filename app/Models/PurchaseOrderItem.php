<?php

namespace App\Models;

use App\Models\Default\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'qty',
        'cost',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }
}
