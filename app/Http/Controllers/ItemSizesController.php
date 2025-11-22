<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemSize;
use App\Models\ItemSizesHistory;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\DispatchItem;

class ItemSizesController extends Controller
{
    public function __construct()
    {
        // Permission for viewing the main lists of items
        $this->middleware('permission:ItemSize-Index', ['only' => ['index']]);

        $this->middleware('permission:ItemName-Index', ['only' => ['items']]);

        // Permissions for creating and editing (submitting requests)
        $this->middleware('permission:ItemSize-Create', ['only' => ['add']]);
        $this->middleware('permission:ItemSize-Edit', ['only' => ['update']]);

        // Permission for viewing a single item's details and history
        $this->middleware('permission:ItemSize-View', ['only' => ['show']]);

        // Permissions for managing the active/inactive status
        $this->middleware('permission:ItemSize-InActive', ['only' => ['markInactive', 'inactiveItemSizes']]);
        $this->middleware('permission:ItemSize-Active', ['only' => ['activeItemSizes']]);

        // Permission for the entire approval workflow
        $this->middleware('permission:ItemSize-Approve', ['only' => ['approvalRequests', 'approve', 'reject']]);
    }

    public function items()
    {
        $itemSizes = ItemSize::with('itemName')
            ->where('status', 'Active')
            ->orderBy('size')
            ->get();
        return view('item_master.items.index', compact('itemSizes'));
    }
    public function index()
    {
        $itemSizes = ItemSize::with('itemName')
            ->where('status', 'Active')
            ->orderBy('size')
            ->get();
        $noOfRequests = ItemSize::where('status', 'Pending')->count();

        return view('item_master.item_sizes.index', compact('itemSizes', 'noOfRequests'));
    }

    public function add(Request $request)
    {
        // Validate form data
        $validated = $request->validate([
            'item' => 'required|string|max:255',
            'hsn_code' => 'nullable|string|max:100',
            'size' => [
                'required',
                'numeric',
                Rule::unique('item_sizes')->where(function ($query) use ($request) {
                    return $query->where('item', $request->item);
                }),
            ],
            'rate' => 'required|numeric',
            'remarks' => 'nullable|string|max:255',
        ]);

        // Check for existing pending entry for same item + region
        $exists = ItemSize::where('item', $validated['item'])
            ->where('status', 'Pending')
            ->where('size', $validated['size'])
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'A pending request already exists for this item size');
        }

        // Add default status if applicable
        $validated['status'] = 'Pending'; // or whatever logic you want

        // Create the ItemSize
        ItemSize::create($validated);

        // Redirect with success message
        return redirect()->back()->with('success', 'Item size Request added successfully.');
    }

    public function update(Request $request, $id)
    {
        // Validate
        $validated = $request->validate([
            'item' => 'required|string|max:255',
            'hsn_code' => 'nullable|string|max:100',
            'size' => [
                'required',
                'numeric',
                Rule::unique('item_sizes')->where(function ($query) use ($request) {
                    return $query->where('item', $request->item);
                })->ignore($id),
            ],
            'rate' => 'required|numeric',
            'remarks' => 'nullable|string|max:255',
        ]);

        // Check if there's already a pending request for this item and region
        $pendingExists = ItemSize::where('item', $validated['item'])
            ->where('size',$validated['size'])
            ->where('status', 'Pending')
            ->exists();

        if ($pendingExists) {
            return redirect()->back()->with('error', 'A size-rate updation request already exists for this item size. Please approve or reject it before submitting a new one.');
        }

        // Add default status if applicable
        $validated['status'] = 'Pending'; // or whatever logic you want

        // Create the ItemSize
        ItemSize::create($validated);

        return redirect()->back()->with('success', 'Item size update requested successfully.');
    }

    public function show($id)
    {
        $itemSize = ItemSize::with('itemName')->findOrFail($id);
        $priceHistory = ItemSizesHistory::with('itemName')->where('size', $itemSize->size)->orderBy('created_at', 'desc')->get();
        return view('item_master.item_sizes.show', compact("itemSize", 'priceHistory'));
    }

    public function markInactive($id)
    {
        $itemSize = ItemSize::with('itemName')->findOrFail($id);

        $dispatchItems = DispatchItem::where('size_id',$id)->where('status','pending')->get();

        if($dispatchItems->isNotEmpty()){
            return back()->with('error','Cannot Inactivate this size as there are pending dispatch(es) for this size!');
        }

        // Update status
        $itemSize->status = 'Inactive';
        $itemSize->save();

        return redirect()->back()->with('success', 'Item marked as inactive successfully.');
    }

    public function inactiveItemSizes()
    {
        $itemSizes = ItemSize::with('itemName')->where('status', 'Inactive')->orderBy('size')->get();
        return view('item_master.item_sizes.inactive_items', compact('itemSizes'));
    }

    public function activeItemSizes($id)
    {
        $itemSize = ItemSize::with('itemName')->findOrFail($id);

        // Update status
        $itemSize->status = 'Active';
        $itemSize->save();

        return redirect()->route('itemSizes.index')->with('success', 'Item marked as Active successfully.');
    }

    public function approvalRequests()
    {
        $requests = ItemSize::with('itemName')->where('status', 'Pending')->get();
        $rejected = ItemSizesHistory::with('itemName')->where('status','Rejected')->orderBy('created_at','desc')->get();
        return view('item_master.item_sizes.approvalRequests', compact('requests','rejected'));
    }

    public function approve($id)
    {
        $newItem = ItemSize::findOrFail($id);

        // Find the currently active item (to be replaced)
        $existingApproved = ItemSize::where('item', $newItem->item)
            ->where('status', 'Active')
            ->where('id', '!=', $newItem->id)
            ->where('size', $newItem->size)
            ->first();

        if ($existingApproved) {
            // Step 1: Move the existing approved record to history
            ItemSizesHistory::create([
                'item'          => $existingApproved->item,
                'size'          => $existingApproved->size,
                'rate'          => $existingApproved->rate,
                'hsn_code'      => $existingApproved->hsn_code,
                'remarks'       => $existingApproved->remarks,
                'approval_time' => $existingApproved->approval_time ?? now(),
                'approved_by'   => $existingApproved->approved_by ?? 'Super Admin',
                'status'        => 'Approved',
            ]);

            // Step 2: Overwrite existingApproved with newItem’s data
            $existingApproved->size          = $newItem->size;
            $existingApproved->rate          = $newItem->rate;
            $existingApproved->hsn_code      = $newItem->hsn_code;
            $existingApproved->remarks       = $newItem->remarks;
            $existingApproved->approval_time = now();
            $existingApproved->approved_by   = Auth::user()->name . ' ' . Auth::user()->last_name;
            $existingApproved->status        = 'Active';
            $existingApproved->save();

            // Step 3: Delete the new pending item after copying
            $newItem->delete();
        } else {
            // No existing active record, approve as-is
            $newItem->status        = 'Active';
            $newItem->approval_time = now();
            $newItem->approved_by   = Auth::user()->name . ' ' . Auth::user()->last_name;
            $newItem->save();
        }

        return redirect()->back()->with('success', 'Item basic price approved successfully.');
    }

    public function reject($id)
    {
        $item = ItemSize::findOrFail($id);

        // 1. Move to history with status Rejected
        ItemSizesHistory::create([
            'item' => $item->item,
            'size' => $item->size,
            'rate' => $item->rate,
            'hsn_code' => $item->hsn_code,
            'remarks' => $item->remarks,
            'approval_time' => $item->approval_time ? $item->approval_time : now(),
            'approved_by' => Auth::user()->name . ' ' . Auth::user()->last_name,
            'status' => 'Rejected',
        ]);

        // 2. Delete the rejected request from item_basic_prices
        $item->delete();

        return redirect()->back()->with('error', 'Item basic price rejected and logged in history.');
    }
}
