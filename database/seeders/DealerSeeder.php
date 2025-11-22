<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dealer;

class DealerSeeder extends Seeder
{
    public function run(): void
    {
        $dealers = [
            [
                'name' => 'Ashok Traders',
                'distributor_id' => null,
                'code' => 'DLR001',
                'mobile_no' => '9998877665',
                'email' => 'ashok@dealers.com',
                'gst_num' => 'GSTD001',
                'pan_num' => 'PAND001',
                'order_limit' => 150,
                'allowed_order_limit' => 150,
                'remarks' => 'Old wholesale dealer',
                'status' => 'Active',
                'type' => 'Wholesale',
                'address' => 'Sector 10, Raipur',
                'pincode' => '492001',
                'state_id' => 1,
                'city_id' => 1,
                'bank_name' => 'Axis Bank',
                'account_holder_name' => 'Ashok Traders',
                'ifsc_code' => 'AXIS0001234',
                'account_number' => '111122223333',
                'created_by' => 'Super Admin',
                'contact_persons' => [
                    ['name' => 'Ashok Kumar', 'mobile_no' => '9876501234', 'email' => 'ashok.kumar@dealers.com'],
                    ['name' => 'Pooja Mehta', 'mobile_no' => '9123456780', 'email' => 'pooja.mehta@dealers.com'],
                ],
            ],
            [
                'name' => 'Sanjay Steels',
                'distributor_id' => null,
                'code' => 'DLR002',
                'mobile_no' => '8887766554',
                'email' => 'sanjay@steels.com',
                'gst_num' => 'GSTD002',
                'pan_num' => 'PAND002',
                'order_limit' => 80,
                'allowed_order_limit' => 80,
                'remarks' => null,
                'status' => 'Inactive',
                'type' => 'Retail',
                'address' => 'Indore Industrial Area',
                'pincode' => '452001',
                'state_id' => 2,
                'city_id' => 2,
                'bank_name' => 'ICICI Bank',
                'account_holder_name' => 'Sanjay Steels Pvt Ltd',
                'ifsc_code' => 'ICIC0005678',
                'account_number' => '444455556666',
                'created_by' => 'Super Admin',
                'contact_persons' => [
                    ['name' => 'Sanjay Patel', 'mobile_no' => '9988776655', 'email' => 'sanjay.patel@steels.com'],
                ],
            ],
            [
                'name' => 'Mahesh Iron',
                'distributor_id' => null,
                'code' => 'DLR003',
                'mobile_no' => '9123456789',
                'email' => 'mahesh@iron.com',
                'gst_num' => 'GSTM003',
                'pan_num' => 'PANM003',
                'order_limit' => 120,
                'allowed_order_limit' => 120,
                'remarks' => null,
                'status' => 'Active',
                'type' => 'Wholesale',
                'address' => 'Nagpur Industrial Area',
                'pincode' => '440001',
                'state_id' => 2,
                'city_id' => 3,
                'bank_name' => 'HDFC Bank',
                'account_holder_name' => 'Mahesh Iron Ltd',
                'ifsc_code' => 'HDFC0009876',
                'account_number' => '556677889900',
                'created_by' => 'Super Admin',
                'contact_persons' => [
                    ['name' => 'Mahesh Sharma', 'mobile_no' => '9001122334', 'email' => 'mahesh.sharma@iron.com'],
                ],
            ],
            [
                'name' => 'City Steel House',
                'distributor_id' => null,
                'code' => 'DLR004',
                'mobile_no' => '8901234567',
                'email' => 'citysteel@example.com',
                'gst_num' => 'GSTCS004',
                'pan_num' => 'PANCS004',
                'order_limit' => 95,
                'allowed_order_limit' => 95,
                'remarks' => 'Reliable dealer',
                'status' => 'Active',
                'type' => 'Retail',
                'address' => 'Pune Central',
                'pincode' => '411001',
                'state_id' => 2,
                'city_id' => 4,
                'bank_name' => 'Bank of India',
                'account_holder_name' => 'City Steel House',
                'ifsc_code' => 'BKID0001234',
                'account_number' => '123443211234',
                'created_by' => 'Super Admin',
                'contact_persons' => [
                    ['name' => 'Rahul Joshi', 'mobile_no' => '9988774411', 'email' => 'rahul@citysteel.com'],
                ],
            ],
            [
                'name' => 'Metro Hardware',
                'distributor_id' => null,
                'code' => 'DLR005',
                'mobile_no' => '9786543210',
                'email' => 'metro@hardware.com',
                'gst_num' => 'GSTMH005',
                'pan_num' => 'PANMH005',
                'order_limit' => 60,
                'allowed_order_limit' => 60,
                'remarks' => null,
                'status' => 'Active',
                'type' => 'Retail',
                'address' => 'Lucknow Main Road',
                'pincode' => '226001',
                'state_id' => 2,
                'city_id' => 5,
                'bank_name' => 'SBI',
                'account_holder_name' => 'Metro Hardware',
                'ifsc_code' => 'SBIN0004567',
                'account_number' => '777788889999',
                'created_by' => 'Super Admin',
                'contact_persons' => [
                    ['name' => 'Amit Verma', 'mobile_no' => '9123412341', 'email' => 'amit@metrohardware.com'],
                ],
            ],
        ];

        // Generate 10 more dealers with a loop using faker-like data
        // for ($i = 6; $i <= 15; $i++) {
        //     $order_limit = rand(50, 200); // Store once and reuse
        //     $dealers[] = [
        //         'name' => "Generic Dealer $i",
        //         'distributor_id' => null,
        //         'code' => "DLR00$i",
        //         'mobile_no' => "99900000$i",
        //         'email' => "dealer$i@example.com",
        //         'gst_num' => "GST00$i",
        //         'pan_num' => "PAN00$i",
        //         'order_limit' => $order_limit,
        //         'allowed_order_limit' => $order_limit,
        //         'remarks' => null,
        //         'status' => 'Active',
        //         'type' => rand(0, 1) ? 'Wholesale' : 'Retail',
        //         'address' => "Main Market Area $i",
        //         'pincode' => "4900$i",
        //         'state_id' => rand(1, 5),
        //         'city_id' => rand(1, 10),
        //         'bank_name' => 'Test Bank',
        //         'account_holder_name' => "Dealer $i",
        //         'ifsc_code' => "TEST000$i",
        //         'account_number' => "99999$i",
        //         'contact_persons' => [
        //             ['name' => "Person A$i", 'mobile_no' => "91100000$i", 'email' => "personA$i@example.com"],
        //             ['name' => "Person B$i", 'mobile_no' => "92200000$i", 'email' => "personB$i@example.com"],
        //         ],
        //     ];
        // }

        // Insert into DB
        foreach ($dealers as $data) {
            $contactPersons = $data['contact_persons'];
            unset($data['contact_persons']);

            $dealer = Dealer::create($data);
            $dealer->contactPersons()->createMany($contactPersons);
        }
    }
}
