<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse;
use App\Models\State;
use App\Models\City;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample state and city IDs for seeding, ensure these exist in your database
        $state_id = State::first()->id;  // Or hardcode an existing state ID
        $city_id = City::first()->id;    // Or hardcode an existing city ID

        // You can loop to insert 4 sample warehouses
        Warehouse::create([
            'name' => 'Gerwani Unit',
            'mobile_no' => null,
            'pan_no' => null,
            'gst_no' => null,
            'state_id' => 16,
            'city_id' => 562,
            'pincode' => null,
            'address' => '13 KM MILESTONE,AMBIKAPUR ROAD,VILLAGE: GERWANI',
        ]);
    }
}
