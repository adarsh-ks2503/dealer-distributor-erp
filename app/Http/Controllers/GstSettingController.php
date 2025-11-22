<?php

namespace App\Http\Controllers;

use App\Models\GstSetting;
use Illuminate\Http\Request;

class GstSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->middleware('permission:GST-Index', ['only' => ['index']]);
        $this->middleware('permission:GST-Create', ['only' => ['create', 'store']]);
        $this->middleware('permission:GST-Edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:GST-InActive', ['only' => ['destroy']]);
        $this->middleware('permission:GST-Active', ['only' => ['activate']]);
        $this->middleware('permission:GST-View', ['only' => ['show']]);
    }
    public function index()
    {
        $gsts = GstSetting::withTrashed()->latest('updated_at')->get();
        return view('settings.gst_setting.index', compact('gsts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {

        $all_item = GstSetting::all();
        $check = [];
        foreach ($all_item as $item) {
            if ($item->gst_prefix == $request->gst_prefix) {
                $check[] = $item->gst_prefix;
            }
        }
        if (!empty($check)) {
            return redirect()->back()->with('error', 'GST (' . $request->gst_prefix . ') already exist.');
        }

        $gst = new GstSetting();
        $gst->gst_prefix = $request->gst_prefix;
        $gst->percent = $request->gst_percent;
        $gst->created_at = now(); // Update updated_at

        // Save the instance to the database
        $gst->save();

        // Redirect back to the index page with a success message
        return redirect()->route('gst-setting.index')->with('success', 'GST Added Successfully.');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */


    public function update(Request $request)
    {
        $check = GstSetting::where('gst_prefix', $request->gst_prefix)->where('id', $request->gst_id)->first();
        if (empty($check)) {
            $checkdescription = GstSetting::where('gst_prefix', $request->gst_prefix)->first();
            if (!empty($checkdescription)) {
                return redirect()->back()->with('error', 'GST (' . $request->gst_prefix . ') already exist.');
            }
        }


        $data = [
            'gst_prefix' => $request->gst_prefix,
            'percent' => $request->gst_percent,
        ];
        GstSetting::where('id', $request->gst_id)->update($data);
        // Redirect back to the index page with a success message
        return redirect()->route('gst-setting.index')->with('update', 'Gst Updated Successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $gst = GstSetting::find($id);
        if ($gst) {
            $gst->delete();
            return redirect()->route('gst-setting.index')->with('delete', 'GST Inactive Successfully.');
        }
        return redirect()->route('gst-setting.index')->with('error', 'GST Not Found.');
    }

    public function activate(string $id)
    {
        $gst = GstSetting::withTrashed()->findOrFail($id);

        if ($gst->trashed()) {
            $gst->restore();
            return redirect()->route('gst-setting.index')->with('success', 'GST activated successfully.');
        }

        return redirect()->route('gst-setting.index')->with('info', 'GST is already active.');
    }


    public function get_gst_details(Request $request)
    {
        $gst_details = GstSetting::where('id', $request->item_id)->first();

        return response([
            'data' => $gst_details,
        ]);
    }
}
