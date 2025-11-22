<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $guarded = [];
    public function sizes()
    {
        return $this->hasMany(ItemSize::class, 'item');
    }

    public function basicPrices()
    {
        return $this->hasMany(ItemBasicPrice::class, 'item');
    }
}
