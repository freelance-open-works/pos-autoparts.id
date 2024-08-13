<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasStatusWithAllowChange
{
    const STATUS_SUBMIT = 'submit';

    public function initializeHasStatusWithAllowChange()
    {
        $this->append('allow_change');
    }

    public function allowChange(): Attribute
    {
        return Attribute::make(get: fn() => $this->status !== self::STATUS_SUBMIT);
    }
}
