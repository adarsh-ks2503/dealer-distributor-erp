<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\LoadingPointMaster;

class LoadingPointMasterController extends Controller
{

    // public function __construct()
    // {
    //     $this->middleware('permission:LoadingPoint-Index', ['only' => ['index']]);
    //     $this->middleware('permission:LoadingPoint-Create', ['only' => ['create', 'store']]);
    //     // $this->middleware('permission:LoadingPoint-View', ['only' => ['show']]);
    //     // $this->middleware('permission:LoadingPoint-Edit', ['only' => ['edit', 'update']]);
    //     // $this->middleware('permission:LoadingPoint-Delete', ['only' => ['destroy']]);
    // }
    public function index()
    {
        $warehouses = Warehouse::all();
        $loadingPoints = LoadingPointMaster::with('warehouse')->get();
        return view('settings.loading_point_master.index', compact('warehouses', 'loadingPoints'));
    }

    public function create(Request $request)
    {
        $warehouse = Warehouse::findOrFail($request->warehouse_id);
        return view('settings.loading_point_master.create', compact('warehouse'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'loading_point_name' => 'required|string|max:255',
            'short_code' => 'required|string|max:50',
            'dock_gate_no' => 'required|string|max:50',
            'supervisor_name' => 'required|string|max:100',
            'supervisor_mobile_no' => 'required|numeric',
            'remark' => 'nullable|string',
        ]);

        $loadingPoint = new LoadingPointMaster([
            'name' => $request->loading_point_name,
            'short_code' => $request->short_code,
            'gate_no' => $request->dock_gate_no,
            'supervisor_name' => $request->supervisor_name,
            'supervisor_mobile_no' => $request->supervisor_mobile_no,
            'remarks' => $request->remark,
            'warehouse_id' => $request->warehouse_id,  // Associate warehouse with the loading point
            'status' => 'Active',
        ]);

        $loadingPoint->save();

        return redirect()->route('loadingPointMaster.index')->with('success', 'Loading Point created successfully.');
    }
}
