<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\City;

class State extends Model
{
    protected $guarded = [];
    public function basicPrices()
    {
        return $this->hasMany(ItemBasicPrice::class, 'state');
    }

    public function cities(){
        return $this->hasMany(City::class);
    }
    public function distributors()
    {
        return $this->hasMany(Distributor::class);
    }
}
