<?php

namespace App\Models;

use App\Models\Default\Model;

class SaleItemCost extends Model
{
    protected $fillable = [
        'sale_item_id',
        'qty',
        'cost',
    ];
}
