<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ItemGroup;
use App\Models\ItemName;
use App\Models\ItemSize;
use App\Models\RollingProgramItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemListController extends Controller
{
    public function all_item_list()
    {
        try {
            $items_grouped = ItemName::select(
                DB::raw('GROUP_CONCAT(name SEPARATOR "/") as label'),
                DB::raw('MIN(price) as price')
            )
                ->groupBy('item_group')
                ->get();

            // Fetch and group item groups
            $grouped = ItemGroup::with('item_name')->get()->groupBy('item_name_id');

            if ($items_grouped->isEmpty() && $grouped->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No items or item groups found.',
                    'data' => [
                        'item_name' => [],
                        'item_group' => []
                    ]
                ], 404);
            }

            // Map over grouped data
            $groups = $grouped->map(function ($group) {
                $items = $group->map(function ($item) {
                    // ✅ sizes only if stock is available
                    $sizes = ItemSize::whereIn('id', $item->item_master_id ?? [])
                        ->whereHas('stocks', function ($query) {
                            $query->where(function ($q) {
                                $q->where('sunil_steel', '>', 0)
                                    ->orWhere('sunil_sponge', '>', 0);
                            });
                        })
                        ->get();

                    return [
                        'rate' => ($item->rate == 0) ? 'Basic Price' : $item->rate,
                        'stock' => $sizes->isNotEmpty() ? 'In Stock' : 'Out of Stock',
                        'sizes' => $sizes->map(function ($size) {
                            return [
                                'size' => $size->size,
                            ];
                        })->values(),
                    ];
                })
                    // ❌ remove items where no sizes found
                    ->filter(function ($item) {
                        return $item['sizes']->isNotEmpty();
                    })
                    ->values();

                // ❌ if no items left, remove group
                if ($items->isEmpty()) {
                    return null;
                }

                return [
                    'item_name' => $group->first()->item_name->name ?? "N/A",
                    'price' => $group->first()->item_name->price ?? 0,
                    'items' => $items,
                ];
            })
                // remove null groups
                ->filter()
                ->values();

            // Final return
            return response()->json([
                'status' => true,
                'message' => $groups->isEmpty()
                    ? 'Items fetched successfully, but no stock is available.'
                    : 'Items fetched successfully.',
                'data' => [
                    'item_name' => $items_grouped,
                    'item_group' => $groups,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
            ], 500);
        }
    }


    public function item_list()
    {
        try {
            // Fetch all item names
            $items = ItemName::select('name', 'price')->get();

            // Fetch and group item groups
            $grouped = ItemGroup::with('item_name')->get()->groupBy('item_name_id');

            if ($items->isEmpty() && $grouped->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No items or item groups found.',
                    'data' => [
                        'item_name' => [],
                        'item_group' => []
                    ]
                ], 404);
            }

            // Map over grouped data
            $groups = $grouped->map(function ($group) {
                return [
                    'item_name' => $group->first()->item_name->name ?? "N/A",
                    'price' => $group->first()->item_name->price ?? 0,
                    'items' => $group->map(function ($item) {
                        $sizes = ItemSize::whereIn('id', $item->item_master_id ?? [])->get();

                        return [
                            'rate' => ($item->rate == 0) ? 'Basic Price' : $item->rate,
                            'stock' => 'In Stock',
                            'sizes' => $sizes->map(function ($size) {
                                return [
                                    'size' => $size->size,
                                ];
                            }),
                        ];
                    }),
                ];
            })->values();

            return response()->json([
                'status' => true,
                'message' => 'Items fetched successfully.',
                'data' => [
                    'item_name' => $items,
                    'item_group' => $groups,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
            ], 500);
        }
    }



    public function rolling_list()
    {
        try {
            $date = date('Y-m-d');

            // Fetch rolling items updated today, with related item data
            $rollings = RollingProgramItem::with('item', 'size')
                ->whereHas('rolling', function ($q) {
                    $q->where('status', 'Approved');
                })
                ->whereDate('updated_at', $date)
                ->get();

            if ($rollings->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No rolling items found for today.',
                    'data' => []
                ], 404);
            }

            // Transform response data
            $data = $rollings->map(function ($item) {
                return [
                    'item_name' => $item->item->name ?? 'N/A',
                    'item_size' => $item->size->size ?? 'N/A',
                    'quantity' => $item->quantity
                ];
            });

            return response()->json([
                'status' => true,
                'message' => 'Rolling items fetched successfully.',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
}
