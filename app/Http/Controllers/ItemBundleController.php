<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemBundle;
use App\Models\ItemSize;

class ItemBundleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ItemBundle-Index', ['only' => ['index']]);
        $this->middleware('permission:ItemBundle-Create', ['only' => ['store']]);
        $this->middleware('permission:ItemBundle-Edit', ['only' => ['update']]);
    }
    public function index()
    {
        $bundles = ItemBundle::with('itemName', 'sizeDetail')->where('status', 'Active')->get();
        $sizes = ItemSize::get();
        return view('item_master.item_bundle.index', compact('bundles', 'sizes'));
    }

    public function store(Request $request)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'item_id'       => 'required|exists:items,id',
            'bundle_name'   => 'required|string|max:255',
            'size_id'       => 'required|exists:item_sizes,id',
            'pieces'        => 'required|integer|min:1',
            'initial_range' => 'required|integer|min:0',
            'end_range'     => 'required|integer|gt:initial_range',
            'status'        => 'nullable|in:Active,Inactive',
            'remarks'       => 'nullable|string|max:255',
        ]);

        // Default status if not provided
        $validated['status'] = $validated['status'] ?? 'Active';

        // Create the bundle
        ItemBundle::create($validated);

        return redirect()->back()->with('success', 'Item bundle created successfully.');
    }

    public function update(Request $request, $id)
    {
        // Fetch the bundle to update
        $bundle = ItemBundle::findOrFail($id);

        // Validate input
        $validated = $request->validate([
            'item_id'       => 'required|exists:items,id',
            'bundle_name'   => 'required|string|max:255',
            'size_id'       => 'required|exists:item_sizes,id',
            'pieces'        => 'required|integer|min:1',
            'initial_range' => 'required|integer|min:0',
            'end_range'     => 'required|integer|gt:initial_range',
            'status'        => 'nullable|in:Active,Inactive',
            'remarks'       => 'nullable|string|max:255',
        ]);

        // Default status if not provided
        $validated['status'] = $validated['status'] ?? 'Active';

        // Update the bundle
        $bundle->update($validated);

        return redirect()->back()->with('success', 'Item bundle updated successfully.');
    }
}
