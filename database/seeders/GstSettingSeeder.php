<?php

namespace Database\Seeders;

use App\Models\GstSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GstSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gstSettings = [
            // [
            //     'gst_prefix' => 'GST 5%',
            //     'percent' => 5.00,
            // ],
            // [
            //     'gst_prefix' => 'GST 12%',
            //     'percent' => 12.00,
            // ],
            // [
            //     'gst_prefix' => 'GST 15%',
            //     'percent' => 15.00,
            // ],
            [
                'gst_prefix' => 'GST 18%',
                'percent' => 18.00,
            ],
        ];
        foreach ($gstSettings as $setting) {
            GstSetting::create($setting);
        }
    }
}
