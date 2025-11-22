<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\State;
use App\Models\City;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(){
        $json = File::get(database_path('seeders/Indian_Cities_In_States_JSON.json'));
        $data = json_decode($json, true);

        foreach ($data as $stateName => $cities) {
            $state = State::where('state', $stateName)->first();

            if ($state) {
                foreach ($cities as $cityName) {
                    City::firstOrCreate([
                        'state_id' => $state->id,
                        'name' => $cityName
                    ]);
                }
            }
        }
    }
}
