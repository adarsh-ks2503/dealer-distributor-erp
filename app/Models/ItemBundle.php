<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemBundle extends Model
{
    protected $guarded = [];
    public function itemName()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function sizeDetail()
    {
        return $this->belongsTo(ItemSize::class, 'size_id');
    }
    // use above as 
    // {{ $bundle->sizeDetail->size }} mm
}
