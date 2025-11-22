<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GstSetting extends Model
{
    use SoftDeletes;

    protected $table = 'gst_settings';

    protected $fillable = [
        'gst_prefix',
        'percent',
    ];

    public $timestamps = true;
}
