<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
// raza changes
use Illuminate\Database\Eloquent\SoftDeletes;
// raza changes

class AppUserManagement extends Model
{
    use HasApiTokens, Notifiable, SoftDeletes;
    protected $table = 'app_user_management';

    protected $fillable = [
        'name',
        'type',
        'code',
        'email',
        'mobile_no',
        'password',
        'status',
        'state_id',
        'city_id',
    ];

    /**
     * Get the app user's notifications.
     */
    // public function notifications()
    // {
    //     return $this->morphMany(Notification::class, 'notifiable');
    // }

    // /**
    //  * Get the app user's unread notifications.
    //  */
    // public function unreadNotifications()
    // {
    //     return $this->morphMany(Notification::class, 'notifiable')->whereNull('read_at');
    // }

    /**
     * Get the state associated with the user.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    /**
     * Get the city associated with the user.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
