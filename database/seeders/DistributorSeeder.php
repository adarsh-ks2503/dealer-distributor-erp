<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Distributor;
use App\Models\DistributorContactPersonsDetail;

class DistributorSeeder extends Seeder
{
    public function run(): void
    {
        $distributors = [
            [
                'name' => 'Om Steel Co.',
                'code' => 'OSC_001',
                'mobile_no' => '9876543210',
                'email' => 'omsteel@example.com',
                'gst_num' => 'GSTOSC001',
                'pan_num' => 'PANOSC001',
                'order_limit' => '100',
                'allowed_order_limit' => '100',
                'individual_allowed_order_limit' => '100',
                'remarks' => 'Top performing distributor.',
                'address' => 'Sector 1, Gujarat',
                'pincode' => '380001',
                'state_id' => 1,
                'city_id' => 1,
                'bank_name' => 'HDFC Bank',
                'account_holder_name' => 'Om Steel Co.',
                'ifsc_code' => 'HDFC0001234',
                'account_number' => '123456789012',
                'status' => 'Active',
                'created_by' => 'Super Admin',
                'contact_persons' => [
                    [
                        'name' => 'Rajesh Sharma',
                        'mobile_no' => '9123456780',
                        'email' => 'rajesh@omsteel.com',
                    ],
                    [
                        'name' => 'Meena Patel',
                        'mobile_no' => '9876501234',
                        'email' => 'meena@omsteel.com',
                    ]
                ]
            ],
            [
                'name' => 'Bharat Infra',
                'code' => 'BL_001',
                'mobile_no' => '9765432109',
                'email' => 'bharatinfra@example.com',
                'gst_num' => 'GSTBL001',
                'pan_num' => 'PANBL001',
                'order_limit' => '800',
                'allowed_order_limit' => '800',
                'individual_allowed_order_limit' => '800',
                'remarks' => null,
                'address' => 'Mumbai, Maharashtra',
                'pincode' => '400001',
                'state_id' => 2,
                'city_id' => 2,
                'bank_name' => 'State Bank of India',
                'account_holder_name' => 'Bharat Infra Ltd',
                'ifsc_code' => 'SBIN0005678',
                'account_number' => '998877665544',
                'status' => 'Active',
                'created_by' => 'Super Admin',
                'contact_persons' => [
                    [
                        'name' => 'Amit Desai',
                        'mobile_no' => '9988776655',
                        'email' => 'amit@bharatinfra.com',
                    ]
                ]
            ]
        ];

        foreach ($distributors as $data) {
            $contactPersons = $data['contact_persons'];
            unset($data['contact_persons']);

            $distributor = Distributor::create($data);

            foreach ($contactPersons as $person) {
                $distributor->contactPersons()->create($person);
            }
        }
    }
}
