<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\ItemBasicPrice as ModelsItemBasicPrice;
use App\Models\State;
use App\Models\ItemBasicPrice;
use App\Models\BasicPriceHistory;
use Carbon\Carbon;
use App\Models\Dealer;
use App\Models\Distributor;

class ItemBasicPriceController extends Controller
{
    public function index(Request $request)
    {
        // --- Step 1: Get the authenticated user from the API token ---
        $appUser = $request->user();

        // --- Step 2: Find the user's state_id based on their type and code ---
        $stateId = null;

        if ($appUser->type === 'dealer') {
            // Find the dealer's profile using their unique code and get their state_id
            $stateId = Dealer::where('code', $appUser->code)->value('state_id');
        } elseif ($appUser->type === 'distributor') {
            // Find the distributor's profile and get their state_id
            $stateId = Distributor::where('code', $appUser->code)->value('state_id');
        }

        // --- Step 3: If the user has no state, return an empty list ---
        if (!$stateId) {
            return response()->json(['data' => []], 200);
        }

        // --- Step 4: Fetch the prices, filtering by the user's state ---
        $itemBasicPrices = ItemBasicPrice::where('status', 'Approved')
            // THE FIX: Only get prices where the 'region' matches the user's state ID
            ->where('region', $stateId)
            ->orderBy('approval_date', 'desc')
            ->select('market_basic_price', 'distributor_basic_price', 'dealer_basic_price')
            ->get();

        return response()->json(['data' => $itemBasicPrices], 200);
    }

    /**
     * List all PENDING item basic price requests for approval.
     */
    public function approvalRequests()
    {
        $requests = ItemBasicPrice::with('itemName', 'stateName')->where('status', 'Pending')->get();
        return response()->json(['data' => $requests], 200);
    }

    /**
     * Submit a NEW item basic price request. It will have a 'Pending' status.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item' => [
                'required',
                'numeric',
                // Check karo ki is item+region ke liye pehle se koi pending request toh nahi hai
                Rule::unique('item_basic_prices')->where(function ($query) use ($request) {
                    return $query->where('region', $request->region)->where('status', 'Pending');
                }),
            ],
            'region' => 'required|numeric',
            'market_basic_price' => 'required|integer|min:0',
            'distributor_basic_price' => 'required|integer|min:0',
            'dealer_basic_price' => 'required|integer|min:0',
            'remarks' => 'nullable|string',
        ], [
            'item.unique' => 'A pending price already exists for this item and region.'
        ]);

        $itemPrice = ItemBasicPrice::create([
            'item' => $validated['item'],
            'region' => $validated['region'],
            'market_basic_price' => $validated['market_basic_price'],
            'distributor_basic_price' => $validated['distributor_basic_price'],
            'dealer_basic_price' => $validated['dealer_basic_price'],
            'remarks' => $validated['remarks'] ?? null,
            'status' => 'Pending',
        ]);

        return response()->json([
            'message' => 'Basic price request submitted successfully and is pending approval.',
            'data' => $itemPrice
        ], 201);
    }

    /**
     * Show a single APPROVED price and its recent history.
     */
    public function show(ItemBasicPrice $itemBasicPrice)
    {
        // Ensure we only show approved prices via this endpoint
        if ($itemBasicPrice->status !== 'Approved') {
            return response()->json(['message' => 'Price not found or not approved.'], 404);
        }

        $priceHistory = BasicPriceHistory::with('itemName', 'stateName')
            ->where('item_id', $itemBasicPrice->item)
            ->where('state_id', $itemBasicPrice->region)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'current_price' => $itemBasicPrice,
            'history' => $priceHistory
        ], 200);
    }

    /**
     * Approve a PENDING price request.
     */
    public function approve(Request $request, ItemBasicPrice $itemBasicPrice)
    {
        if ($itemBasicPrice->status !== 'Pending') {
            return response()->json(['message' => 'This request is not in pending state.'], 409); // 409 Conflict
        }

        // Step 1: Purane approved record ko history mein move karo (agar hai toh)
        $existingApproved = ItemBasicPrice::where('item', $itemBasicPrice->item)
            ->where('region', $itemBasicPrice->region)
            ->where('status', 'Approved')
            ->first();

        if ($existingApproved) {
            BasicPriceHistory::create([
                'item_id' => $existingApproved->item,
                'state_id' => $existingApproved->region,
                'market_basic_price' => $existingApproved->market_basic_price,
                'distributor_basic_price' => $existingApproved->distributor_basic_price,
                'dealer_basic_price' => $existingApproved->dealer_basic_price,
                'status_changed_at' => $existingApproved->approval_date,
                'status_changed_by' => $request->user()->name . ' ' . $request->user()->last_name,
                'status' => 'Archived', // Old approved becomes Archived
            ]);
            $existingApproved->delete();
        }

        // Step 2: Naye request ko approve karo
        $itemBasicPrice->status = 'Approved';
        $itemBasicPrice->approval_date = now();
        $itemBasicPrice->approved_by = $request->user()->name . ' ' . $request->user()->last_name;
        $itemBasicPrice->save();

        return response()->json(['message' => 'Item basic price approved successfully.', 'data' => $itemBasicPrice], 200);
    }

    /**
     * Reject a PENDING price request.
     */
    public function reject(Request $request, ItemBasicPrice $itemBasicPrice)
    {
        if ($itemBasicPrice->status !== 'Pending') {
            return response()->json(['message' => 'This request is not in pending state.'], 409);
        }

        // Step 1: Rejected request ko history mein daalo
        BasicPriceHistory::create([
            'item_id' => $itemBasicPrice->item,
            'state_id' => $itemBasicPrice->region,
            'market_basic_price' => $itemBasicPrice->market_basic_price,
            'distributor_basic_price' => $itemBasicPrice->distributor_basic_price,
            'dealer_basic_price' => $itemBasicPrice->dealer_basic_price,
            'status_changed_at' => now(),
            'status_changed_by' => $request->user()->name . ' ' . $request->user()->last_name,
            'status' => 'Rejected',
        ]);

        // Step 2: Pending request ko delete kar do
        $itemBasicPrice->delete();

        return response()->json(['message' => 'Item basic price rejected and logged in history.'], 200);
    }
}
