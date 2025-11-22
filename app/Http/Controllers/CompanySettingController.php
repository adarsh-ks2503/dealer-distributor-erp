<?php

namespace App\Http\Controllers;

use App\Models\CityStateModel;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CompanySettingController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:Company-Index', ['only' => ['index']]);
    }
    public function index()
    {

        $data = CompanySetting::first();
        // dd($data, $states);
        return view('settings.company_setting.index', compact('data'));
    }

    public function update(Request $req)
    {
        Validator::make($req->all(), [
            'name' => 'required',
            'email' => 'required',
            'phone_number' => 'required',
            'address' => 'required',
            'state' => 'required',
            'city' => 'required',
            'country' => 'required',
            'pincode' => 'required',
            'gst_no' => 'required',
            'pan' => 'required',
            'tan' => 'required',
            // 'threshold' => 'required',
            'bank_name' => 'required',
            // 'amount' => 'required',
            'ac_number' => 'required',
            'ifsc_code' => 'required',
            'branch' => 'required',
        ])->validate();

        // dd($request);
        // Retrieve the settings from the database
        $CompanySetting = CompanySetting::firstOrNew();
        $CompanySetting->name = $req->name;
        $CompanySetting->email = $req->email;
        $CompanySetting->phone_number = $req->phone_number;
        $CompanySetting->address = $req->address;
        $CompanySetting->state = $req->state;
        $CompanySetting->city = $req->city;
        $CompanySetting->country = $req->country;
        $CompanySetting->pincode = $req->pincode;
        $CompanySetting->gst_no = $req->gst_no;
        $CompanySetting->pan = $req->pan;
        $CompanySetting->tan = $req->tan;
        // $CompanySetting->threshold = $req->threshold;
        $CompanySetting->bank_name = $req->bank_name;
        // $CompanySetting->amount = $req->amount;
        $CompanySetting->ac_number = $req->ac_number;
        $CompanySetting->ifsc_code = $req->ifsc_code;
        $CompanySetting->branch = $req->branch;
        $CompanySetting->updated_at = now();

        // Save the updated settings to the database
        $CompanySetting->save();
        return redirect()->back()->with('update', 'Company Updated Successfully');
    }
}
