<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributorContactPersonsDetail extends Model
{
    protected $guarded = [];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}
