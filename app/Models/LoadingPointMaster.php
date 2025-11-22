<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoadingPointMaster extends Model
{
    protected $guarded = [];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class); // LoadingPoint belongs to a single Warehouse
    }
}
