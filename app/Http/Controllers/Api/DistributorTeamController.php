<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Distributor;
use App\Models\DistributorTeam;
use App\Models\Dealer;
use Illuminate\Support\Facades\DB;

class DistributorTeamController extends Controller
{
    public function getAvailableDealers(Distributor $distributor)
    {
        // Find dealers that match the distributor's state and city, and are unassigned.
        $availableDealers = Dealer::where('state_id', $distributor->state_id)
            ->where('city_id', $distributor->city_id)
            ->whereNull('distributor_id')
            ->get(['id', 'name', 'order_limit']); // Only send necessary data

        return response()->json([
            'status' => true,
            'data' => $availableDealers
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'distributor_id' => 'required|exists:distributors,id',
            'dealers' => 'required|array|min:1',
            'dealers.*' => 'exists:dealers,id',
            'remarks' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $distributor = Distributor::findOrFail($validated['distributor_id']);

            // --- VALIDATION LOGIC ---
            // 1. Verify all selected dealers are still unassigned
            $assignedDealers = Dealer::whereIn('id', $validated['dealers'])
                ->whereNotNull('distributor_id')
                ->count();

            if ($assignedDealers > 0) {
                return response()->json(['status' => false, 'message' => 'One or more selected dealers have already been assigned to a team.'], 422);
            }

            // 2. Verify all dealers are in the same location as the distributor
            $dealers = Dealer::whereIn('id', $validated['dealers'])->get();
            foreach ($dealers as $dealer) {
                if ($dealer->state_id != $distributor->state_id || $dealer->city_id != $distributor->city_id) {
                    return response()->json(['status' => false, 'message' => "Dealer {$dealer->name} is not in the same location as the distributor."], 422);
                }
            }

            // --- CALCULATION & CREATION ---
            $totalDealerLimit = $dealers->sum('order_limit');
            $totalOrderLimit = $distributor->order_limit + $totalDealerLimit;

            // Update distributor's own allowed limit
            $distributor->allowed_order_limit = $totalOrderLimit;
            $distributor->save();

            // Create the distributor team record
            $team = DistributorTeam::create([
                'distributor_id' => $distributor->id,
                'no_of_dealers' => $dealers->count(),
                'total_order_limit' => $totalOrderLimit,
                'ordered_quantity' => 0,
                'remarks' => $validated['remarks'] ?? null,
                'status' => 'Active',
            ]);

            // Attach dealers to the team and update their distributor_id
            $dealerIds = $dealers->pluck('id')->toArray();
            $team->dealers()->attach($dealerIds);
            Dealer::whereIn('id', $dealerIds)->update(['distributor_id' => $distributor->id]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Distributor team created successfully.',
                'data' => $team->load('dealers')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }


    public function update(Request $request, DistributorTeam $team)
{
    // Validation is simpler: we just need the new list of dealers.
    // The 'dealers' array can be empty if the user wants to remove all dealers.
    $validated = $request->validate([
        'dealers' => 'required|array',
        'dealers.*' => 'exists:dealers,id',
        'remarks' => 'nullable|string|max:1000',
    ]);

    DB::beginTransaction();
    try {
        $distributor = $team->distributor; // Get the distributor associated with this team

        // --- 1. DETACH ALL OLD DEALERS ---
        // Get the IDs of dealers currently in the team
        // $oldDealerIds = $team->dealers()->pluck('id')->toArray();
        $oldDealerIds = $team->dealers()->pluck('dealers.id')->toArray();
        // Remove their association from the team
        $team->dealers()->detach();
        // Reset their distributor_id back to NULL
        Dealer::whereIn('id', $oldDealerIds)->update(['distributor_id' => null]);
        
        // --- 2. VALIDATE AND PREPARE NEW DEALERS ---
        $newDealerIds = $validated['dealers'];
        $dealers = Dealer::whereIn('id', $newDealerIds)->get();

        // Verify all newly selected dealers are unassigned OR belonged to this team before
        foreach ($dealers as $dealer) {
            if ($dealer->distributor_id !== null) {
                // This error prevents you from stealing a dealer from another team
                return response()->json(['status' => false, 'message' => "Dealer {$dealer->name} is already assigned to another team."], 422);
            }
            if ($dealer->state_id != $distributor->state_id || $dealer->city_id != $distributor->city_id) {
                return response()->json(['status' => false, 'message' => "Dealer {$dealer->name} is not in the same location as the distributor."], 422);
            }
        }
        
        // --- 3. RE-CALCULATE LIMITS AND UPDATE TEAM ---
        $totalDealerLimit = $dealers->sum('order_limit');
        $totalOrderLimit = $distributor->order_limit + $totalDealerLimit;

        // Update the distributor's allowed_order_limit
        $distributor->allowed_order_limit = $totalOrderLimit;
        $distributor->save();
        
        // Update the team record with new values
        $team->update([
            'no_of_dealers' => $dealers->count(),
            'total_order_limit' => $totalOrderLimit,
            'remarks' => $validated['remarks'] ?? $team->remarks, // Keep old remarks if new one isn't provided
        ]);
        
        // --- 4. ATTACH NEW DEALERS ---
        if ($dealers->isNotEmpty()) {
            $team->dealers()->attach($newDealerIds);
            Dealer::whereIn('id', $newDealerIds)->update(['distributor_id' => $distributor->id]);
        }

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Distributor team updated successfully.',
            'data' => $team->load('dealers')
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['status' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
    }
}
}
