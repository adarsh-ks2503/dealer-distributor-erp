<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Dealer extends Model
{
    use Notifiable;
    protected $guarded = [];

    protected $casts = [
        'approval_time'=> 'datetime',
    ];

    public function contactPersons()
    {
        return $this->hasMany(DealerContactPersonsDetail::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function distributor(){
        return $this->belongsTo(Distributor::class);
    }

    public function distributorTeams()
    {
        return $this->belongsToMany(DistributorTeam::class, 'distributor_team_dealers')
                    ->using(DistributorTeamDealer::class)
                    ->withTimestamps();
    }

    public function orderLimitRequests()
    {
        return $this->hasMany(DealerOrderLimitRequest::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function allocations()
{
    return $this->hasMany(OrderAllocation::class);
}

}
