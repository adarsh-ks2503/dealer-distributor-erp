<?php

namespace App\Http\Controllers;

use DB;
use App\Models\ItemBasicPrice as ModelsItemBasicPrice;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ItemBasicPriceExport;
use App\Models\State;
use Illuminate\Support\Facades\Auth;
use App\Models\ItemBasicPrice;
use App\Models\BasicPriceHistory;
use Carbon\Carbon;

class ItemBasicPriceController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:ItemBasicPrice-Index', ['only' => ['index']]);
        $this->middleware('permission:ItemBasicPrice-Create', ['only' => ['store']]);
        $this->middleware('permission:ItemBasicPrice-Edit', ['only' => ['update']]);
        $this->middleware('permission:ItemBasicPrice-View', ['only' => ['show']]);
        $this->middleware('permission:ItemBasicPrice-Approve', ['only' => ['approvalRequests', 'approve', 'reject']]);
        $this->middleware('permission:ItemBasicPrice-Delete', ['only' => ['approve', 'reject']]);
        $this->middleware('permission:ItemBasicPrice-Import/Export', ['only' => ['import', 'export']]);
    }
    public function index()
    {
        $itemBasicPrices = ModelsItemBasicPrice::with('itemName', 'stateName')
            ->where('status', 'Approved')
            ->orderBy('approval_date', 'desc')
            ->get();
        $states = State::get();
        $numberOfRequests = ModelsItemBasicPrice::where('status', 'Pending')->count();
        return view('item_master.item_basic_price.index', compact('itemBasicPrices', 'states', 'numberOfRequests'));
    }

    public function rejected(){
        $requests = BasicPriceHistory::with('itemName', 'stateName')
            ->where('status', 'Rejected')
            ->orderBy('updated_at', 'desc')
            ->get();
        $states = State::get();
        return view('item_master.item_basic_price.rejected', compact('requests', 'states'));
    }

    public function store(Request $request)
    {
        // Validate form input
        $validated = $request->validate([
            'item' => 'required|numeric',
            'region' => 'required|numeric',
            'market_basic_price' => 'required|integer|min:0',
            'distributor_basic_price' => 'required|integer|min:0',
            'dealer_basic_price' => 'required|integer|min:0',
        ]);
        // Check for existing pending entry for same item + region
        $exists = ModelsItemBasicPrice::where('item', $validated['item'])
            ->where('region', $validated['region'])
            ->where('status', 'Pending')
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'A pending price already exists for this item and region.');
        }

        ModelsItemBasicPrice::create([
            'item' => $validated['item'],
            'region' => $validated['region'],
            'market_basic_price' => $validated['market_basic_price'],
            'distributor_basic_price' => $validated['distributor_basic_price'],
            'dealer_basic_price' => $validated['dealer_basic_price'],
            'remarks'=>$request->remarks,
            'status' => 'Pending', // default as per migration
            'approval_date' => null,
            'approved_by' => null,
        ]);

        return redirect()->back()->with('success', 'Basic price added successfully.');
    }

    public function update(Request $request, $id)
    {
        // Validate form input
        $validated = $request->validate([
            'item' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'market_basic_price' => 'required|integer|min:0',
            'distributor_basic_price' => 'required|integer|min:0',
            'dealer_basic_price' => 'required|integer|min:0',
        ]);

        // Check if there's already a pending request for this item and region
        $pendingExists = ModelsItemBasicPrice::where('item', $validated['item'])
            ->where('region', $validated['region'])
            ->where('status', 'Pending')
            ->exists();

        if ($pendingExists) {
            return redirect()->back()->with('error', 'A pending basic price request already exists for this item and region. Please approve or reject it before submitting a new one.');
        }

        // Create a new pending request (does NOT affect existing approved rows)
        ModelsItemBasicPrice::create([
            'item' => $validated['item'],
            'region' => $validated['region'],
            'market_basic_price' => $validated['market_basic_price'],
            'distributor_basic_price' => $validated['distributor_basic_price'],
            'dealer_basic_price' => $validated['dealer_basic_price'],
            'status' => 'Pending',
            'remarks' => $request->remarks,
            'approval_date' => null,
            'approved_by' => null,
        ]);

        return redirect()->back()->with('success', 'Basic price change request submitted successfully.');
    }

    public function export()
    {
        return Excel::download(new ItemBasicPriceExport, 'Item-Basic-Price-Template.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        try {
            $import = new \App\Imports\ItemBasicPriceImport;
            Excel::import($import, $request->file('file'));

            if (!empty($import->errors)) {
                return back()
                    ->with('import_errors', $import->errors)
                    ->with('active_tab', 'item-price')
                    ->withInput();
            }

            return back()
                ->with('success', 'File imported successfully. New pending requests created.')
                ->with('active_tab', 'item-price');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'An error occurred during import: ' . $e->getMessage())
                ->with('active_tab', 'item-price')
                ->withInput();
        }
    }

    public function approvalRequests()
    {
        $requests = ModelsItemBasicPrice::with('itemName', 'stateName')->where('status', 'Pending')->orderBy('created_at','DESC')->get();
        return view('item_master.item_basic_price.approvalRequests', compact('requests'));
    }

    public function approve($id)
    {
        $item = ItemBasicPrice::findOrFail($id);

        // Step 1: Move existing approved record (if any) to history
        $existingApproved = ItemBasicPrice::where('item', $item->item)
            ->where('region', $item->region)
            ->where('status', 'Approved')
            ->where('id', '!=', $item->id)
            ->first();

        if ($existingApproved) {
            // Move old approved record to history
            BasicPriceHistory::create([
                'item_id' => $existingApproved->item,
                'state_id' => $existingApproved->region,
                'market_basic_price' => $existingApproved->market_basic_price,
                'distributor_basic_price' => $existingApproved->distributor_basic_price,
                'dealer_basic_price' => $existingApproved->dealer_basic_price,
                'status_changed_at' => $existingApproved->approval_date,
                'status_changed_by' => Auth::user()->name . ' ' . Auth::user()->last_name,
                'status' => 'Approved',
            ]);

            // Delete the old approved record
            $existingApproved->delete();
        }

        // Step 2: Approve the current request
        $item->status = 'Approved';
        $item->approval_date = now();
        $item->approved_by = Auth::user()->name . ' ' . Auth::user()->last_name;
        $item->save();

        return redirect()->back()->with('success', 'Item basic price approved successfully.');
    }

    public function reject($id)
    {
        $item = ItemBasicPrice::findOrFail($id);

        // 1. Move to history with status Rejected
        BasicPriceHistory::create([
            'item_id' => $item->item,
            'state_id' => $item->region,
            'market_basic_price' => $item->market_basic_price,
            'distributor_basic_price' => $item->distributor_basic_price,
            'dealer_basic_price' => $item->dealer_basic_price,
            'status_changed_at' => Carbon::now(),
            'status_changed_by' => Auth::user()->name . ' ' . Auth::user()->last_name,
            'status' => 'Rejected',
        ]);

        // 2. Delete the rejected request from item_basic_prices
        $item->delete();

        return redirect()->back()->with('success', 'Item basic price rejected and logged in history.');
    }

    public function show($id)
    {
        $itemPrice = ModelsItemBasicPrice::with('itemName', 'stateName')->where('status', 'Approved')->findOrFail($id);
        $priceHistory = BasicPriceHistory::with('itemName', 'stateName')
            ->where('state_id', $itemPrice->region)
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at', 'desc')
            ->get();
        return view('item_master.item_basic_price.show', compact('itemPrice', 'priceHistory'));
    }

    public function approveAll(Request $request)
    {
        $requestIds = array_filter(explode(',', $request->input('request_ids', '')));

        if (empty($requestIds)) {
            return redirect()->back()->with('error', 'No pending requests to approve.');
        }

        DB::beginTransaction();
        try {
            foreach ($requestIds as $id) {
                $item = ItemBasicPrice::findOrFail($id);

                // Move existing approved record (if any) to history
                $existingApproved = ItemBasicPrice::where('item', $item->item)
                    ->where('region', $item->region)
                    ->where('status', 'Approved')
                    ->where('id', '!=', $item->id)
                    ->first();

                if ($existingApproved) {
                    BasicPriceHistory::create([
                        'item_id' => $existingApproved->item,
                        'state_id' => $existingApproved->region,
                        'market_basic_price' => $existingApproved->market_basic_price,
                        'distributor_basic_price' => $existingApproved->distributor_basic_price,
                        'dealer_basic_price' => $existingApproved->dealer_basic_price,
                        'status_changed_at' => $existingApproved->approval_date,
                        'status_changed_by' => Auth::user()->name . ' ' . Auth::user()->last_name,
                        'status' => 'Approved',
                    ]);

                    $existingApproved->delete();
                }

                // Approve the current request
                $item->status = 'Approved';
                $item->approval_date = now();
                $item->approved_by = Auth::user()->name . ' ' . Auth::user()->last_name;
                $item->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'All selected item basic price requests approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to approve all requests: ' . $e->getMessage());
        }
    }

    public function rejectAll(Request $request)
    {
        $requestIds = array_filter(explode(',', $request->input('request_ids', '')));

        if (empty($requestIds)) {
            return redirect()->back()->with('error', 'No pending requests to reject.');
        }

        DB::beginTransaction();
        try {
            foreach ($requestIds as $id) {
                $item = ItemBasicPrice::findOrFail($id);

                // Move to history with status Rejected
                BasicPriceHistory::create([
                    'item_id' => $item->item,
                    'state_id' => $item->region,
                    'market_basic_price' => $item->market_basic_price,
                    'distributor_basic_price' => $item->distributor_basic_price,
                    'dealer_basic_price' => $item->dealer_basic_price,
                    'status_changed_at' => Carbon::now(),
                    'status_changed_by' => Auth::user()->name . ' ' . Auth::user()->last_name,
                    'status' => 'Rejected',
                ]);

                // Delete the rejected request
                $item->delete();
            }

            DB::commit();
            return redirect()->back()->with('error', 'All selected item basic price requests rejected and logged in history.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('success', 'Failed to reject all requests: ' . $e->getMessage());
        }
    }
}
