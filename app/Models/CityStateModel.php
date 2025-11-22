<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityStateModel extends Model
{
    use HasFactory;

    protected $table = 'city_state';
    protected $fillable = [
        'city',
        'state'
    ];
    public $timestamps = true; 
}
