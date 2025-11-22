<?php

namespace App\Models;

// Changes by md raza start
use App\Models\Dealer;
use App\Models\Distributor;
use App\Models\DispatchItem;
use App\Models\DispatchAttachment;
use App\Models\Warehouse;
// Changes by md raza end

use Illuminate\Database\Eloquent\Model;

class Dispatch extends Model
{
    protected $guarded = [];
    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
    }

    protected $casts = [
        // 'dispatch_out_time' => 'time',
        'dispatch_date' => 'date', // Cast dispatch_date as a date
    ];
    public function getDispatchOutTimeAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value)->format('H:i') : null;
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
    public function dispatchItems()
    {
        return $this->hasMany(DispatchItem::class);
    }

    public function attachments(){
        return $this->hasMany(DispatchAttachment::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function recipientState()
    {
        return $this->belongsTo(State::class, 'recipient_state_id');
    }
    public function recipientCity()
    {
        return $this->belongsTo(City::class, 'recipient_city_id');
    }
    public function consigneeState()
    {
        return $this->belongsTo(State::class, 'consignee_state_id');
    }
    public function consigneeCity()
    {
        return $this->belongsTo(City::class, 'consignee_city_id');
    }
    public function items()
    {
        return $this->hasMany(DispatchItem::class);
    }
}
