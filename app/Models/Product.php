<?php

namespace App\Models;

use App\Models\Default\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'part_code',
        'type', //text
        'discount', //0
        'cost', //0
        'price', //0
        'brand_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            $product->stock()->create(['stock' => 0]);
        });
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class)->withTrashed();
    }

    public function stock()
    {
        return $this->hasOne(ProductStock::class);
    }
}
