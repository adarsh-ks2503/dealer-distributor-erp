<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchAttachment extends Model
{
    protected $guarded = [];
    public function dispatch()
    {
        return $this->belongsTo(Dispatch::class, 'dispatch_id');
    }
}
