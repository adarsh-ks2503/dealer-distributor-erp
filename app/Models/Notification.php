<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['type', 'data', 'read_at'];

    // Automatically cast the 'data' column as an array (since it's stored as JSON in the DB)
    protected $casts = [
        'data' => 'array',
    ];

    // Define the polymorphic relationship back to the notifiable entity (User or AppUserManagement)
    public function notifiable()
    {
        return $this->morphTo();
    }
}
