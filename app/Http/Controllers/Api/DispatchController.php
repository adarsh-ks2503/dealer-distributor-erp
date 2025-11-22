<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dispatch;
use App\Models\Order;
use App\Models\OrderAllocation;
use App\Models\Warehouse;
use App\Models\ItemSize;
use App\Models\Item;
use App\Models\DispatchItem;
use App\Models\DispatchAttachment;
use App\Models\Dealer;
use App\Models\Distributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Storage;
use App\Models\State;
use App\Models\City;
use Illuminate\Database\QueryException;
use App\Notifications\DispatchCreated;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

class DispatchController extends Controller
{
    /**
     * Helper method to get user type and party information
     */
    // private function getUserPartyInfo($user)
    // {
    //     $userType = null;
    //     $userId = null;
    //     $party = null;

    //     // Method 1: Check if user has user_type field
    //     if (isset($user->user_type)) {
    //         $userType = $user->user_type;

    //         if ($userType === 'dealer') {
    //             $userId = $user->dealer_id ?? null;
    //             if (!$userId) {
    //                 $dealer = Dealer::where('id', $user->id)->first();
    //                 $userId = $dealer ? $dealer->id : null;
    //             }
    //             if ($userId) {
    //                 $party = Dealer::with('state', 'city')->find($userId);
    //             }
    //         } elseif ($userType === 'distributor') {
    //             $userId = $user->distributor_id ?? null;
    //             if (!$userId) {
    //                 $distributor = Distributor::where('id', $user->id)->first();
    //                 $userId = $distributor ? $distributor->id : null;
    //             }
    //             if ($userId) {
    //                 $party = Distributor::with('state', 'city')->find($userId);
    //             }
    //         }
    //     }
    //     // Method 2: Check relationships
    //     elseif (method_exists($user, 'dealer') && $user->dealer) {
    //         $userType = 'dealer';
    //         $userId = $user->dealer->id;
    //         $party = Dealer::with('state', 'city')->find($userId);
    //     } elseif (method_exists($user, 'distributor') && $user->distributor) {
    //         $userType = 'distributor';
    //         $userId = $user->distributor->id;
    //         $party = Distributor::with('state', 'city')->find($userId);
    //     }
    //     // Method 3: Find by user_id in dealer/distributor tables
    //     else {
    //         $dealer = Dealer::where('id', $user->id)->with('state', 'city')->first();
    //         if ($dealer) {
    //             $userType = 'dealer';
    //             $userId = $dealer->id;
    //             $party = $dealer;
    //         } else {
    //             $distributor = Distributor::where('id', $user->id)->with('state', 'city')->first();
    //             if ($distributor) {
    //                 $userType = 'distributor';
    //                 $userId = $distributor->id;
    //                 $party = $distributor;
    //             }
    //         }
    //     }

    //     return [
    //         'user_type' => $userType,
    //         'user_id' => $userId,
    //         'party' => $party
    //     ];
    // }

    private function getUserPartyInfo($user)
    {
        $userType = null;
        $userId = null;
        $party = null;

        // --- YAHAN FIX KIYA GAYA HAI ---
        // 'user_type' ki jagah 'type' istemaal kiya gaya hai,
        // aur 'id' ki jagah 'code' se linking ki gayi hai.

        if (isset($user->type)) {
            $userType = $user->type;

            if ($userType === 'dealer') {
                // User ke 'code' se 'dealers' table mein 'code' ko match karo
                $party = Dealer::with('state', 'city')->where('code', $user->code)->first();
                if ($party) {
                    $userId = $party->id;
                }
            } elseif ($userType === 'distributor') {
                // User ke 'code' se 'distributors' table mein 'code' ko match karo
                $party = Distributor::with('state', 'city')->where('code', $user->code)->first();
                if ($party) {
                    $userId = $party->id;
                }
            }
        }

        // --- BAAKI KA LOGIC AB ZAROORI NAHI HAI ---
        // Kyunki upar wala logic hi 99% cases ko handle kar lega.
        // Agar $party abhi bhi null hai, toh function null hi return karega, jo ki sahi hai.

        return [
            'user_type' => $userType,
            'user_id' => $userId,
            'party' => $party
        ];
    }

    /**
     * Step 1: Get initial data for creating dispatch
     * Returns: warehouses, sizes, charges, and user's approved orders
     */
    // public function prepare(Request $request)
    // {
    //     try {
    //         $user = $request->user();

    //         // Get user party information
    //         $userInfo = $this->getUserPartyInfo($user);
    //         $userType = $userInfo['user_type'];
    //         $userId = $userInfo['user_id'];
    //         $party = $userInfo['party'];

    //         // If no party found, return error
    //         if (!$party || !$userType || !$userId) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'User is not associated with a dealer or distributor. Please contact administrator.'
    //             ], 403);
    //         }

    //         // Get approved orders for the logged-in user
    //         $orders = Order::with(['allocations' => function ($query) {
    //             $query->where('remaining_qty', '>', 0);
    //         }])
    //             ->where('type', $userType)
    //             ->whereIn('status', ['approved', 'partial dispatch'])
    //             ->when($userType === 'dealer', function ($q) use ($userId) {
    //                 return $q->where('placed_by_dealer_id', $userId);
    //             })
    //             ->when($userType === 'distributor', function ($q) use ($userId) {
    //                 return $q->where('placed_by_distributor_id', $userId);
    //             })
    //             ->get()
    //             ->map(function ($order) {
    //                 return [
    //                     'id' => $order->id,
    //                     'order_number' => $order->order_number,
    //                     'created_at' => $order->created_at->format('Y-m-d'),
    //                     'ordered_qty' => $order->allocations->sum('qty'),
    //                     'remaining_qty' => $order->allocations->sum('remaining_qty')
    //                 ];
    //             });

    //         // Get master data
    //         $warehouses = Warehouse::select('id', 'name')->get();
    //         $sizes = ItemSize::where('status', 'Active')->select('id', 'size', 'rate')->get();

    //         // Get charges and rates
    //         $loadingCharge = \App\Models\LoadingCharge::first()->amount ?? 265;
    //         $insuranceCharge = \App\Models\InsuranceCharge::first()->amount ?? 40;
    //         $gstRate = \App\Models\GstRate::first()->rate ?? 18;
    //         $singleItemName = Item::first()->item_name ?? 'TMT Bar';

    //         return response()->json([
    //             'success' => true,
    //             'data' => [
    //                 'user_type' => $userType,
    //                 'user_id' => $userId,
    //                 'party' => [
    //                     'id' => $party->id,
    //                     'name' => $party->name,
    //                     'code' => $party->code,
    //                     'mobile_no' => $party->mobile_no,
    //                     'email' => $party->email,
    //                     'gst_num' => $party->gst_num,
    //                     'pan_num' => $party->pan_num,
    //                     'order_limit' => $party->order_limit,
    //                     'address' => $party->address,
    //                     'state' => $party->state ? $party->state->state : null,
    //                     'state_id' => $party->state_id,
    //                     'city' => $party->city ? $party->city->city : null,
    //                     'city_id' => $party->city_id,
    //                     'pincode' => $party->pincode
    //                 ],
    //                 'orders' => $orders,
    //                 'warehouses' => $warehouses,
    //                 'sizes' => $sizes,
    //                 'charges' => [
    //                     'loading_charge' => $loadingCharge,
    //                     'insurance_charge' => $insuranceCharge,
    //                     'gst_rate' => $gstRate
    //                 ],
    //                 'item_name' => $singleItemName
    //             ]
    //         ]);
    //     } catch (Exception $e) {
    //         Log::error('Dispatch prepare error: ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to fetch dispatch data',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // /**
    //  * Step 2: Get allocations for selected orders
    //  */
    // public function getPendingOrders(Request $request)
    // {
    //     try {
    //         $validator = Validator::make($request->all(), [
    //             'order_ids' => 'required|array',
    //             'order_ids.*' => 'required|integer|exists:orders,id'
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Validation failed',
    //                 'errors' => $validator->errors()
    //             ], 422);
    //         }

    //         $user = $request->user();
    //         $orderIds = $request->order_ids;

    //         // Get user party information
    //         $userInfo = $this->getUserPartyInfo($user);
    //         $userType = $userInfo['user_type'];
    //         $userId = $userInfo['user_id'];

    //         if (!$userType || !$userId) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'User is not associated with a dealer or distributor'
    //             ], 403);
    //         }

    //         // Verify orders belong to the logged-in user
    //         $orders = Order::with(['allocations.allocatable'])
    //             ->whereIn('id', $orderIds)
    //             ->when($userType === 'dealer', function ($q) use ($userId) {
    //                 return $q->where('placed_by_dealer_id', $userId);
    //             })
    //             ->when($userType === 'distributor', function ($q) use ($userId) {
    //                 return $q->where('placed_by_distributor_id', $userId);
    //             })
    //             ->get();

    //         if ($orders->count() !== count($orderIds)) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Some orders do not belong to you or do not exist'
    //             ], 403);
    //         }

    //         $ordersData = $orders->map(function ($order) {
    //             return [
    //                 'id' => $order->id,
    //                 'order_number' => $order->order_number,
    //                 'allocations' => $order->allocations->where('remaining_qty', '>', 0)->map(function ($allocation) {
    //                     $allocatable = $allocation->allocatable;
    //                     return [
    //                         'id' => $allocation->id,
    //                         'qty' => $allocation->qty,
    //                         'dispatched_qty' => $allocation->dispatched_qty ?? 0,
    //                         'remaining_qty' => $allocation->remaining_qty,
    //                         'agreed_basic_price' => $allocation->agreed_basic_price,
    //                         'payment_terms' => $allocation->payment_terms,
    //                         'token_amount' => $allocation->token_amount,
    //                         'allocated_to_type' => $allocation->allocated_to_type,
    //                         'allocated_to_id' => $allocation->allocated_to_id,
    //                         'dealer_name' => $allocation->allocated_to_type === 'dealer' ? ($allocatable->name ?? 'N/A') : null,
    //                         'dealer_code' => $allocation->allocated_to_type === 'dealer' ? ($allocatable->code ?? 'N/A') : null,
    //                         'distributor_name' => $allocation->allocated_to_type === 'distributor' ? ($allocatable->name ?? 'N/A') : null,
    //                         'distributor_code' => $allocation->allocated_to_type === 'distributor' ? ($allocatable->code ?? 'N/A') : null,
    //                     ];
    //                 })->values()
    //             ];
    //         });

    //         return response()->json([
    //             'success' => true,
    //             'data' => $ordersData
    //         ]);
    //     } catch (Exception $e) {
    //         Log::error('Get pending orders error: ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to fetch orders',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }


    public function prepare(Request $request)
    {
        try {
            $user = $request->user();

            // Get user party information (ab yeh sahi kaam karega)
            $userInfo = $this->getUserPartyInfo($user);
            $userType = $userInfo['user_type'];
            $userId = $userInfo['user_id'];
            $party = $userInfo['party'];

            if (!$party || !$userType || !$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not associated with a dealer or distributor. Please contact administrator.'
                ], 403);
            }

            // --- YAHAN LOGIC THEEK KIYA GAYA HAI ---
            $ordersQuery = Order::with(['allocations' => function ($query) {
                $query->where('remaining_qty', '>', 0);
            }])
                ->whereIn('status', ['approved', 'partial dispatch']);

            if ($userType === 'dealer') {
                $ordersQuery->where('type', 'dealer')
                    ->where('placed_by_dealer_id', $userId);
            } elseif ($userType === 'distributor') {
                // Distributor ki team ke saare dealer IDs
                $teamDealerIds = Dealer::where('distributor_id', $userId)->pluck('id');

                $ordersQuery->where(function ($q) use ($userId, $teamDealerIds) {
                    // Condition 1: Woh orders jo distributor ne khud place kiye (type: distributor)
                    $q->where(function ($sq) use ($userId) {
                        $sq->where('type', 'distributor')
                            ->where('placed_by_distributor_id', $userId);
                    })
                        // Condition 2: YA, woh orders jo uski team ke dealers ne place kiye (type: dealer)
                        ->orWhere(function ($sq) use ($teamDealerIds) {
                            $sq->where('type', 'dealer')
                                ->whereIn('placed_by_dealer_id', $teamDealerIds);
                        });
                });
            }

            $orders = $ordersQuery->get()->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'created_at' => $order->created_at->format('Y-m-d'),
                    'ordered_qty' => $order->allocations->sum('qty'),
                    'remaining_qty' => $order->allocations->sum('remaining_qty')
                ];
            });
            // --- LOGIC FIX END ---

            // Get master data
            $warehouses = Warehouse::select('id', 'name')->get();
            $sizes = ItemSize::where('status', 'Active')->select('id', 'size', 'rate')->get();

            // Get charges and rates
            $loadingCharge = \App\Models\LoadingCharge::first()->amount ?? 265;
            $insuranceCharge = \App\Models\InsuranceCharge::first()->amount ?? 40;
            $gstRate = \App\Models\GstRate::first()->rate ?? 18;
            $singleItemName = Item::first()->item_name ?? 'TMT Bar';

            return response()->json([
                'success' => true,
                'data' => [
                    'user_type' => $userType,
                    'user_id' => $userId,
                    'party' => [
                        'id' => $party->id,
                        'name' => $party->name,
                        'code' => $party->code,
                        'mobile_no' => $party->mobile_no,
                        'email' => $party->email,
                        'gst_num' => $party->gst_num,
                        'pan_num' => $party->pan_num,
                        'order_limit' => $party->order_limit,
                        'address' => $party->address,
                        'state' => $party->state ? $party->state->state : null,
                        'state_id' => $party->state_id,
                        'city' => $party->city ? $party->city->city : null,
                        'city_id' => $party->city_id,
                        'pincode' => $party->pincode
                    ],
                    'orders' => $orders,
                    'warehouses' => $warehouses,
                    'sizes' => $sizes,
                    'charges' => [
                        'loading_charge' => $loadingCharge,
                        'insurance_charge' => $insuranceCharge,
                        'gst_rate' => $gstRate
                    ],
                    'item_name' => $singleItemName
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Dispatch prepare error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dispatch data',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function getPendingOrders(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_ids' => 'required|array',
                'order_ids.*' => 'required|integer|exists:orders,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();
            $orderIds = $request->order_ids;

            // Get user party information (ab yeh sahi kaam karega)
            $userInfo = $this->getUserPartyInfo($user);
            $userType = $userInfo['user_type'];
            $userId = $userInfo['user_id']; // Yeh $party->id hai

            if (!$userType || !$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not associated with a dealer or distributor'
                ], 403);
            }

            // --- YAHAN LOGIC THEEK KIYA GAYA HAI ---
            // Verify orders belong to the logged-in user OR their team
            $query = Order::with(['allocations.allocatable'])
                ->whereIn('id', $orderIds)
                ->whereIn('status', ['approved', 'partial dispatch']);

            if ($userType === 'dealer') {
                $query->where('placed_by_dealer_id', $userId);
            } elseif ($userType === 'distributor') {
                // Distributor ki team ke saare dealer IDs
                $teamDealerIds = Dealer::where('distributor_id', $userId)->pluck('id');

                $query->where(function ($q) use ($userId, $teamDealerIds) {
                    // Order ya toh distributor ne place kiya ho
                    $q->where('placed_by_distributor_id', $userId)
                        // Ya uski team ke kisi dealer ne place kiya ho
                        ->orWhereIn('placed_by_dealer_id', $teamDealerIds);
                });
            }

            $orders = $query->get();
            // --- LOGIC FIX END ---

            if ($orders->count() !== count($orderIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some orders do not belong to you or do not exist'
                ], 403);
            }

            $ordersData = $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'allocations' => $order->allocations->where('remaining_qty', '>', 0)->map(function ($allocation) {
                        $allocatable = $allocation->allocatable;
                        return [
                            'id' => $allocation->id,
                            'qty' => $allocation->qty,
                            'dispatched_qty' => $allocation->dispatched_qty ?? 0,
                            'remaining_qty' => $allocation->remaining_qty,
                            'agreed_basic_price' => $allocation->agreed_basic_price,
                            'payment_terms' => $allocation->payment_terms,
                            'token_amount' => $allocation->token_amount,
                            'allocated_to_type' => $allocation->allocated_to_type,
                            'allocated_to_id' => $allocation->allocated_to_id,
                            'dealer_name' => $allocation->allocated_to_type === 'dealer' ? ($allocatable->name ?? 'N/A') : null,
                            'dealer_code' => $allocation->allocated_to_type === 'dealer' ? ($allocatable->code ?? 'N/A') : null,
                            'distributor_name' => $allocation->allocated_to_type === 'distributor' ? ($allocatable->name ?? 'N/A') : null,
                            'distributor_code' => $allocation->allocated_to_type === 'distributor' ? ($allocatable->code ?? 'N/A') : null,
                        ];
                    })->values()
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $ordersData
            ]);
        } catch (Exception $e) {
            Log::error('Get pending orders error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Step 3: Create dispatch
     */
    // public function store(Request $request)
    // {
    //     $paymentSlipPath = null;
    //     $attachmentPaths = [];

    //     // ORIGINAL VALIDATION RULES (No fields removed)
    //     $validator = Validator::make($request->all(), [
    //         'warehouse_id' => 'required|integer|exists:warehouses,id',
    //         'dispatch_date' => 'required|date|after_or_equal:today',
    //         'recipient_name' => 'required|string|max:255',
    //         'recipient_address' => 'required|string|max:1000',
    //         'recipient_state_id' => 'required|integer|exists:states,id',
    //         'recipient_city_id' => 'required|integer|exists:cities,id',
    //         'recipient_pincode' => 'required|string|regex:/^[0-9]{5,6}$/',
    //         'consignee_name' => 'required|string|max:255',
    //         'consignee_address' => 'required|string|max:1000',
    //         'consignee_state_id' => 'required|integer|exists:states,id',
    //         'consignee_city_id' => 'required|integer|exists:cities,id',
    //         'consignee_pincode' => 'required|string|regex:/^[0-9]{5,6}$/',
    //         'consignee_mobile_no' => 'nullable|string|regex:/^[0-9]{10,15}$/',
    //         'bill_to' => 'nullable|string|max:255',
    //         'bill_number' => 'nullable|string|alpha_num:ascii|max:50',
    //         'dispatch_out_time' => 'nullable|date_format:H:i',
    //         'payment_slip' => 'nullable|file|max:2048',
    //         'dispatch_remarks' => 'nullable|string|max:2000',
    //         'transporter_name' => 'required|string|max:255',
    //         'vehicle_no' => 'nullable|string|regex:/^[A-Z0-9 -]{5,20}$/',
    //         'driver_name' => 'nullable|string|max:255',
    //         'driver_mobile_no' => 'nullable|string|regex:/^[0-9]{10,15}$/',
    //         'e_way_bill_no' => 'nullable|string|alpha_num:ascii|max:50',
    //         'bilty_no' => 'nullable|string|alpha_num:ascii|max:50',
    //         'transport_remarks' => 'nullable|string|max:2000',
    //         'terms_conditions' => 'nullable|string|max:5000',
    //         'additional_charges' => 'nullable|numeric|min:0|max:99999999.99',
    //         'items' => 'required|array|min:1',
    //         'items.*.order_id' => 'required|integer|exists:orders,id',
    //         'items.*.allocation_id' => 'required|integer|exists:order_allocations,id',
    //         'items.*.order_qty' => 'nullable|numeric|min:0',
    //         'items.*.already_disp' => 'nullable|numeric|min:0',
    //         'items.*.remaining_qty' => 'nullable|numeric|min:0',
    //         'items.*.item_name' => 'required|string|max:255',
    //         'items.*.size_id' => 'required|integer|exists:item_sizes,id',
    //         'items.*.length' => 'required|numeric|min:0|max:999.99',
    //         'items.*.dispatch_qty' => 'required|numeric|min:0.01',
    //         'items.*.basic_price' => 'nullable|numeric|min:0',
    //         'items.*.gauge_diff' => 'nullable|numeric',
    //         'items.*.final_price' => 'nullable|numeric|min:0',
    //         'items.*.loading_charge' => 'nullable|numeric|min:0',
    //         'items.*.insurance' => 'nullable|numeric|min:0',
    //         'items.*.gst' => 'nullable|numeric|min:0|max:100',
    //         'items.*.token_amount' => 'nullable|numeric|min:0',
    //         'items.*.total_amount' => 'nullable|numeric|min:0',
    //         'items.*.payment_term' => 'nullable|string|in:Advance,Next Day,15 Days Later,30 Days Later',
    //         'items.*.remark' => 'nullable|string|max:2000',
    //         'attachments' => 'nullable|array',
    //         'attachments.*.document' => 'nullable|file|max:2048',
    //         'attachments.*.remark' => 'nullable|string|max:2000',
    //     ]);

    //     // Additional validation
    //     $validator->after(function ($validator) use ($request) {
    //         if ($validator->errors()->any()) {
    //             return;
    //         }

    //         // Check for duplicate allocation and size pairs
    //         $pairs = [];
    //         foreach ($request->input('items', []) as $index => $item) {
    //             $pair = $item['allocation_id'] . '-' . $item['size_id'];
    //             if (in_array($pair, $pairs)) {
    //                 $validator->errors()->add("items.$index.size_id", 'Duplicate allocation and size combination');
    //             } else {
    //                 $pairs[] = $pair;
    //             }
    //         }

    //         // Check dispatch quantity threshold
    //         $order_proposed = [];
    //         foreach ($request->input('items', []) as $index => $item) {
    //             $order_id = $item['order_id'];
    //             if (!isset($order_proposed[$order_id])) {
    //                 $order_proposed[$order_id] = 0;
    //             }
    //             $order_proposed[$order_id] += (float) $item['dispatch_qty'];
    //         }

    //         foreach ($order_proposed as $order_id => $proposed) {
    //             $total_remaining = OrderAllocation::where('order_id', $order_id)->sum('remaining_qty');
    //             if ($proposed > $total_remaining + 5) {
    //                 $validator->errors()->add('items', 'Total dispatch quantity exceeds remaining quantity + 5 MT threshold for order #' . $order_id);
    //             }
    //         }

    //         // Verify quantities match DB
    //         foreach ($request->input('items', []) as $index => $item) {

    //             // [FIX 1] Optimized query to get 'order' relation at the same time
    //             $allocation = OrderAllocation::with('order')->find($item['allocation_id']);

    //             if (!$allocation) {
    //                 $validator->errors()->add("items.$index.allocation_id", 'Invalid allocation');
    //                 continue;
    //             }

    //             // --- !!! NEW FIX ADDED !!! ---
    //             // [FIX 2] Check if the allocation_id from app actually belongs to the order_id from app
    //             if ($allocation->order_id != $item['order_id']) {
    //                 $validator->errors()->add("items.$index.allocation_id", 'This item allocation does not belong to the selected order.');
    //                 continue; // Skip other checks for this item
    //             }
    //             // --- !!! END FIX !!! ---


    //             // Check 1: Kya parent Order 'approved' ya 'partial' hai?
    //             // This check is now safe because we verified the order_id above
    //             if (!in_array($allocation->order->status, ['approved', 'partial dispatch'])) {
    //                 $validator->errors()->add("items.$index.order_id", "Order #{$allocation->order->order_number} is not approved.");
    //             }

    //             // Check 2: Kya yeh specific allocation 'approved' hai?
    //             if ($allocation->status !== 'approved' && $allocation->status !== 'partially dispatched') {
    //                 $validator->errors()->add("items.$index.allocation_id", "This item allocation is still pending and cannot be dispatched.");
    //             }

    //             // Check 3: Remaining quantity check
    //             if ((float) $item['remaining_qty'] != $allocation->remaining_qty) {
    //                 $validator->errors()->add("items.$index.remaining_qty", 'Remaining quantity mismatch');
    //             }
    //         }

    //         // Verify orders belong to user
    //         $user = $request->user();
    //         $orderIds = collect($request->input('items'))->pluck('order_id')->unique()->toArray();

    //         $ordersCount = Order::whereIn('id', $orderIds)
    //             ->when($user->dealer, function ($q) use ($user) {
    //                 return $q->where('placed_by_dealer_id', $user->dealer->id);
    //             })
    //             ->when($user->distributor, function ($q) use ($user) {
    //                 return $q->where('placed_by_distributor_id', $user->distributor->id);
    //             })
    //             ->count();

    //         if ($ordersCount !== count($orderIds)) {
    //             $validator->errors()->add('items', 'Some orders do not belong to you');
    //         }
    //     });

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Validation failed',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     try {
    //         DB::beginTransaction();

    //         $user = $request->user();
    //         $validated = $validator->validated();

    //         // Get user party information
    //         $userInfo = $this->getUserPartyInfo($user);
    //         $type = $userInfo['user_type'];
    //         $partyId = $userInfo['user_id'];

    //         if (!$type || !$partyId) {
    //             throw new Exception('User is not associated with a dealer or distributor');
    //         }

    //         // Generate dispatch number
    //         $date = $validated['dispatch_date'];
    //         $parsedDate = \Carbon\Carbon::parse($date); // Using Carbon facade
    //         $year = $parsedDate->month >= 4 ? $parsedDate->year : $parsedDate->year - 1;
    //         $nextYear = substr($year + 1, -2);
    //         $financialYear = "{$year}-{$nextYear}";
    //         $yearStart = \Carbon\Carbon::create($year, 4, 1)->startOfDay(); // Using Carbon facade
    //         $yearEnd = \Carbon\Carbon::create($year + 1, 3, 31)->endOfDay(); // Using Carbon facade
    //         $count = Dispatch::whereBetween('dispatch_date', [$yearStart, $yearEnd])->count();
    //         $index = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    //         $dispatchNumber = "DO_{$financialYear}_{$index}";

    //         // Handle payment slip
    //         if ($request->hasFile('payment_slip')) {
    //             $paymentSlipPath = $request->file('payment_slip')->store('dispatches/payment_slips', 'public');
    //         }

    //         // Create dispatch (ORIGINAL FIELDS)
    //         $dispatch = Dispatch::create([
    //             'dispatch_number' => $dispatchNumber,
    //             'type' => $type,
    //             'distributor_id' => $type === 'distributor' ? $partyId : null,
    //             'dealer_id' => $type === 'dealer' ? $partyId : null,
    //             'recipient_name' => $validated['recipient_name'],
    //             'recipient_address' => $validated['recipient_address'],
    //             'recipient_state_id' => $validated['recipient_state_id'],
    //             'recipient_city_id' => $validated['recipient_city_id'],
    //             'recipient_pincode' => $validated['recipient_pincode'],
    //             'consignee_name' => $validated['consignee_name'],
    //             'consignee_address' => $validated['consignee_address'],
    //             'consignee_state_id' => $validated['consignee_state_id'],
    //             'consignee_city_id' => $validated['consignee_city_id'],
    //             'consignee_pincode' => $validated['consignee_pincode'],
    //             'consignee_mobile_no' => $validated['consignee_mobile_no'] ?? null,
    //             'dispatch_date' => $validated['dispatch_date'],
    //             'warehouse_id' => $validated['warehouse_id'],
    //             'bill_to' => $validated['bill_to'] ?? null,
    //             'bill_number' => $validated['bill_number'] ?? '00', // Kept your original logic
    //             'dispatch_out_time' => $validated['dispatch_out_time'] ?? null,
    //             'payment_slip' => $paymentSlipPath,
    //             'dispatch_remarks' => $validated['dispatch_remarks'] ?? null,
    //             'transporter_name' => $validated['transporter_name'],
    //             'vehicle_no' => $validated['vehicle_no'] ?? null,
    //             'driver_name' => $validated['driver_name'] ?? null,
    //             'driver_mobile_no' => $validated['driver_mobile_no'] ?? null,
    //             'e_way_bill_no' => $validated['e_way_bill_no'] ?? null,
    //             'bilty_no' => $validated['bilty_no'] ?? null,
    //             'transport_remarks' => $validated['transport_remarks'] ?? null,
    //             'terms_conditions' => $validated['terms_conditions'] ?? null,
    //             'additional_charges' => (float) ($validated['additional_charges'] ?? 0.00),
    //             'total_amount' => 0.00,
    //             'status' => 'pending'
    //         ]);

    //         // Process items (ORIGINAL CALCULATIONS)
    //         $grandTotal = (float) ($validated['additional_charges'] ?? 0.00);
    //         foreach ($validated['items'] as $item) {
    //             // Original backend calculation logic
    //             $finalPrice = (float) $item['basic_price'] + (float) $item['gauge_diff'];
    //             $baseTotal = $finalPrice + (float) $item['loading_charge'] + (float) $item['insurance'];
    //             $baseTotal *= (float) $item['dispatch_qty'];
    //             $gstAmount = $baseTotal * ((float) $item['gst'] / 100);
    //             $tokenAmount = (float) ($item['token_amount'] ?? 0.00);
    //             $itemTotal = $baseTotal + $gstAmount - $tokenAmount;

    //             // Note: The commented-out verification checks are kept as-is
    //             // if (abs($finalPrice - (float) $item['final_price']) > 0.01) {
    //             // ...
    //             // }
    //             // if (abs($itemTotal - (float) $item['total_amount']) > 0.01) {
    //             // ...
    //             // }

    //             DispatchItem::create([
    //                 'dispatch_id' => $dispatch->id,
    //                 'item_id' => 1, // This was hardcoded in original
    //                 'order_id' => $item['order_id'],
    //                 'allocation_id' => $item['allocation_id'],
    //                 'order_qty' => (float) $item['order_qty'],
    //                 'already_disp' => (float) $item['already_disp'],
    //                 'remaining_qty' => (float) $item['remaining_qty'],
    //                 'item_name' => $item['item_name'],
    //                 'size_id' => $item['size_id'],
    //                 'length' => (float) $item['length'],
    //                 'dispatch_qty' => (float) $item['dispatch_qty'],
    //                 'basic_price' => (float) $item['basic_price'],
    //                 'gauge_diff' => (float) $item['gauge_diff'],
    //                 'final_price' => $finalPrice, // Calculated value
    //                 'loading_charge' => (float) $item['loading_charge'],
    //                 'insurance' => (float) $item['insurance'],
    //                 'gst' => (float) $item['gst'],
    //                 'token_amount' => $tokenAmount > 0 ? $tokenAmount : null,
    //                 'total_amount' => $itemTotal, // Calculated value
    //                 'payment_term' => $item['payment_term'],
    //                 'status' => 'pending',
    //                 'remark' => $item['remark'] ?? null,
    //             ]);

    //             $grandTotal += $itemTotal;
    //         }

    //         // Update dispatch total
    //         $dispatch->total_amount = $grandTotal;
    //         $dispatch->save();

    //         // Handle attachments
    //         if ($request->hasFile('attachments')) {
    //             foreach ($request->file('attachments') as $key => $attachmentData) {
    //                 if (isset($attachmentData['document']) && $attachmentData['document']->isValid()) {
    //                     $extension = $attachmentData['document']->getClientOriginalExtension();
    //                     $filename = 'attachment_' . $dispatchNumber . '_' . $key . '_' . time() . '_' . Str::random(10) . '.' . $extension;
    //                     $path = $attachmentData['document']->storeAs('dispatches/attachments', $filename, 'public');
    //                     $attachmentPaths[] = $path;

    //                     DispatchAttachment::create([
    //                         'dispatch_id' => $dispatch->id,
    //                         'document' => $path,
    //                         'remark' => $request->input("attachments.$key.remark") ?? null,
    //                     ]);
    //                 }
    //             }
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Dispatch created successfully',
    //             'data' => [
    //                 'dispatch_id' => $dispatch->id,
    //                 'dispatch_number' => $dispatch->dispatch_number,
    //                 'status' => $dispatch->status,
    //                 'total_amount' => $dispatch->total_amount
    //             ]
    //         ], 201);
    //     } catch (Exception $e) {
    //         DB::rollBack();

    //         // Clean up uploaded files
    //         if ($paymentSlipPath) {
    //             Storage::disk('public')->delete($paymentSlipPath);
    //         }
    //         foreach ($attachmentPaths as $path) {
    //             Storage::disk('public')->delete($path);
    //         }

    //         Log::error('Dispatch creation error: ' . $e->getMessage() . ' on line ' . $e->getLine()); // Added getLine() for better debugging
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to create dispatch',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function store(Request $request)
    {
        $paymentSlipPath = null;
        $attachmentPaths = [];

        $validator = Validator::make($request->all(), [
            // 'warehouse_id' => 'required|integer|exists:warehouses,id', // <-- NEW CHANGE: Removed validation
            'dispatch_date' => 'required|date|after_or_equal:today',
            'recipient_name' => 'required|string|max:255',
            'recipient_address' => 'required|string|max:1000',
            'recipient_state_id' => 'required|integer|exists:states,id',
            'recipient_city_id' => 'required|integer|exists:cities,id',
            'recipient_pincode' => 'required|string|regex:/^[0-9]{5,6}$/',
            'consignee_name' => 'required|string|max:255',
            'consignee_address' => 'required|string|max:1000',
            'consignee_state_id' => 'required|integer|exists:states,id',
            'consignee_city_id' => 'required|integer|exists:cities,id',
            'consignee_pincode' => 'required|string|regex:/^[0-9]{5,6}$/',
            'consignee_mobile_no' => 'nullable|string|regex:/^[0-9]{10,15}$/',
            'bill_to' => 'nullable|string|max:255',
            'bill_number' => 'nullable|string|alpha_num:ascii|max:50',
            'dispatch_out_time' => 'nullable|date_format:H:i',
            'payment_slip' => 'nullable|file|max:2048',
            'dispatch_remarks' => 'nullable|string|max:2000',
            // 'transporter_name' => 'required|string|max:255',
            'transporter_name' => 'nullable|string|max:255',
            'vehicle_no' => 'nullable|string|regex:/^[A-Z0-9 -]{5,20}$/',
            'driver_name' => 'nullable|string|max:255',
            'driver_mobile_no' => 'nullable|string|regex:/^[0-9]{10,15}$/',
            'e_way_bill_no' => 'nullable|string|alpha_num:ascii|max:50',
            'bilty_no' => 'nullable|string|alpha_num:ascii|max:50',
            'transport_remarks' => 'nullable|string|max:2000',
            'terms_conditions' => 'nullable|string|max:5000',
            'additional_charges' => 'nullable|numeric|min:0|max:99999999.99',
            'items' => 'required|array|min:1',
            'items.*.order_id' => 'required|integer|exists:orders,id',
            'items.*.allocation_id' => 'required|integer|exists:order_allocations,id',
            'items.*.order_qty' => 'nullable|numeric|min:0',
            'items.*.already_disp' => 'nullable|numeric|min:0',
            'items.*.remaining_qty' => 'nullable|numeric|min:0',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.size_id' => 'required|integer|exists:item_sizes,id',
            // 'items.*.length' => 'required|numeric|min:0|max:999.99',
            // 'items.*.length' => 'nullable|numeric|min:0|max:999.99',
            'items.*.dispatch_qty' => 'required|numeric|min:0.01',
            'items.*.basic_price' => 'nullable|numeric|min:0',
            'items.*.gauge_diff' => 'nullable|numeric',
            'items.*.final_price' => 'nullable|numeric|min:0',
            'items.*.loading_charge' => 'nullable|numeric|min:0',
            'items.*.insurance' => 'nullable|numeric|min:0',
            'items.*.gst' => 'nullable|numeric|min:0|max:100',
            'items.*.token_amount' => 'nullable|numeric|min:0',
            'items.*.total_amount' => 'nullable|numeric|min:0',
            // 'items.*.payment_term' => 'nullable|string|in:Advance,Next Day,15 Days Later,30 Days Later',
            'items.*.payment_term' => 'nullable|string',
            'items.*.remark' => 'nullable|string|max:2000',
            'attachments' => 'nullable|array',
            'attachments.*.document' => 'nullable|file|max:2048',
            'attachments.*.remark' => 'nullable|string|max:2000',
        ]);

        // Additional validation
        $validator->after(function ($validator) use ($request) {
            if ($validator->errors()->any()) {
                return;
            }

            // Check for duplicate allocation and size pairs
            $pairs = [];
            foreach ($request->input('items', []) as $index => $item) {
                $pair = $item['allocation_id'] . '-' . $item['size_id'];
                if (in_array($pair, $pairs)) {
                    $validator->errors()->add("items.$index.size_id", 'Duplicate allocation and size combination');
                } else {
                    $pairs[] = $pair;
                }
            }

            // Check dispatch quantity threshold
            $order_proposed = [];
            foreach ($request->input('items', []) as $index => $item) {
                $order_id = $item['order_id'];
                if (!isset($order_proposed[$order_id])) {
                    $order_proposed[$order_id] = 0;
                }
                $order_proposed[$order_id] += (float) $item['dispatch_qty'];
            }

            foreach ($order_proposed as $order_id => $proposed) {
                $total_remaining = OrderAllocation::where('order_id', $order_id)->sum('remaining_qty');
                if ($proposed > $total_remaining + 5) {
                    $validator->errors()->add('items', 'Total dispatch quantity exceeds remaining quantity + 5 MT threshold for order #' . $order_id);
                }
            }

            // Verify quantities match DB
            foreach ($request->input('items', []) as $index => $item) {

                // [FIX 1] Optimized query to get 'order' relation at the same time
                $allocation = OrderAllocation::with('order')->find($item['allocation_id']);

                if (!$allocation) {
                    $validator->errors()->add("items.$index.allocation_id", 'Invalid allocation');
                    continue;
                }

                // --- !!! PREVIOUS FIX (Kept) !!! ---
                // [FIX 2] Check if the allocation_id from app actually belongs to the order_id from app
                if ($allocation->order_id != $item['order_id']) {
                    $validator->errors()->add("items.$index.allocation_id", 'This item allocation does not belong to the selected order.');
                    continue; // Skip other checks for this item
                }
                // --- !!! END FIX !!! ---


                // Check 1: Kya parent Order 'approved' ya 'partial' hai?
                if (!in_array($allocation->order->status, ['approved', 'partial dispatch'])) {
                    $validator->errors()->add("items.$index.order_id", "Order #{$allocation->order->order_number} is not approved.");
                }

                // Check 2: Kya yeh specific allocation 'approved' hai?
                if ($allocation->status !== 'approved' && $allocation->status !== 'partially dispatched') {
                    $validator->errors()->add("items.$index.allocation_id", "This item allocation is still pending and cannot be dispatched.");
                }

                // Check 3: Remaining quantity check
                if ((float) $item['remaining_qty'] != $allocation->remaining_qty) {
                    $validator->errors()->add("items.$index.remaining_qty", 'Remaining quantity mismatch');
                }
            }

            // Verify orders belong to user
            $user = $request->user();
            $orderIds = collect($request->input('items'))->pluck('order_id')->unique()->toArray();

            $ordersCount = Order::whereIn('id', $orderIds)
                ->when($user->dealer, function ($q) use ($user) {
                    return $q->where('placed_by_dealer_id', $user->dealer->id);
                })
                ->when($user->distributor, function ($q) use ($user) {
                    return $q->where('placed_by_distributor_id', $user->distributor->id);
                })
                ->count();

            if ($ordersCount !== count($orderIds)) {
                $validator->errors()->add('items', 'Some orders do not belong to you');
            }
        });

        // if ($validator->fails()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Validation failed',
        //         'errors' => $validator->errors()
        //     ], 422);
        // }

        // --- YAHAN BADLAAV KIYA GAYA HAI ---
        if ($validator->fails()) {

            // 1. Saare errors ki ek simple list banayein
            // (Ismein 'after' hook ke errors bhi aa jaayenge)
            $allErrors = $validator->errors()->all();

            // 2. List ko naye format mein badlein
            $formattedErrors = collect($allErrors)->map(function ($message) {
                return ['reason' => $message];
            })->all();

            // 3. Naya response bhejein
            return response()->json([
                'status' => false,
                'message' => 'validations failed',
                'errors' => $formattedErrors
            ], 422);
        }
        // --- BADLAAV KHATAM ---

        try {
            DB::beginTransaction();

            $user = $request->user();
            $validated = $validator->validated();

            // Get user party information
            $userInfo = $this->getUserPartyInfo($user);
            $type = $userInfo['user_type'];
            $partyId = $userInfo['user_id'];

            if (!$type || !$partyId) {
                throw new Exception('User is not associated with a dealer or distributor');
            }

            // // Generate dispatch number
            // $date = $validated['dispatch_date'];
            // $parsedDate = \Carbon\Carbon::parse($date);
            // $year = $parsedDate->month >= 4 ? $parsedDate->year : $parsedDate->year - 1;
            // $nextYear = substr($year + 1, -2);
            // $financialYear = "{$year}-{$nextYear}";
            // $yearStart = \Carbon\Carbon::create($year, 4, 1)->startOfDay();
            // $yearEnd = \Carbon\Carbon::create($year + 1, 3, 31)->endOfDay();
            // $count = Dispatch::whereBetween('dispatch_date', [$yearStart, $yearEnd])->count();
            // $index = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
            // $dispatchNumber = "DO_{$financialYear}_{$index}";


            // Generate dispatch number
            $date = $validated['dispatch_date'];
            $parsedDate = \Carbon\Carbon::parse($date);
            $year = $parsedDate->month >= 4 ? $parsedDate->year : $parsedDate->year - 1;
            $nextYear = substr($year + 1, -2);
            $financialYear = "{$year}-{$nextYear}";
            $yearStart = \Carbon\Carbon::create($year, 4, 1)->startOfDay();
            $yearEnd = \Carbon\Carbon::create($year + 1, 3, 31)->endOfDay();

            // --- YAHAN BADLAV HUA HAI ---

            // 1. Count() ki jagah, uss financial year ka aakhiri (sabse bada) number dhoondein
            // !! Agar aap Soft Deletes use kar rahe hain, to ->withTrashed() add karein !!
            $lastDispatch = Dispatch::whereBetween('dispatch_date', [$yearStart, $yearEnd])
                // Agar soft delete use nahi kar rahe to ise hata dein
                ->orderBy('dispatch_number', 'desc') // Aakhiri number pehle layein
                ->first();

            $nextIndex = 1; // Default 1, agar yeh saal ka pehla order hai

            if ($lastDispatch) {
                // 2. Aakhiri number (jaise "0002") ko dispatch number se nikaalein
                // "DO_2025-26_0002" mein se "0002" nikaalna
                $lastIndexStr = substr($lastDispatch->dispatch_number, -4);

                // 3. Use 1 add karein
                $nextIndex = (int)$lastIndexStr + 1;
            }

            // 4. Naye number ko 4 digit ka banayein (yeh line pehle se thi, bas $count ki jagah $nextIndex)
            $index = str_pad($nextIndex, 4, '0', STR_PAD_LEFT);
            $dispatchNumber = "DO_{$financialYear}_{$index}";

            // --- BADLAV KHATAM ---

            // Handle payment slip
            if ($request->hasFile('payment_slip')) {
                $paymentSlipPath = $request->file('payment_slip')->store('dispatches/payment_slips', 'public');
            }

            // Create dispatch
            $dispatch = Dispatch::create([
                'dispatch_number' => $dispatchNumber,
                'type' => $type,
                'distributor_id' => $type === 'distributor' ? $partyId : null,
                'dealer_id' => $type === 'dealer' ? $partyId : null,
                'recipient_name' => $validated['recipient_name'],
                'recipient_address' => $validated['recipient_address'],
                'recipient_state_id' => $validated['recipient_state_id'],
                'recipient_city_id' => $validated['recipient_city_id'],
                'recipient_pincode' => $validated['recipient_pincode'],
                'consignee_name' => $validated['consignee_name'],
                'consignee_address' => $validated['consignee_address'],
                'consignee_state_id' => $validated['consignee_state_id'],
                'consignee_city_id' => $validated['consignee_city_id'],
                'consignee_pincode' => $validated['consignee_pincode'],
                'consignee_mobile_no' => $validated['consignee_mobile_no'] ?? null,
                'dispatch_date' => $validated['dispatch_date'],

                // 'warehouse_id' => $validated['warehouse_id'], // <-- OLD Line
                'warehouse_id' => 1, // <-- NEW CHANGE: Hardcoded to 1

                'bill_to' => $validated['bill_to'] ?? null,
                'bill_number' => $validated['bill_number'] ?? null,
                'dispatch_out_time' => $validated['dispatch_out_time'] ?? null,
                'payment_slip' => $paymentSlipPath,
                'dispatch_remarks' => $validated['dispatch_remarks'] ?? null,
                // 'transporter_name' => $validated['transporter_name'],
                'transporter_name' => $validated['transporter_name'] ?? null,
                'vehicle_no' => $validated['vehicle_no'] ?? null,
                'driver_name' => $validated['driver_name'] ?? null,
                'driver_mobile_no' => $validated['driver_mobile_no'] ?? null,
                'e_way_bill_no' => $validated['e_way_bill_no'] ?? null,
                'bilty_no' => $validated['bilty_no'] ?? null,
                'transport_remarks' => $validated['transport_remarks'] ?? null,
                'terms_conditions' => $validated['terms_conditions'] ?? null,
                'additional_charges' => (float) ($validated['additional_charges'] ?? 0.00),
                'total_amount' => 0.00,
                'status' => 'pending',
                'created_by' => $user->name,
            ]);

            // Process items
            $grandTotal = (float) ($validated['additional_charges'] ?? 0.00);
            foreach ($validated['items'] as $item) {

                // ---!!! NEW CHANGE: Calculation logic updated to match JS ---
                $finalPrice = (float) $item['basic_price'] + (float) $item['gauge_diff'];

                // Old logic (commented out for reference)
                // $baseTotal = $finalPrice + (float) $item['loading_charge'] + (float) $item['insurance'];
                // $baseTotal *= (float) $item['dispatch_qty'];

                // New logic (matches JS: (A+B+C) * D)
                $baseTotal = ($finalPrice + (float) $item['loading_charge'] + (float) $item['insurance']) * (float) $item['dispatch_qty'];

                $gstAmount = $baseTotal * ((float) $item['gst'] / 100);
                $tokenAmount = (float) ($item['token_amount'] ?? 0.00);
                $itemTotal = $baseTotal + $gstAmount - $tokenAmount;
                // ---!!! END CHANGE ---

                DispatchItem::create([
                    'dispatch_id' => $dispatch->id,
                    'item_id' => 1,
                    'order_id' => $item['order_id'],
                    'allocation_id' => $item['allocation_id'],
                    'order_qty' => (float) $item['order_qty'],
                    'already_disp' => (float) $item['already_disp'],
                    'remaining_qty' => (float) $item['remaining_qty'],
                    'item_name' => $item['item_name'],
                    'size_id' => $item['size_id'],
                    // 'length' => (float) $item['length'],
                    // 'length' => (float) ($item['length'] ?? 12.00),
                    'dispatch_qty' => (float) $item['dispatch_qty'],
                    'basic_price' => (float) $item['basic_price'],
                    'gauge_diff' => (float) $item['gauge_diff'],
                    'final_price' => $finalPrice, // Calculated value
                    'loading_charge' => (float) $item['loading_charge'],
                    'insurance' => (float) $item['insurance'],
                    'gst' => (float) $item['gst'],
                    'token_amount' => $tokenAmount > 0 ? $tokenAmount : null,
                    'total_amount' => $itemTotal, // Calculated value
                    'payment_term' => $item['payment_term'],
                    'status' => 'pending',
                    'remark' => $item['remark'] ?? null,
                ]);

                $grandTotal += $itemTotal;
            }

            // Update dispatch total
            $dispatch->total_amount = $grandTotal;
            $dispatch->save();
            $attachmentPaths = [];

            // Loop over the FILE inputs, not the text inputs
            $attachmentFiles = $request->file('attachments', []);

            if (is_array($attachmentFiles)) {
                Log::info('Attachment files found in request', ['count' => count($attachmentFiles)]);

                foreach ($attachmentFiles as $key => $fileData) {

                    // Get the file from the 'document' key
                    $file = $fileData['document'] ?? null;

                    if ($file && $file->isValid()) {
                        Log::info('Processing attachment', [
                            'key' => $key,
                            'filename' => $file->getClientOriginalName(),
                        ]);

                        $extension = $file->getClientOriginalExtension();
                        $filename = 'attachment_' . $dispatchNumber . '_' . $key . '_' . time() . '_' . Str::random(10) . '.' . $extension;
                        $path = $file->storeAs('dispatches/attachments', $filename, 'public');
                        $attachmentPaths[] = $path;

                        // Get the corresponding remark using the same $key
                        $remark = $request->input("attachments.$key.remark");

                        DispatchAttachment::create([
                            'dispatch_id' => $dispatch->id,
                            'document' => $path,
                            'remark' => $remark ?? null, // Use the remark found
                        ]);
                    } else {
                        Log::warning('Invalid or missing document file for attachment', ['key' => $key]);
                    }
                }
            } else {
                Log::info('No attachments array found in request');
            }
            // --- BADLAAV YAHAN KHATAM ---

            DB::commit();

            $superAdminRole = \Spatie\Permission\Models\Role::where('name', 'Super Admin')->first();

            if ($superAdminRole) {
                // Get all users with Super Admin role
                $superAdmins = \App\Models\User::role($superAdminRole)->get();

                if ($superAdmins->isNotEmpty()) {
                    // Determine the placer (Dealer or Distributor)
                    $placer = null;
                    if ($type === 'dealer' && isset($partyId)) {
                        $placer = \App\Models\Dealer::find($partyId);
                    } elseif ($type === 'distributor' && isset($partyId)) {
                        $placer = \App\Models\Distributor::find($partyId);
                    }

                    // Send notification with both dispatch and placer
                    Notification::send($superAdmins, new \App\Notifications\DispatchCreated($dispatch, $placer));
                }
            } else {
                \Log::warning('Super Admin role not found when sending new dispatch notification.');
            }

            return response()->json([
                'success' => true,
                'message' => 'Dispatch created successfully',
                'data' => [
                    'dispatch_id' => $dispatch->id,
                    'dispatch_number' => $dispatch->dispatch_number,
                    'status' => $dispatch->status,
                    'total_amount' => $dispatch->total_amount
                ]
            ], 201);
            // } catch (Exception $e) {
            //     DB::rollBack();

            //     // Clean up uploaded files
            //     if ($paymentSlipPath) {
            //         Storage::disk('public')->delete($paymentSlipPath);
            //     }
            //     foreach ($attachmentPaths as $path) {
            //         Storage::disk('public')->delete($path);
            //     }

            //     Log::error('Dispatch creation error: ' . $e->getMessage() . ' on line ' . $e->getLine());
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Failed to create dispatch',
            //         'error' => $e->getMessage()
            //     ], 500);
            // }

            // --- YAHAN BADLAAV KIYA GAYA HAI ---
            // (Sirf catch block ko update kiya gaya hai)
        } catch (QueryException $qe) { // DB Errors ko alag se pakda
            DB::rollBack();

            // File cleanup logic waisa hi
            if ($paymentSlipPath) {
                Storage::disk('public')->delete($paymentSlipPath);
            }
            foreach ($attachmentPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            Log::error('Dispatch creation DB error: ' . $qe->getMessage());

            // --- Naya Error Message Logic ---
            $errorMessage = 'A database error occurred. Please check your data.';

            // Check karein agar yeh wahi 'Out of range' error hai
            if ($qe->errorInfo[1] == 1264) {
                // Error 1264 = Out of range value
                $errorMessage = 'Calculation resulted in a value too large for the database. Please check item quantities and prices.';
            }

            // Naye format mein badlein
            $formattedErrors = [
                ['reason' => $errorMessage] // lowercase 'r' (aapke validation format ke according)
            ];

            return response()->json([
                'status' => false,
                'message' => 'Failed to create dispatch', // Message wahi rakha hai
                'errors' => $formattedErrors
            ], 500);
        } catch (Exception $e) { // Doosre sabhi errors ke liye
            DB::rollBack();

            // File cleanup logic waisa hi
            if ($paymentSlipPath) {
                Storage::disk('public')->delete($paymentSlipPath);
            }
            foreach ($attachmentPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            Log::error('Dispatch creation error: ' . $e->getMessage() . ' on line ' . $e->getLine());

            // Naye format mein badlein
            $formattedErrors = [
                ['reason' => $e->getMessage()] // Simple error message
            ];

            return response()->json([
                'status' => false,
                'message' => 'Failed to create dispatch',
                'errors' => $formattedErrors
            ], 500);
        }
        // --- BADLAAV KHATAM ---
    }

    /**
     * Get dispatch list for logged-in user
     */
    // public function index(Request $request)
    // {
    //     try {
    //         $user = $request->user();

    //         // Get user party information
    //         $userInfo = $this->getUserPartyInfo($user);
    //         $userType = $userInfo['user_type'];
    //         $userId = $userInfo['user_id'];

    //         if (!$userType || !$userId) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'User is not associated with a dealer or distributor'
    //             ], 403);
    //         }

    //         $dispatches = Dispatch::with(['dispatchItems', 'warehouse'])
    //             ->when($userType === 'dealer', function ($q) use ($userId) {
    //                 return $q->where('dealer_id', $userId);
    //             })
    //             ->when($userType === 'distributor', function ($q) use ($userId) {
    //                 return $q->where('distributor_id', $userId);
    //             })
    //             ->orderBy('created_at', 'desc')
    //             ->get()
    //             ->map(function ($dispatch) {
    //                 return [
    //                     'id' => $dispatch->id,
    //                     'dispatch_number' => $dispatch->dispatch_number,
    //                     'dispatch_date' => $dispatch->dispatch_date,
    //                     'dispatch_out_time' => $dispatch->dispatch_out_time,
    //                     'recipient_name' => $dispatch->recipient_name,
    //                     'total_amount' => $dispatch->total_amount,
    //                     'total_qty' => $dispatch->dispatchItems->sum('dispatch_qty'),
    //                     'total_items' => $dispatch->dispatchItems->count(),
    //                     'vehicle_no' => $dispatch->vehicle_no,
    //                     'driver_name' => $dispatch->driver_name,
    //                     'warehouse' => $dispatch->warehouse ? $dispatch->warehouse->name : null,
    //                     'status' => $dispatch->status,
    //                     'type' => $dispatch->type
    //                 ];
    //             });

    //         return response()->json([
    //             'success' => true,
    //             'data' => $dispatches
    //         ]);
    //     } catch (Exception $e) {
    //         Log::error('Get dispatches error: ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to fetch dispatches',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // public function index(Request $request)
    // {
    //     try {
    //         $user = $request->user();

    //         // Get user party information
    //         $userInfo = $this->getUserPartyInfo($user);
    //         $userType = $userInfo['user_type'];
    //         $userId = $userInfo['user_id'];

    //         if (!$userType || !$userId) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'User is not associated with a dealer or distributor'
    //             ], 403);
    //         }

    //         // !! YAHAN CHANGES HAIN !!

    //         // Step 1: Query banayein aur paginate karein
    //         $dispatchesPaginator = Dispatch::with(['dispatchItems', 'warehouse'])
    //             ->when($userType === 'dealer', function ($q) use ($userId) {
    //                 return $q->where('dealer_id', $userId);
    //             })
    //             ->when($userType === 'distributor', function ($q) use ($userId) {
    //                 return $q->where('distributor_id', $userId);
    //             })
    //             ->orderBy('created_at', 'desc')
    //             ->paginate(15); // ->get() ki jagah ->paginate(15) use karein

    //         // Step 2: Paginator ke data ko .through() se transform karein (yeh map jaisa hai)
    //         $transformedData = $dispatchesPaginator->through(function ($dispatch) {
    //             return [
    //                 'id' => $dispatch->id,
    //                 'dispatch_number' => $dispatch->dispatch_number,
    //                 'dispatch_date' => $dispatch->dispatch_date,
    //                 'dispatch_out_time' => $dispatch->dispatch_out_time,
    //                 'recipient_name' => $dispatch->recipient_name,
    //                 'total_amount' => $dispatch->total_amount,
    //                 'total_qty' => $dispatch->dispatchItems->sum('dispatch_qty'),
    //                 'total_items' => $dispatch->dispatchItems->count(),
    //                 'vehicle_no' => $dispatch->vehicle_no,
    //                 'driver_name' => $dispatch->driver_name,
    //                 'warehouse' => $dispatch->warehouse ? $dispatch->warehouse->name : null,
    //                 'status' => $dispatch->status,
    //                 'type' => $dispatch->type
    //             ];
    //         });

    //         // Step 3: Paginator ko response mein return karein
    //         // Laravel Paginator ko automatically 'data', 'links', aur 'meta' mein badal dega
    //         return response()->json([
    //             'success' => true,
    //             'data' => $transformedData // Paginator object ko yahaan pass karein
    //         ]);

    //     } catch (Exception $e) {
    //         Log::error('Get dispatches error: ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to fetch dispatches',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function index(Request $request)
    {
        try {
            $user = $request->user();

            // Get user party information
            $userInfo = $this->getUserPartyInfo($user);
            $userType = $userInfo['user_type'];
            $userId = $userInfo['user_id'];

            if (!$userType || !$userId) {
                return response()->json([
                    'status' => false, // 'success' ko 'status' kar diya
                    'message' => 'User is not associated with a dealer or distributor'
                ], 403);
            }

            // ===================================================================
            // !! YAHAN CHANGES HAIN !!
            // ===================================================================

            // Step 1: 'per_page' parameter get karo, default 15 rakho
            $perPage = $request->input('per_page', 10);

            // Step 2: Query banayein
            $query = Dispatch::with(['dispatchItems', 'warehouse'])
                ->when($userType === 'dealer', function ($q) use ($userId) {
                    return $q->where('dealer_id', $userId);
                })
                ->when($userType === 'distributor', function ($q) use ($userId) {
                    return $q->where('distributor_id', $userId);
                });

            // !! NAYA: Optional Search Filter (dispatch_number par) !!
            // if ($request->filled('search')) {
            //     $query->where('dispatch_number', 'like', '%' . $request->input('search') . '%');
            // }

            // --- YAHAN BADLAAV KIYA GAYA HAI (Search Logic) ---
            // Ab yeh 'dispatch_number', 'status', aur 'recipient_name' sabko search karega
            if ($request->filled('search')) {
                $searchTerm = '%' . $request->input('search') . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('dispatch_number', 'like', $searchTerm)
                      ->orWhere('status', 'like', $searchTerm)
                      ->orWhere('recipient_name', 'like', $searchTerm);
                });
            }
            // --- BADLAAV KHATAM ---

            // Step 3: Sort karein
            // $query->orderBy('created_at', 'desc');
            $query->orderBy('created_at', 'desc')->orderBy('id', 'desc');
            // Step 4: Paginate karein
            // $paginator = $query->paginate($perPage);
            $paginator = $query->paginate($perPage, ['*'], 'current_page');

            // Step 5: Paginator ke data ko .through() se transform karein
            // $paginator object ab transformed items ke saath update ho gaya hai
            $paginator->through(function ($dispatch) {
                return [
                    'id' => $dispatch->id,
                    'dispatch_number' => $dispatch->dispatch_number,
                    // 'dispatch_date' => $dispatch->dispatch_date,
                    // 'dispatch_out_time' => $dispatch->dispatch_out_time,
                    'dispatch_date' => $dispatch->dispatch_date->format('d, M Y'),
                    // 'dispatch_out_time' => $dispatch->dispatch_out_time,
                    'dispatch_out_time' => $dispatch->dispatch_out_time ? \Carbon\Carbon::parse($dispatch->dispatch_out_time)->format('h:i A') : null,
                    'recipient_name' => $dispatch->recipient_name,
                    'total_amount' => $dispatch->total_amount,
                    'total_qty' => $dispatch->dispatchItems->sum('dispatch_qty'),
                    'total_items' => $dispatch->dispatchItems->count(),
                    'vehicle_no' => $dispatch->vehicle_no,
                    'driver_name' => $dispatch->driver_name,
                    'warehouse' => $dispatch->warehouse ? $dispatch->warehouse->name : null,
                    'status' => $dispatch->status,
                    'type' => $dispatch->type
                ];
            });

            // ===================================================================
            // !! NAYA: Custom JSON Response (jaisa Orders API mein tha) !!
            // ===================================================================
            return response()->json([
                'status'       => true,
                'data'         => $paginator->items(), // Sirf items ka array
                'current_page' => $paginator->currentPage(),
                'per_page'     => (int) $paginator->perPage(),
                'total'        => $paginator->total(),
                'total_pages'  => $paginator->lastPage()
            ], 200);
        } catch (Exception $e) {
            Log::error('Get dispatches error: ' . $e->getMessage());
            return response()->json([
                'status' => false, // 'success' ko 'status' kar diya
                'message' => 'Failed to fetch dispatches',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single dispatch details
     */
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();

            // Get user party information
            $userInfo = $this->getUserPartyInfo($user);
            $userType = $userInfo['user_type'];
            $userId = $userInfo['user_id'];

            if (!$userType || !$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not associated with a dealer or distributor'
                ], 403);
            }

            $dispatch = Dispatch::with([
                'dispatchItems.order',
                'dispatchItems.size',
                'warehouse',
                'attachments',
                'recipientState',
                'recipientCity',
                'consigneeState',
                'consigneeCity'
            ])
                ->when($userType === 'dealer', function ($q) use ($userId) {
                    return $q->where('dealer_id', $userId);
                })
                ->when($userType === 'distributor', function ($q) use ($userId) {
                    return $q->where('distributor_id', $userId);
                })
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'dispatch' => [
                        'id' => $dispatch->id,
                        'dispatch_number' => $dispatch->dispatch_number,
                        // 'dispatch_date' => $dispatch->dispatch_date,
                        'dispatch_date' => $dispatch->dispatch_date->format('d, M Y'),
                        // 'dispatch_out_time' => $dispatch->dispatch_out_time,
                        'dispatch_out_time' => $dispatch->dispatch_out_time ? \Carbon\Carbon::parse($dispatch->dispatch_out_time)->format('h:i A') : null,
                        'recipient_name' => $dispatch->recipient_name,
                        'recipient_address' => $dispatch->recipient_address,
                        'recipient_state' => $dispatch->recipientState ? $dispatch->recipientState->state : null,
                        'recipient_city' => $dispatch->recipientCity ? $dispatch->recipientCity->name : null,
                        'recipient_pincode' => $dispatch->recipient_pincode,
                        'consignee_name' => $dispatch->consignee_name,
                        'consignee_address' => $dispatch->consignee_address,
                        'consignee_state' => $dispatch->consigneeState ? $dispatch->consigneeState->state : null,
                        'consignee_city' => $dispatch->consigneeCity ? $dispatch->consigneeCity->name : null,
                        'consignee_pincode' => $dispatch->consignee_pincode,
                        'consignee_mobile_no' => $dispatch->consignee_mobile_no,
                        'warehouse' => $dispatch->warehouse ? $dispatch->warehouse->name : null,
                        'bill_to' => $dispatch->bill_to,
                        'bill_number' => $dispatch->bill_number,
                        'payment_slip' => $dispatch->payment_slip ? asset('storage/' . $dispatch->payment_slip) : null,
                        'dispatch_remarks' => $dispatch->dispatch_remarks,
                        'transporter_name' => $dispatch->transporter_name,
                        'vehicle_no' => $dispatch->vehicle_no,
                        'driver_name' => $dispatch->driver_name,
                        'driver_mobile_no' => $dispatch->driver_mobile_no,
                        'e_way_bill_no' => $dispatch->e_way_bill_no,
                        'bilty_no' => $dispatch->bilty_no,
                        'transport_remarks' => $dispatch->transport_remarks,
                        'terms_conditions' => $dispatch->terms_conditions,
                        'additional_charges' => $dispatch->additional_charges,
                        'total_amount' => $dispatch->total_amount,
                        'status' => $dispatch->status,
                        'type' => $dispatch->type
                    ],
                    // 'items' => $dispatch->dispatchItems->map(function ($item) {
                    //     $party = $item->allocation->allocatedTo ?? null;
                    //     $partyName = $party->name ?? 'N/A';
                    //     $partyCode = $party->code ?? 'N/A';
                    // // --- YAHAN BADLAAV KIYA GAYA HAI ---
                    // 'items' => $dispatch->dispatchItems->map(function ($item) use ($userType, $userId) { // <-- 'use' add kiya

                    //     // 1. Party ka data nikaalein (jo humne pehle fix kiya tha)
                    //     $party = $item->allocation->allocatedTo ?? null;
                    //     $partyName = $party->name ?? 'N/A';
                    //     $partyCode = $party->code ?? 'N/A';

                    //     // 2. "Self" check logic add karein
                    //     $isSelf = false;
                    //     if ($item->allocation) { // Check karein ki allocation load hua hai
                    //         if ($item->allocation->allocated_to_type === $userType &&
                    //             $item->allocation->allocated_to_id === $userId) {
                    //             $isSelf = true;
                    //         }
                    //     }

                    //     // 3. Final string banayein
                    //     $partyString = "{$partyName} ({$partyCode})";
                    //     if ($isSelf) {
                    //         $partyString = "Self - " . $partyString;
                    //     }

                    //     // --- BADLAAV KHATAM ---

                    // --- YAHAN BADLAAV KIYA GAYA HAI ---
                    'items' => $dispatch->dispatchItems->map(function ($item) use ($userType, $userId) {

                        // 1. Party ka data nikaalein
                        $party = $item->allocation->allocatedTo ?? null;
                        $partyName = $party->name ?? 'N/A';
                        $partyCode = $party->code ?? 'N/A';
                        $partyString = "{$partyName} ({$partyCode})";

                        // 2. "Self" check logic
                        $isSelf = false;
                        if ($item->allocation) {
                            if (
                                $item->allocation->allocated_to_type === $userType &&
                                $item->allocation->allocated_to_id === $userId
                            ) {
                                $isSelf = true;
                            }
                        }

                        // 3. Final string banayein (YAHAN LOGIC ADD HUA HAI)
                        if ($isSelf) {
                            // Agar "Self" hai
                            $partyString = "Self - " . $partyString;
                        } else {
                            // Agar "Self" nahi hai, toh type (Dealer/Distributor) add karein
                            $partyType = $item->allocation->allocated_to_type ?? null;
                            if ($partyType) {
                                $partyTypeFormatted = ucfirst($partyType); // 'dealer' -> 'Dealer'
                                $partyString = "{$partyTypeFormatted} - " . $partyString;
                            }
                        }
                        // --- BADLAAV KHATAM ---

                        return [
                            'id' => $item->id,
                            'order_number' => $item->order ? $item->order->order_number : null,
                            'item_name' => $item->item_name,
                            'size' => $item->size ? $item->size->size : null,
                            'length' => $item->length,
                            'dispatch_qty' => $item->dispatch_qty,
                            // 'allocation_party' => "{$partyName} ({$partyCode})",
                            'allocation_party' => $partyString,
                            'already_disp' => $item->already_disp,
                            'remaining_qty' => $item->remaining_qty,
                            'order_qty' => $item->order_qty,
                            'basic_price' => $item->basic_price,
                            'gauge_diff' => $item->gauge_diff,
                            'final_price' => $item->final_price,
                            'loading_charge' => $item->loading_charge,
                            'insurance' => $item->insurance,
                            'gst' => $item->gst,
                            'token_amount' => $item->token_amount,
                            'total_amount' => $item->total_amount,
                            'payment_term' => $item->payment_term,
                            'remark' => $item->remark,
                            'status' => $item->status
                        ];
                    }),
                    'attachments' => $dispatch->attachments->map(function ($attachment) {
                        return [
                            'id' => $attachment->id,
                            'document' => asset('storage/' . $attachment->document),
                            'remark' => $attachment->remark
                        ];
                    })
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Get dispatch details error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dispatch details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get allocation details by ID
     */
    public function getAllocationDetails(Request $request, $allocationId)
    {
        try {
            $allocation = OrderAllocation::findOrFail($allocationId);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $allocation->id,
                    'dispatched_qty' => $allocation->dispatched_qty ?? 0,
                    'remaining_qty' => $allocation->remaining_qty,
                    'agreed_basic_price' => $allocation->agreed_basic_price ?? 0,
                    'payment_term' => $allocation->payment_terms ?? '',
                    'loading_charge' => $allocation->loading_charge ?? 265,
                    'insurance_charge' => $allocation->insurance_charge ?? 40,
                    'gst_rate' => $allocation->gst_rate ?? 18,
                    'token_amount' => $allocation->token_amount ?? 0.00,
                    'qty' => $allocation->qty
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Get allocation details error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Allocation not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Check if bill number is unique
     */
    public function checkBillNumber(Request $request)
    {
        $billNumber = $request->query('bill_number');

        if (!$billNumber) {
            return response()->json([
                'success' => false,
                'exists' => false,
                'message' => 'Bill number is required'
            ], 400);
        }

        $exists = Dispatch::where('bill_number', $billNumber)->exists();

        return response()->json([
            'success' => true,
            'exists' => $exists,
            'message' => $exists ? 'Bill number already exists' : 'Bill number is available'
        ]);
    }

    /**
     * Generate dispatch number based on date
     */
    public function generateDispatchNumber(Request $request)
    {
        try {
            $date = $request->input('date');

            if (!$date) {
                return response()->json([
                    'success' => false,
                    'message' => 'Date is required'
                ], 400);
            }

            $parsedDate = \Carbon\Carbon::parse($date);
            $year = $parsedDate->month >= 4 ? $parsedDate->year : $parsedDate->year - 1;
            $nextYear = substr($year + 1, -2);
            $financialYear = "{$year}-{$nextYear}";
            $yearStart = \Carbon\Carbon::create($year, 4, 1)->startOfDay();
            $yearEnd = \Carbon\Carbon::create($year + 1, 3, 31)->endOfDay();
            $count = Dispatch::whereBetween('dispatch_date', [$yearStart, $yearEnd])->count();
            $index = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
            $dispatchNumber = "DO_{$financialYear}_{$index}";

            return response()->json([
                'success' => true,
                'dispatch_number' => $dispatchNumber
            ]);
        } catch (Exception $e) {
            Log::error('Generate dispatch number error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate dispatch number',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get states list
     */
    public function getStates()
    {
        try {
            $states = \App\Models\State::select('id', 'state')->get();

            return response()->json([
                'success' => true,
                'data' => $states
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch states',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cities by state
     */
    public function getCities(Request $request, $stateId)
    {
        try {
            $cities = \App\Models\City::where('state_id', $stateId)
                ->select('id', 'city')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $cities
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cities',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
