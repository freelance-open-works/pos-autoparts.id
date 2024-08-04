<?php

namespace App\Models;

use App\Models\Default\Model;

class ClaimItem extends Model
{
    protected $fillable = [
        'claim_id',
        'sale_item_id',
        'product_id',
        'qty',
    ];

    public function claim()
    {
        return $this->belongsTo(Claim::class)->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class)->withTrashed();
    }
}
