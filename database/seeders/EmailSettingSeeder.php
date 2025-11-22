<?php

namespace Database\Seeders;

use App\Models\EmailSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmailSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EmailSetting::create([
            'mailer' => 'smtp',
            'host' => 'smtp-relay.brevo.com',
            'port' => '587',
            'username' => '8c284a001@smtp-brevo.com',
            'key' => 'B3W0axv4zS2hdqHO', // Assuming `key` column in your table
            'from_address' => 'singhalsteel@gmail.com',
            'from_name' => 'Singhal Steel COMPANY',
        ]);
    }
}
