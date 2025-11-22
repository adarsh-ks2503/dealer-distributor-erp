<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DistributorTeamDealer extends Pivot
{
    protected $table = 'distributor_team_dealers'; // ← use actual table name

    protected $guarded = [];
}
