<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Distributor;
use App\Models\Dealer;
use App\Models\DistributorTeam;
use Illuminate\Support\Facades\DB;
use App\Models\DistributorTeamDealer;

class DistributorTeamController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:DistributorsTeam-Index', ['only' => ['index']]);
        $this->middleware('permission:DistributorsTeam-Create', ['only' => ['create', 'store']]);
        $this->middleware('permission:DistributorsTeam-Edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:DistributorsTeam-View', ['only' => ['show']]);
        $this->middleware('permission:DistributorsTeam-Suspend', ['only' => ['suspend']]);
    }

   public function index()
    {
        $teams = DistributorTeam::with(['distributor.state'])->orderBy('created_at','DESC')->get();

        foreach ($teams as $team) {
            $activeDealers = Dealer::join('distributor_team_dealers as pivot', 'dealers.id', '=', 'pivot.dealer_id')
                ->where('pivot.distributor_team_id', $team->id)
                ->where('pivot.status', 'Active')
                ->where('dealers.status', 'active')
                ->whereNotNull('dealers.distributor_id') // ← Critical
                ->count();

            $activeLimit = Dealer::join('distributor_team_dealers as pivot', 'dealers.id', '=', 'pivot.dealer_id')
                ->where('pivot.distributor_team_id', $team->id)
                ->where('pivot.status', 'Active')
                ->where('dealers.status', 'active')
                ->whereNotNull('dealers.distributor_id') // ← Critical
                ->sum('dealers.order_limit');

            $totalLimit = $activeLimit + ($team->distributor?->order_limit ?? 0);

            $team->active_dealer_count = $activeDealers;
            $team->active_total_order_limit = $totalLimit;
        }

        return view('dealers_distributors.distributor_team.index', compact('teams'));
    }

    public function create()
    {
        $distributors = Distributor::with(['state', 'city', 'contactPersons'])
            ->where('status', 'Active')
            ->where('has_team', 'No')
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('dealers_distributors.distributor_team.create', compact('distributors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'distributor_id' => 'required|exists:distributors,id',
            'dealers' => 'required|array|min:1',
            'dealers.*' => 'exists:dealers,id',
            'remark' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $distributor = Distributor::findOrFail($request->distributor_id);

            $dealers = Dealer::whereIn('id', $request->dealers)
                    ->whereNull('distributor_id')
                    ->where('status', 'active') // ← Add
                    ->get();

            if ($dealers->count() !== count($request->dealers)) {
                return back()->withErrors(['dealers' => 'One or more selected dealers are already assigned.'])->withInput();
            }

            $dealerOrderLimit = $dealers->sum('order_limit');
            $totalOrderLimit = $dealerOrderLimit + $distributor->order_limit;

            $distributor->allowed_order_limit = $totalOrderLimit;
            $distributor->has_team = 'Yes';
            $distributor->save();

            $team = DistributorTeam::create([
                'distributor_id'    => $distributor->id,
                'no_of_dealers'     => $dealers->count(),
                'total_order_limit' => $totalOrderLimit,
                'ordered_quantity'  => 0,
                'remarks'           => $request->remark,
            ]);

            $team->dealers()->attach($dealers->pluck('id')->toArray(), ['status' => 'Active']);

            Dealer::whereIn('id', $dealers->pluck('id'))
                ->update(['distributor_id' => $distributor->id]);

            DB::commit();

            return redirect()->route('distributor_team.index')
                ->with('success', 'Distributor team created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $team = DistributorTeam::with(['distributor.state', 'distributor.city'])->findOrFail($id);

        // Current Dealers: BOTH pivot AND dealer active
        $currentDealers = Dealer::join('distributor_team_dealers as pivot', 'dealers.id', '=', 'pivot.dealer_id')
            ->where('pivot.distributor_team_id', $id)
            ->where('pivot.status', 'Active')
            ->where('dealers.status', 'active')
            ->whereNotNull('dealers.distributor_id') // ← Add
            ->with('state')
            ->select(['dealers.*', 'pivot.status as pivot_status'])
            ->get();

        // Past Dealers: EITHER pivot inactive OR dealer inactive
        $pastDealers = Dealer::join('distributor_team_dealers as pivot', 'dealers.id', '=', 'pivot.dealer_id')
            ->where('pivot.distributor_team_id', $id)
            ->where(function($q) {
                $q->where('pivot.status', '!=', 'Active')
                ->orWhere('dealers.status', '!=', 'active');
            })
            ->with('state')
            ->select([
                'dealers.*',
                'pivot.status as pivot_status'
            ])
            ->get();

        return view('dealers_distributors.distributor_team.show', compact('team', 'currentDealers', 'pastDealers'));
    }

    public function getDealersInModal($id)
    {
        $activeDealers = Dealer::join('distributor_team_dealers as pivot', 'dealers.id', '=', 'pivot.dealer_id')
            ->where('pivot.distributor_team_id', $id)
            ->where('pivot.status', 'Active')  // ← Ensure this
            ->where('dealers.status', 'active')  // ← Ensure this
            ->whereNotNull('dealers.distributor_id')
            ->select([
                'dealers.id',
                'dealers.code',
                'dealers.name',
                'dealers.order_limit',
                'dealers.state_id'
            ])
            ->with('state')
            ->get()
            ->map(fn($d) => [
                'id' => $d->id,
                'code' => $d->code,
                'name' => $d->name,
                'state' => $d->state ? ['state' => $d->state->state] : null,
                'order_limit' => $d->order_limit,
            ]);

        return response()->json(['dealers' => $activeDealers]);
    }

    public function edit($id)
    {
        $team = DistributorTeam::with(['distributor', 'dealers'])->findOrFail($id);

        $distributors = Distributor::with(['state', 'city', 'contactPersons'])
            ->where('status', 'Active')
            ->orderBy('created_at', 'DESC')
            ->get();

        $selectedDealers = $team->dealers;

        return view('dealers_distributors.distributor_team.edit', [
            'team' => $team,
            'distributors' => $distributors,
            'selectedDealers' => $selectedDealers
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'distributor_id' => 'required|exists:distributors,id',
            'dealers' => 'required|array|min:1',
            'dealers.*' => 'exists:dealers,id',
            'remark' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $team = DistributorTeam::with(['dealers'])->findOrFail($id);
            $distributor = Distributor::findOrFail($request->distributor_id);
            $selectedDealerIds = collect($request->dealers)->map(fn($id) => (int)$id)->toArray();

            $existingDealersWithStatus = $team->dealers()
                ->withPivot('status')
                ->get()
                ->pluck('pivot.status', 'id')
                ->toArray();

            $existingDealerIds = array_keys($existingDealersWithStatus);

            $dealersToAdd = [];
            $dealersToReactivate = [];
            $dealersToRemove = [];

            foreach ($selectedDealerIds as $dealerId) {
                if (!in_array($dealerId, $existingDealerIds)) {
                    $dealersToAdd[] = $dealerId;
                } elseif ($existingDealersWithStatus[$dealerId] === 'Inactive') {
                    $dealersToReactivate[] = $dealerId;
                }
            }

            $dealersToRemove = array_diff($existingDealerIds, $selectedDealerIds);

            // In update() method, after $dealersToAdd
            $newDealerModels = Dealer::whereIn('id', $dealersToAdd)
                ->whereNull('distributor_id')
                ->where('status', 'active') // ← Add this
                ->get();

            if ($newDealerModels->count() !== count($dealersToAdd)) {
                return back()->withErrors(['dealers' => 'One or more selected dealers are already assigned or inactive.'])->withInput();
            }

            foreach ($newDealerModels as $dealer) {
                $team->dealers()->attach($dealer->id, ['status' => 'Active']);
                $dealer->distributor_id = $distributor->id;
                $dealer->save();
            }

            foreach ($dealersToReactivate as $dealerId) {
                $team->dealers()->updateExistingPivot($dealerId, ['status' => 'Active']);
                Dealer::where('id', $dealerId)->update(['distributor_id' => $distributor->id]); // ← Sync
            }

            foreach ($dealersToRemove as $dealerId) {
                $team->dealers()->updateExistingPivot($dealerId, ['status' => 'Inactive']);
                Dealer::where('id', $dealerId)->update(['distributor_id' => null]); // ← Sync
            }

            // Recalculate active dealers
            $activeDealers = $team->dealers()->wherePivot('status', 'Active')->get();
            $dealerOrderLimit = $activeDealers->sum('order_limit');
            $totalOrderLimit = $dealerOrderLimit + $distributor->order_limit;

            $team->update([
                'distributor_id' => $distributor->id,
                'no_of_dealers' => $activeDealers->count(),
                'total_order_limit' => $totalOrderLimit,
                'remarks' => $request->remark,
            ]);

            $distributor->allowed_order_limit = $totalOrderLimit;
            $distributor->save();

            DB::commit();

            return redirect()->route('distributor_team.index')
                ->with('success', 'Distributor team updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()])->withInput();
        }
    }

    public function suspend($teamId)
    {
        DB::beginTransaction();

        try {
            $team = DistributorTeam::with('dealers')->findOrFail($teamId);

            $team->status = 'suspended';
            $team->save();

            DistributorTeamDealer::where('distributor_team_id', $teamId)
                ->where('status', 'Active')
                ->update(['status' => 'Suspended']);

            Dealer::whereIn('id', $team->dealers->pluck('id'))
                ->update(['distributor_id' => null]);

            $distributor = Distributor::findOrFail($team->distributor_id);
            $distributor->allowed_order_limit = $distributor->order_limit;
            $distributor->has_team = 'No';
            $distributor->save();

            DB::commit();

            return redirect()
                ->route('distributor_team.index')
                ->with('success', 'Team suspended. Distributor limits reset.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to suspend: ' . $e->getMessage()]);
        }
    }

    public function getDealersByDistributor($id)
    {
        $distributor = Distributor::findOrFail($id);

        $dealers = Dealer::where('state_id', $distributor->state_id)
            ->whereNull('distributor_id')
            ->where('status', 'active')
            ->get(['id', 'name']);

        return response()->json($dealers);
    }

    public function getDistributor($id)
    {
        $distributor = Distributor::findOrFail($id);
        return response()->json([
            'order_limit' => $distributor->order_limit,
        ]);
    }

    public function getDealers(Request $request)
    {
        $dealerIds = $request->input('dealer_ids', []);
        $dealers = Dealer::whereIn('id', $dealerIds)->get(['id', 'order_limit']);
        return response()->json($dealers);
    }
}
