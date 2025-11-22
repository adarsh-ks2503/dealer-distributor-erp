<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ItemSize;
use Illuminate\Support\Carbon;

class ItemSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $approvalTime = Carbon::now();
        $approvedBy = 'Super Admin'; // Super admin email or identifier

        $sizes = [
            ['size' => 8,  'rate' => 6000, 'hsn' => 'HSN001', 'remarks' => 'TMT Bar 8mm'],
            ['size' => 10, 'rate' => 5500, 'hsn' => 'HSN002', 'remarks' => 'TMT Bar 10mm'],
            ['size' => 12, 'rate' => 5000, 'hsn' => 'HSN003', 'remarks' => 'TMT Bar 12mm'],
            ['size' => 16, 'rate' => 6500, 'hsn' => 'HSN004', 'remarks' => 'TMT Bar 16mm'],
            ['size' => 20, 'rate' => 4000, 'hsn' => 'HSN005', 'remarks' => 'TMT Bar 20mm'],
            ['size' => 25, 'rate' => 4000, 'hsn' => 'HSN006', 'remarks' => 'TMT Bar 25mm'],
            ['size' => 32, 'rate' => 4000, 'hsn' => 'HSN007', 'remarks' => 'TMT Bar 32mm'],
        ];

        foreach ($sizes as $data) {
            ItemSize::create([
                'item' => 1,
                'size' => $data['size'],
                'rate' => $data['rate'],
                'hsn_code' => $data['hsn'],
                'remarks' => $data['remarks'],
                'status' => 'Active',
                'approval_time' => $approvalTime,
                'approved_by' => $approvedBy,
            ]);
        }
    }
}
