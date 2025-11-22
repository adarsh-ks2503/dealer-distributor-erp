<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\State;
use App\Models\Warehouse;
use App\Models\City;

class WarehouseController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:Warehouse-Index', ['only' => ['index']]);
        $this->middleware('permission:Warehouse-Create', ['only' => ['create', 'store']]);
        // $this->middleware('permission:Warehouse-View', ['only' => ['show']]);
        // $this->middleware('permission:Warehouse-Edit', ['only' => ['edit', 'update']]);
        // $this->middleware('permission:Warehouse-Delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        $warehouses = Warehouse::with(['state', 'city'])->get();
        return view('settings.warehouse.index', compact('warehouses'));
    }

    public function create()
    {
        $states = State::all();
        return view('settings.warehouse.create', compact('states'));
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'warehouse_name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15',
            'pan' => 'nullable|string|max:10',
            'gst_no' => 'nullable|string|max:15',
            'state_id' => 'required|exists:states,id',  // Ensure the state_id exists in the states table
            'city_id' => 'required|exists:cities,id',  // Ensure the city_id exists in the cities table
            'pincode' => 'required|integer',
            'address' => 'required|string|max:500',
        ]);

        // Create a new warehouse entry
        Warehouse::create([
            'name' => $validated['warehouse_name'],
            'mobile_no' => $validated['mobile'],
            'pan_no' => $validated['pan'],
            'gst_no' => $validated['gst_no'],
            'state_id' => $validated['state_id'],
            'city_id' => $validated['city_id'],
            'pincode' => $validated['pincode'],
            'address' => $validated['address'],
        ]);

        // Redirect back to the warehouse index or another route with a success message
        return redirect()->route('warehouse.index')->with('success', 'Warehouse created successfully!');
    }

    public function show($id)
    {
        $warehouse = Warehouse::with(['state', 'city'])->findOrFail($id);
        return view('settings.warehouse.show', compact('warehouse'));
    }

    public function edit($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        $states = State::all();
        $cities = City::where('state_id', $warehouse->state_id)->get();
        return view('settings.warehouse.edit', compact('warehouse', 'states', 'cities'));
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'warehouse_name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15',
            'pan' => 'nullable|string|max:10',
            'gst_no' => 'nullable|string|max:15',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'pincode' => 'required|integer',
            'address' => 'required|string|max:500',
        ]);

        // Find the warehouse and update it
        $warehouse = Warehouse::findOrFail($id);
        $warehouse->update([
            'name' => $validated['warehouse_name'],
            'mobile_no' => $validated['mobile'],
            'pan_no' => $validated['pan'],
            'gst_no' => $validated['gst_no'],
            'state_id' => $validated['state_id'],
            'city_id' => $validated['city_id'],
            'pincode' => $validated['pincode'],
            'address' => $validated['address'],
        ]);

        // Redirect back to the warehouse index with a success message
        return redirect()->route('warehouse.index')->with('success', 'Warehouse updated successfully!');
    }
}
