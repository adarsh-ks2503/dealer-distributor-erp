<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributorTeam extends Model
{
    protected $guarded = [];

   // App/Models/DistributorTeam.php

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function dealers()
    {
        return $this->belongsToMany(Dealer::class, 'distributor_team_dealers')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    // protected $appends = ['active_dealer_count', 'active_total_order_limit'];

    // public function getActiveDealerCountAttribute()
    // {
    //     return $this->dealers()
    //         ->wherePivot('status', 'Active')
    //         ->where('dealers.status', 'active')  // ← Add this
    //         ->count();
    // }

    // public function getActiveTotalOrderLimitAttribute()
    // {
    //     $dealerLimit = $this->dealers()
    //         ->wherePivot('status', 'Active')
    //         ->where('dealers.status', 'active')  // ← Add this
    //         ->sum('dealers.order_limit');

    //     return $dealerLimit + ($this->distributor?->order_limit ?? 0);
    // }
}
