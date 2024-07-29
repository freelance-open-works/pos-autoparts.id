<?php

namespace App\Models;

use App\Models\Default\Model;

class Customer extends Model
{
    const INCITY = 'incity';
    const OUTCITY = 'outcity';

    protected $fillable = [
        'name',
        'code',
        'address',
        'type',
    ];
}
