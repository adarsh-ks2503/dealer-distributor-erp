<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        return view('users-profile', compact('user'));
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }



    public function pro_update(Request $request, $user_id)
    {
        $user = User::findOrFail($user_id);

        // Validate request data
        $validated = $request->validate(
            [
                'name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user_id,
                'profile' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // max 2MB
            ],
            [
                'profile.max' => 'The profile image must not be greater than 2MB.',
            ]
        );

        // Assign values
        $user->name = $validated['name'];
        $user->last_name = $validated['last_name'];
        $user->email = $validated['email'];

        if ($request->hasFile('profile')) {
            $file = $request->file('profile');
            $fileName = uniqid() . '.' . $file->extension();

            $file->move(public_path('uploads/user_profile/' . $user_id . '/'), $fileName);

            // Delete old profile image if exists
            if ($user->profile) {
                $oldFilePath = public_path('uploads/user_profile/' . $user_id . '/' . $user->profile);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            $user->profile = $fileName;
        }

        $user->save();

        return redirect()->route('profile_edit', $user_id)->with('update', 'Profile Updated Successfully');
    }

    public function password_reset(Request $request, $user_id)
    {

        // dd($request);

        $request->validate([
            'old_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($user_id);

        // dd($user);

        // Verify old password
        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'The provided password does not match your current password.']);
        }


        $newpwd = Hash::make($request->password);
        $user->password = $newpwd;
        $user->update();


        // dd('ok ');

        return redirect()->route('profile_edit', $user_id)->with('update', 'Password Updated Successfully');
    }
}
