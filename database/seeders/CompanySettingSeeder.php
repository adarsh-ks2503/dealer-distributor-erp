<?php

namespace Database\Seeders;

use App\Models\CompanySetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CompanySetting::create([
            'name' => 'SINGHAL STEEL & POWER PVT. LTD.',
            'email' => 'singhalraipur@yahoo.com',
            'phone_number' => '07712443238',
            'country' => 'India',
            'pincode' => '492015',
            'state' => 'Chhattisgarh',
            'city' => 'Raipur',
            'address' => 'Regd Office : 303 CENTURY TOWER, 45 SHAKESPEARE SARANI, KOLKATA-700017',
            'gst_no' => '00AAPFR6000D1ZB',
            'pan' => 'AADCS6988F',
            'tan' => 'ABCDE0002F',
            'threshold' => '10',
            'ac_number' => '00005001134',
            'ifsc_code' => '00AJCPJ0000A1ZZ',
            'bank_name' => 'SBI',
            'branch' => 'Raipur',
        ]);
    }
}
