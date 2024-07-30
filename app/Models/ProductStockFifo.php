<?php

namespace App\Models;

use App\Models\Default\Model;

class ProductStockFifo extends Model
{
    protected $fillable = [
        'product_id',
        'product_stock_id',
        'stock',
        'cost',
    ];
}
