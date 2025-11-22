<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailSetting extends Model
{
    use HasFactory;
    protected $table = 'email_settings';
    protected $fillable = [
        'mailer',
        'host',
        'port',
        'username',
        'key',
        'from_address',
        'from_name',
    ];

    public $timestamps = true;
}
