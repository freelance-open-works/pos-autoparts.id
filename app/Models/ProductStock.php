<?php

namespace App\Models;

use App\Models\Default\Model;

class ProductStock extends Model
{
    protected $fillable = [
        'product_id',
        'stock'
    ];

    public function fifo()
    {
        return $this->belongsTo(ProductStockFifo::class, 'product_stock_id');
    }
}
