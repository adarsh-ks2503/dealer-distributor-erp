<?php

namespace App\Models;

use App\Models\Dealer;
use App\Models\Distributor;
use Illuminate\Database\Eloquent\Model;

class OrderAllocation extends Model
{
    protected $guarded = [];

    /* -------------------------------------------------
     *  Existing relationships (keep them)
     * ------------------------------------------------- */
    public function product()
    {
        return $this->belongsTo(Item::class);
    }

    public function size()
    {
        return $this->belongsTo(ItemSize::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function dispatches()
    {
        return $this->hasMany(Dispatch::class);
    }

    /* -------------------------------------------------
     *  1. Morphic relationship – you already use it
     * ------------------------------------------------- */
    public function allocatable()
    {
        return $this->morphTo(__FUNCTION__, 'allocated_to_type', 'allocated_to_id');
    }

    public function allocatedTo()
    {
        // __FUNCTION__ ka matlab hai ki function ka naam ('allocatedTo') hi relation ka naam hai
        return $this->morphTo(__FUNCTION__, 'allocated_to_type', 'allocated_to_id');
    }

    /* -------------------------------------------------
     *  2. Helper attribute – you already use it
     * ------------------------------------------------- */
    public function getAllocatedToAttribute()
    {
        if ($this->allocated_to_type === 'dealer') {
            return Dealer::find($this->allocated_to_id);
        }
        if ($this->allocated_to_type === 'distributor') {
            return Distributor::find($this->allocated_to_id);
        }
        return null;
    }

    /* -------------------------------------------------
     *  3. **REAL** relationships – ONLY for eager loading
     * ------------------------------------------------- */
    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'allocated_to_id');
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, 'allocated_to_id');
    }
}
