<?php

namespace App\Models;

use App\Models\Default\Model;

class ProductStockFifo extends Model
{
    protected $fillable = [
        'model',
        'model_id',
        'product_stock_id',
        'product_id',
        'stock',
        'cost',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }
}
