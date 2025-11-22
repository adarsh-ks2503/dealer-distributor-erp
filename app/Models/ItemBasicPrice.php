<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemBasicPrice extends Model
{
    protected $guarded = [];

    protected $casts = [
        'status' => 'string',
        'type' => 'string',
        'approval_date' => 'datetime', // Cast to Carbon instance
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function itemName()
    {
        return $this->belongsTo(Item::class, 'item');
    }

    public function stateName()
    {
        return $this->belongsTo(State::class, 'region');
    }
}
