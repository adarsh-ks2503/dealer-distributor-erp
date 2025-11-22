<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchItem extends Model
{
    protected $guarded = [];

    public function dispatch()
    {
        return $this->belongsTo(Dispatch::class, 'dispatch_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function size()
    {
        return $this->belongsTo(ItemSize::class, 'size_id');
    }

    public function item(){
        return $this->belongsTo(Item::class, 'item_id');
    }
    public function allocation()
    {
        return $this->belongsTo(OrderAllocation::class, 'allocation_id');
    }
}

