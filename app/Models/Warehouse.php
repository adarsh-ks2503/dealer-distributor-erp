<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $guarded = [];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

     public function loadingPoints()
    {
        return $this->hasMany(LoadingPointMaster::class); // Warehouse has many LoadingPoints
    }
}
