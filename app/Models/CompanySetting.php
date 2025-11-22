<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    use HasFactory; 

    public $table = 'company_settings';

    protected $fillable =[
        'name',
        'email',
        'phone_number',
        'state',
        'city',
        'country',
        'address',
        'pincode',
        'gst_no',
        'pan',
        'tan',
        'threshold',
        'bank_name',
        'amount',
        'ac_number',
        'ifsc_code',
        'branch',
    ];
    public $timestamps = true; 
}
        