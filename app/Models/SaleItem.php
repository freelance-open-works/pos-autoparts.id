<?php

namespace App\Models;

use App\Models\Default\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'product_id',
        'qty',
        'qty_return',
        'price',
        'subtotal',
        'discount_percent',
        'discount_amount',
        'discount_total',
        'subtotal_discount',
        'subtotal_net',
        'subtotal_ppn',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function costs()
    {
        return $this->hasMany(SaleItemCost::class);
    }
}
