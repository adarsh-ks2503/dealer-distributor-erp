<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ItemBundle;
use App\Models\Item;
use App\Models\ItemSize;
use Illuminate\Support\Str;

class ItemBundleSeeder extends Seeder
{
    public function run(): void
    {
        // Get some existing items and sizes
        $items = Item::all();
        $sizes = ItemSize::all();

        // Ensure we have data to work with
        if ($items->isEmpty() || $sizes->isEmpty()) {
            $this->command->warn('⚠️ Skipping seeder: No items or item_sizes found.');
            return;
        }

        // Seed 10 bundles
        for ($i = 0; $i < 10; $i++) {
            $item = $items->random();
            $size = $sizes->where('item', $item->id)->random(); // sizes for that item only

            ItemBundle::create([
                'item_id'       => $item->id,
                'bundle_name'   => 'Bundle-' . strtoupper(Str::random(4)),
                'size_id'       => $size->id,
                'pieces'        => rand(5, 30),
                'initial_range' => rand(100, 200),
                'end_range'     => rand(201, 500),
                'status'        => 'Active',
            ]);
        }
    }
}
