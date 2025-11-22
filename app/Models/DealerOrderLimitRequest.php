<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealerOrderLimitRequest extends Model
{
    protected $guarded = [];
    
    public function dealer(){
        return $this->belongsTo(Dealer::class);
    }
}
