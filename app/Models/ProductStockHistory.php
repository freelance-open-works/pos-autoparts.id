<?php

namespace App\Models;

use App\Models\Default\Model;

class ProductStockHistory extends Model
{
    protected $fillable = [
        'product_id',
        'product_stock_id',
        'start',
        'in',
        'out',
        'last',
        'extras',
    ];
}
