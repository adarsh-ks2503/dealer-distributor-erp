<?php

namespace App\Http\Controllers;

use App\Models\EmailSetting;
use Illuminate\Http\Request;

class EmailSettingController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:Email-Index', ['only' => ['index']]);
        $this->middleware('permission:Email-Edit', ['only' => ['update']]);
    }

    public function index()
    {
        $data = EmailSetting::first();
        return view('settings.smtp.index', compact('data'));
    }

    public function update(Request $request)
    {
        // dd($request);
        // Validate the request data
        $vaildation = $request->validate([
            'mailer' => 'required',
            'host' => 'required',
            'port' => 'required',
            'username' => 'required',
            'key' => 'required',
            'from_address' => 'required|email',
            'from_name' => 'required',
        ]);

        // // Retrieve the settings from the database
        $settings = EmailSetting::firstOrNew(); // Assuming you have one row for settings
        // Update the settings with the new values from the form
        $settings->mailer = $request->mailer;
        $settings->host = $request->host;
        $settings->port = $request->port;
        $settings->username = $request->username;
        $settings->key = $request->key;
        $settings->from_address = $request->from_address;
        $settings->from_name = $request->from_name;
        // $settings->cc = $request->cc;
        // $settings->bcc = $request->bcc;
        $settings->updated_at = now(); // Update updated_at

        // Save the updated settings to the database
        $settings->save();
        // Redirect back with success message

        return redirect()->back()->with('update', 'Email Updated Successfully');
    }
}
