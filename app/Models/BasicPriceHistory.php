<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BasicPriceHistory extends Model
{
    protected $guarded = [];

    public function itemName()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function stateName()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

}
