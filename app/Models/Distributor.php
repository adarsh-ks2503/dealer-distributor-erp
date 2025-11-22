<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DistributorContactPersonsDetail;

class Distributor extends Model
{
    protected $guarded = [];
    protected $table = 'distributors';

    public function contactPersons()
    {
        return $this->hasMany(DistributorContactPersonsDetail::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function dealer(){
        return $this->hasMany(Dealer::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'placed_by_distributor_id');
    }

    public function allocations()
{
    return $this->hasMany(OrderAllocation::class);
}
}
