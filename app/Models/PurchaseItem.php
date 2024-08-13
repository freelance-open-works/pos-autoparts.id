<?php

namespace App\Models;

use App\Models\Default\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id',
        'product_id',
        'qty',
        'cost',
        'subtotal',
        'discount_percent_1',
        'discount_percent_2',
        'discount_total',
        'subtotal_discount',
        'subtotal_net',
        'subtotal_ppn',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class)->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }
}
