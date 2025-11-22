<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemSizesHistory extends Model
{
    protected $guarded = [];
    public function itemName()
    {
        return $this->belongsTo(Item::class, 'item');
    }
    protected $casts = [
        'approval_time' => 'datetime', // Cast to Carbon instance
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
