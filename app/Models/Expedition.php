<?php

namespace App\Models;

use App\Models\Default\Model;

class Expedition extends Model
{
    protected $fillable = [
        'name',
        'address'
    ];
}
