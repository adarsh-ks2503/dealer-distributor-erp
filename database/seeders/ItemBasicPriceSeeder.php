<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ItemBasicPrice;
use Illuminate\Support\Carbon;

class ItemBasicPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $baseMarketPrice = 55000;
        $baseDistributorPrice = 54000;
        $baseDealerPrice = 53000;

        for ($region = 1; $region <= 25; $region++) {
            ItemBasicPrice::create([
                'item' => 1,
                'region' => $region,
                'market_basic_price' => $baseMarketPrice + ($region * 100),       // increment per region
                'distributor_basic_price' => $baseDistributorPrice + ($region * 100),
                'dealer_basic_price' => $baseDealerPrice + ($region * 100),
                'status' => $region % 5 === 0 ? 'Pending' : 'Approved',            // every 5th region is pending
                'approval_date' => $region % 5 === 0 ? null : Carbon::now()->subDays($region),
                'approved_by' => $region % 5 === 0 ? null : 'Super Admin',
            ]);
        }
    }
}
