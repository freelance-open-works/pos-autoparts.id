<?php

namespace App\Models;

use App\Models\Default\Model;

class ProductStock extends Model
{
    protected $fillable = [
        'product_id',
        'stock'
    ];

    public function fifos()
    {
        return $this->hasMany(ProductStockFifo::class, 'product_stock_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
