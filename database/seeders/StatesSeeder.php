<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\State;
use Illuminate\Support\Facades\File;

class StatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        $json = File::get(database_path('seeders/Indian_Cities_In_States_JSON.json'));
        $data = json_decode($json, true);

        foreach ($data as $stateName => $cities) {
            State::firstOrCreate(['state' => $stateName]);
        }
    }
}
