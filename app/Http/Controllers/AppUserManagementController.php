<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppUserManagement;
use App\Models\State;
use App\Models\City;

class AppUserManagementController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:AppUserMgmt-Index', ['only' => ['index']]);
        $this->middleware('permission:AppUserMgmt-View', ['only' => ['show']]);
        $this->middleware('permission:AppUserMgmt-Edit', ['only' => ['edit', 'update']]);
    }
    public function index()
    {
        $users = AppUserManagement::with(['state', 'city'])
            ->whereIn('status', ['Active', 'Inactive'])
            ->orderBy('created_at', 'DESC')->get();

        return view('appUserManagement.index', compact('users'));
    }

    public function show($id)
    {
        $user = AppUserManagement::with(['state', 'city'])->findOrFail($id);
        return view('appUserManagement.view', compact('user'));
    }

    public function edit($id)
    {
        $user = AppUserManagement::with(['state', 'city'])->findOrFail($id);
        $states = State::all();
        $cities = City::where('state_id', $user->state_id ?? 0)->get();
        return view('appUserManagement.edit', compact('user', 'states', 'cities'));
    }

    public function update(Request $request, $id)
    {
        $user = AppUserManagement::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'code' => 'required|string|max:50|unique:app_user_management,code,' . $id,
            'email' => 'required|email|unique:app_user_management,email,' . $id,
            'mobile_no' => 'required|digits:10|unique:app_user_management,mobile_no,' . $id,
            'status' => 'nullable|in:Active,Inactive',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'password' => 'nullable|min:8|confirmed', // 'confirmed' now checks password_confirmation
            'password_confirmation' => 'nullable|min:8|required_with:password', // Required only if password is provided
        ]);

        // Prepare data for update, excluding password_confirmation
        $updateData = array_diff_key($validated, ['password_confirmation' => '']);

        // Only update password if a new password is provided
        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        } else {
            // Exclude password from update to retain the existing one
            unset($updateData['password']);
        }

        // Update the user with the prepared data
        $user->update($updateData);

        return redirect()->route('appUserManagement')->with('success', 'User updated successfully.');
    }

    public function cities($stateId)
    {
        $cities = City::where('state_id', $stateId)->get(['id', 'name']);
        return response()->json($cities);
    }
}
