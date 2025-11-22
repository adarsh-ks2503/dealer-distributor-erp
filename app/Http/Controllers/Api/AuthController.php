<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MobileUser;
use App\Models\PasswordOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\AppUserManagement;
use App\Notifications\ResetPasswordOtp;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\Dealer;
use App\Models\Distributor;
use App\Http\Resources\ProfileResource;
use App\Models\DealerOrderLimitRequest;
use App\Models\DistributorOrderLimitRequest;
use App\Models\State;
use App\Models\City;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\DistributorTeam;
use App\Models\DistributorTeamDealer;

class AuthController extends Controller
{

    // public function login(Request $request)
    // {
    //     // Validate input
    //     $request->validate([
    //         'login' => 'required', // This can be either email or mobile
    //         'password' => 'required',
    //     ]);

    //     // Determine if login is email or mobile
    //     $login_type = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';


    //     // Find the user
    //     $user = User::where($login_type, $request->login)->first();

    //     // Validate user and password
    //     if (! $user || ! Hash::check($request->password, $user->password)) {
    //         return response()->json(['message' => 'Invalid credentials'], 401);
    //     }

    //     // Create token
    //     $token = $user->createToken('API Token')->plainTextToken;

    //     // Decode JSON string safely
    //     $appUserTypeArray = [];

    //     // Agar DB me JSON string hai to decode karo
    //     if (is_string($user->app_user_type)) {
    //         $decoded = json_decode($user->app_user_type, true);
    //         $appUserTypeArray = is_array($decoded) ? $decoded : [$user->app_user_type];
    //     }
    //     // Agar already array hai
    //     elseif (is_array($user->app_user_type)) {
    //         $appUserTypeArray = $user->app_user_type;
    //     }
    //     // Agar null ya empty hai
    //     else {
    //         $appUserTypeArray = [];
    //     }

    //     // Convert to comma-separated string
    //     $appUserType = implode(', ', $appUserTypeArray);

    //     return response()->json([
    //         'message' => 'User login successfully',
    //         'token' => $token,
    //         'user'  => array_merge($user->makeHidden(['last_name'])->toArray(), [
    //             'app_user_type' => $appUserType,
    //             'role' => $user->getRoleNames()->first(),
    //         ]),
    //     ]);
    // }

    public function login(Request $request)
    {
        // --- 1. Validate the input ---
        $request->validate([
            'login'    => 'required|string', // This can be either email or mobile number
            'password' => 'required|string',
        ]);

        // --- 2. Determine if the login field is an email or a mobile number ---
        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile_no';

        // --- 3. Find the App User ---
        $appUser = AppUserManagement::where($loginField, $request->login)->first();

        // --- 4. Validate Credentials ---
        // Check if user exists and password is correct
        if (!$appUser || !Hash::check($request->password, $appUser->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials provided.'
            ], 401); // 401 Unauthorized
        }

        // --- 5. CRITICAL: Check if the user's account is active ---
        if ($appUser->status !== 'Active') {
            return response()->json([
                'status' => false,
                'message' => 'Your account is not active. Please contact support.'
            ], 403); // 403 Forbidden
        }

        // --- 6. Create the API Token ---
        $token = $appUser->createToken('API Token for AppUser')->plainTextToken;

        // --- 7. Return the success response ---
        return response()->json([
            'status' => true,
            'message' => 'Login successful.',
            'token' => $token,
            'user' => $appUser->makeHidden('password'), // Return user data but hide the password hash
        ], 200);
    }


    public function register(Request $request)
    {
        try {
            // Step 1: Validate input
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'last_name' => 'nullable|string|max:255',
                // 'email' => 'nullable|email|unique:users,email',
                'email' => 'required|email|unique:users,email',
                'mobile' => 'required|digits:10|unique:users,mobile',
                'date' => 'required|date',
                'password' => 'required|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return response()->json([
                    'status' => false,
                    'message' => $firstError,
                ], 422);
            }

            // 1. Determine current financial year
            $today = Carbon::today();
            $year = $today->month >= 4 ? $today->year : $today->year - 1;
            $nextYear = substr($year + 1, -2); // get last 2 digits of next year
            $financialYear = $year . '-' . $nextYear;

            // 2. Filter users created in the same financial year
            $startOfYear = Carbon::create($year, 4, 1);  // April 1 of current FY
            $endOfYear = Carbon::create($year + 1, 3, 31, 23, 59, 59); // March 31 next year

            $lastUser = User::whereBetween('created_at', [$startOfYear, $endOfYear])
                ->latest('id')
                ->first();

            // 3. Calculate next number
            $nextId = $lastUser ? ((int) substr($lastUser->user_id, -4)) + 1 : 1;

            // 4. Format ID
            $userId = 'U' . $financialYear . '_' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            // Step 2: Create user
            $user = User::create([
                'user_id' => $userId,
                'name' => $request->name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'user_type' => 'app',
                'app_user_type' => json_encode($request->app_user_type),
                'date' => $request->date,
                'password' => Hash::make($request->password),
            ]);

            // Step 3: Generate Sanctum token
            $token = $user->createToken('mobile-token')->plainTextToken;

            // Decode JSON string to array
            $appUserType = is_string($user->app_user_type)
                ? implode(', ', json_decode($user->app_user_type, true))
                : implode(', ', $user->app_user_type);

            // Step 4: Return API response
            return response()->json([
                'token' => $token,
                'user'  => array_merge($user->toArray(), [
                    'app_user_type' => $appUserType
                ]),
                'status' => true,
                'message' => 'User registered successfully',
            ], 201);
        } catch (\Exception $e) {


            // Handle any unexpected errors
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }


    public function update(Request $request)
    {
        try {
            $user = Auth::user();

            // Step 1: Validate input
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                // 'last_name' => 'sometimes|string|max:255',
                'email' => 'nullable|email|unique:users,email,' . $user->id,
                'mobile' => 'required|digits:10|unique:users,mobile,' . $user->id,
                // 'app_user_type' => 'required|in:Broker,Stockist,Consumer',
                // 'date' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                // Get the first error message from the validator
                $firstError = $validator->errors()->first();

                return response()->json([
                    'status' => false,
                    'message' => $firstError,
                ], 422);
            }
            // Step 2: Update fields if provided
            $user->update([
                'name' => $request->input('name', $user->name),
                // 'last_name' => $request->input('last_name', $user->last_name),
                'email' => $request->input('email', $user->email),
                'mobile' => $request->input('mobile', $user->mobile),
                // 'app_user_type' => $request->input('app_user_type', $user->app_user_type),
                'date' => $request->input('date', $user->date),
            ]);

            // Step 3: Return updated data
            return response()->json([
                'status' => true,
                'message' => 'Profile updated successfully',
                'data' => $user->makeHidden(['last_name']),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function list()
    {
        $user = Auth::user();

        if ($user) {
            // Decode JSON string to array
            $appUserType = is_string($user->app_user_type)
                ? implode(', ', json_decode($user->app_user_type, true))
                : implode(', ', $user->app_user_type);


            // Check if profile image exists
            if ($user->profile) {
                $user->profile = asset('storage/' . $user->profile);
            }

            return response()->json([
                'status' => true,
                'message' => 'User fetched successfully.',
                'data'  => array_merge($user->makeHidden(['last_name'])->toArray(), [
                    'app_user_type' => $appUserType
                ]),
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No authenticated user found.',
                'data' => null,
            ], 404);
        }
    }


    public function upload_profile(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'profile' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            $user = Auth::user(); // or Auth::guard('mobile')->user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated.',
                ], 401);
            }

            // Delete old image if exists
            if ($user->profile) {
                Storage::disk('public')->delete($user->profile);
            }

            // Store new image
            $path = $request->file('profile')->store('users', 'public');
            $user->profile = $path;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Profile image updated successfully.',
                'profile_url' => asset('storage/' . $path),
                'user' => $user->makeHidden(['last_name']),
            ]);
        } catch (\Exception $e) {
            // Log the error (optional)
            Log::error('Profile upload failed: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while uploading the profile image.',
            ], 500);
        }
    }

    // public function logout(Request $request)
    // {
    //     $request->user()->currentAccessToken()->delete();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Logged out successfully',
    //     ]);
    // }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    // public function sendOtp(Request $request)
    // {
    //     $request->validate(['email' => 'required|email']);

    //     // Check if user exists
    //     $user = User::where('email', $request->email)->first();
    //     if (!$user) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'User not found'
    //         ], 404);
    //     }

    //     // Generate 4-digit OTP
    //     $otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

    //     // Store OTP in database (valid for 10 minutes)
    //     PasswordOtp::updateOrInsert(
    //         ['email' => $request->email],
    //         ['otp' => $otp, 'created_at' => now()->addMinutes(10)] // Set expiry 10 mins from now
    //     );

    //     // Send OTP via email
    //     $user->notify(new ResetPasswordOtp($otp));

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'OTP sent to your email',
    //         'email' => $request->email
    //     ]);
    // }

    // public function verifyOtp(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'otp' => 'required|digits:4'
    //     ]);

    //     $record = PasswordOtp::where('email', $request->email)
    //         ->where('otp', $request->otp)
    //         ->first();

    //     // Step 2: Check if record exists
    //     if (!$record) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Invalid OTP or user not found.',
    //         ], 404);
    //     }

    //     // Step 3: Check if OTP is expired (more than 10 minutes)
    //     if (now()->greaterThan($record->created_at)) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'OTP has expired. Please request a new one.',
    //         ], 422);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'OTP verified successfully'
    //     ]);
    // }

    // public function resetWithOtp(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'otp' => 'required|digits:4',
    //         'password' => 'required|confirmed|min:8',
    //     ]);


    //     // Verify OTP again
    //     $record = PasswordOtp::where('email', $request->email)
    //         ->where('otp', $request->otp)
    //         ->first();

    //     // Step 2: Check if record exists
    //     if (!$record) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Invalid OTP or user not found.',
    //         ], 404);
    //     }

    //     // Step 3: Check if OTP is expired (more than 10 minutes)
    //     if (now()->greaterThan($record->created_at)) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'OTP has expired. Please request a new one.',
    //         ], 422);
    //     }


    //     // Update password
    //     $user = User::where('email', $request->email)->firstOrFail();
    //     $user->password = Hash::make($request->password);
    //     $user->save();

    //     // Clear OTP
    //     PasswordOtp::where('email', $request->email)->delete();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Password reset successful'
    //     ]);
    // }


    // public function password_update(Request $request)
    // {
    //     try {
    //         // Step 1: Validate inputs
    //         $validator = validator($request->all(), [
    //             'email' => 'required|email',
    //             'current_password' => 'required',
    //             'password' => 'required|confirmed|min:8',
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => $validator->errors()->first(), // Return first validation error
    //             ], 422);
    //         }

    //         // Step 2: Find the user
    //         $user = User::where('email', $request->email)->first();

    //         if (!$user) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'User with this email not found.',
    //             ], 404);
    //         }

    //         // Step 3: Check current password
    //         if (!Hash::check($request->current_password, $user->password)) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Current password is incorrect.',
    //             ], 401);
    //         }

    //         // Step 4: Update password
    //         $user->password = Hash::make($request->password);
    //         $user->save();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Password updated successfully.',
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Something went wrong. Please try again.',
    //             'error' => $e->getMessage(), // Optional for debugging
    //         ], 500);
    //     }
    // }


    // Working apis
    // public function sendOtp(Request $request)
    // {
    //     $request->validate(['email' => 'required|email']);

    //     // Check if user exists
    //     $user = AppUserManagement::where('email', $request->email)->first();
    //     if (!$user) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'User not found'
    //         ], 404);
    //     }

    //     // Generate 4-digit OTP
    //     $otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

    //     // Store OTP in database (valid for 10 minutes)
    //     PasswordOtp::updateOrInsert(
    //         ['email' => $request->email],
    //         ['otp' => $otp, 'created_at' => now()->addMinutes(10)] // Set expiry 10 mins from now
    //     );

    //     // Send OTP via email
    //     $user->notify(new ResetPasswordOtp($otp));

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'OTP sent to your email',
    //         'email' => $request->email
    //     ]);
    // }

    // public function verifyOtp(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'otp' => 'required|digits:4'
    //     ]);

    //     $record = PasswordOtp::where('email', $request->email)
    //         ->where('otp', $request->otp)
    //         ->first();

    //     // Step 2: Check if record exists
    //     if (!$record) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Invalid OTP or user not found.',
    //         ], 404);
    //     }

    //     // Step 3: Check if OTP is expired (more than 10 minutes)
    //     if (now()->greaterThan($record->created_at)) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'OTP has expired. Please request a new one.',
    //         ], 422);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'OTP verified successfully'
    //     ]);
    // }

    // public function resetWithOtp(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'otp' => 'required|digits:4',
    //         'password' => 'required|confirmed|min:8',
    //     ]);


    //     // Verify OTP again
    //     $record = PasswordOtp::where('email', $request->email)
    //         ->where('otp', $request->otp)
    //         ->first();

    //     // Step 2: Check if record exists
    //     if (!$record) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Invalid OTP or user not found.',
    //         ], 404);
    //     }

    //     // Step 3: Check if OTP is expired (more than 10 minutes)
    //     if (now()->greaterThan($record->created_at)) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'OTP has expired. Please request a new one.',
    //         ], 422);
    //     }


    //     // Update password
    //     $user = AppUserManagement::where('email', $request->email)->firstOrFail();
    //     $user->password = Hash::make($request->password);
    //     $user->save();

    //     // Clear OTP
    //     PasswordOtp::where('email', $request->email)->delete();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Password reset successful'
    //     ]);
    // }


    // public function password_update(Request $request)
    // {
    //     try {
    //         // Step 1: Validate inputs
    //         $validator = validator($request->all(), [
    //             'email' => 'required|email',
    //             'current_password' => 'required',
    //             'password' => 'required|confirmed|min:8',
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => $validator->errors()->first(), // Return first validation error
    //             ], 422);
    //         }

    //         // Step 2: Find the user
    //         $user = AppUserManagement::where('email', $request->email)->first();

    //         if (!$user) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'User with this email not found.',
    //             ], 404);
    //         }

    //         // Step 3: Check current password
    //         if (!Hash::check($request->current_password, $user->password)) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Current password is incorrect.',
    //             ], 401);
    //         }

    //         // Step 4: Update password
    //         $user->password = Hash::make($request->password);
    //         $user->save();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Password updated successfully.',
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Something went wrong. Please try again.',
    //             'error' => $e->getMessage(), // Optional for debugging
    //         ], 500);
    //     }
    // }


    public function sendOtp(Request $request)
    {
        // --- NAYA VALIDATION ---
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            $formattedErrors = collect($validator->errors()->all())->map(function ($message) {
                return ['Reason' => $message];
            })->all();

            return response()->json([
                'Status' => false,
                'Message' => 'validations failed',
                'Errors' => $formattedErrors
            ], 422);
        }

        $validated = $validator->validated();
        // --- END NAYA VALIDATION ---

        // Check if user exists
        $user = AppUserManagement::where('email', $validated['email'])->first(); // $request->email ki jagah $validated
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Generate 4-digit OTP
        $otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        // Store OTP in database (valid for 10 minutes)
        // (Yeh DB::table() wala fix hai jo humne pehle discuss kiya tha)
        DB::table('password_otps')->updateOrInsert(
            ['email' => $validated['email']],
            ['otp' => $otp, 'created_at' => now()->addMinutes(10)]
        );

        // Send OTP via email
        $user->notify(new ResetPasswordOtp($otp));

        return response()->json([
            'status' => true,
            'message' => 'OTP sent to your email',
            'email' => $validated['email']
        ]);
    }

    public function verifyOtp(Request $request)
    {
        // --- NAYA VALIDATION ---
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|digits:4'
        ]);

        if ($validator->fails()) {
            $formattedErrors = collect($validator->errors()->all())->map(function ($message) {
                return ['Reason' => $message];
            })->all();

            return response()->json([
                'Status' => false,
                'Message' => 'validations failed',
                'Errors' => $formattedErrors
            ], 422);
        }

        $validated = $validator->validated();
        // --- END NAYA VALIDATION ---

        $record = PasswordOtp::where('email', $validated['email'])
            ->where('otp', $validated['otp'])
            ->first();

        // Step 2: Check if record exists
        if (!$record) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP or user not found.',
            ], 404);
        }

        // Step 3: Check if OTP is expired (more than 10 minutes)
        if (now()->greaterThan($record->created_at)) {
            return response()->json([
                'status' => false,
                'message' => 'OTP has expired. Please request a new one.',
            ], 422);
        }

        return response()->json([
            'status' => true,
            'message' => 'OTP verified successfully'
        ]);
    }

    public function resetWithOtp(Request $request)
    {
        // --- NAYA VALIDATION ---
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|digits:4',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            $formattedErrors = collect($validator->errors()->all())->map(function ($message) {
                return ['Reason' => $message];
            })->all();

            return response()->json([
                'Status' => false,
                'Message' => 'validations failed',
                'Errors' => $formattedErrors
            ], 422);
        }

        $validated = $validator->validated();
        // --- END NAYA VALIDATION ---


        // Verify OTP again
        $record = PasswordOtp::where('email', $validated['email'])
            ->where('otp', $validated['otp'])
            ->first();

        // Step 2: Check if record exists
        if (!$record) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP or user not found.',
            ], 404);
        }

        // Step 3: Check if OTP is expired (more than 10 minutes)
        if (now()->greaterThan($record->created_at)) {
            return response()->json([
                'status' => false,
                'message' => 'OTP has expired. Please request a new one.',
            ], 422);
        }


        // Update password
        $user = AppUserManagement::where('email', $validated['email'])->firstOrFail();
        $user->password = Hash::make($validated['password']);
        $user->save();

        // Clear OTP
        PasswordOtp::where('email', $validated['email'])->delete();

        return response()->json([
            'status' => true,
            'message' => 'Password reset successful'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */

    // public function destroy()
    // {
    //     try {
    //         $user = Auth::user();

    //         if (!$user) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Unauthorized. Please login again.'
    //             ], 401);
    //         }

    //         $id = $user->id;
    //         $user = AppUserManagement::find($id);

    //         if (!$user) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'User not found.'
    //             ], 404);
    //         }

    //         $user->delete();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'User deleted successfully.'
    //         ], 200);
    //     } catch (\Exception $e) {
    //         // Optional: Log error
    //         Log::error('User deletion failed: ' . $e->getMessage());

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Something went wrong.',
    //         ], 500);
    //     }
    // }

    // public function destroy()
    // {
    //     DB::beginTransaction();
    //     try {
    //         $appUser = Auth::user();

    //         if (!$appUser) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Unauthorized. Please login again.'
    //             ], 401);
    //         }

    //         // --- YAHAN SE NAYA LOGIC SHURU HOTA HAI ---

    //         $userType = $appUser->type;
    //         $userCode = $appUser->code;

    //         // Step 1: Check for blocks based on user type
    //         if ($userType === 'dealer') {
    //             $dealer = Dealer::where('code', $userCode)->first();
    //             if (!$dealer) {
    //                 throw new \Exception('Associated dealer profile not found.');
    //             }

    //             // Check for active orders (Logic from web dealer checkInactivation)
    //             $hasOrders = Order::where('placed_by_dealer_id', $dealer->id)
    //                 ->whereIn('status', ['Pending', 'Approved', 'Partial Dispatch'])
    //                 ->exists();

    //             if ($hasOrders) {
    //                 DB::rollBack(); // Koi change nahi karna
    //                 return response()->json([
    //                     'status' => false,
    //                     'message' => 'You have pending or active orders. Please contact support to delete your account.'
    //                 ], 422); // 422 Unprocessable Entity
    //             }

    //             // Step 2: Inactivate dealer (Logic from web dealer inactivate)
    //             $dealer->status = 'Inactive';
    //             $dealer->distributor_id = null; // Team se remove karein
    //             $dealer->save();

    //             // Pivot table ko bhi inactive karein
    //             DistributorTeamDealer::where('dealer_id', $dealer->id)
    //                 ->update(['status' => 'Inactive']);

    //         } elseif ($userType === 'distributor') {
    //             $distributor = Distributor::where('code', $userCode)->first();
    //             if (!$distributor) {
    //                 throw new \Exception('Associated distributor profile not found.');
    //             }

    //             // Check for active orders (Logic from web distributor checkInactivation)
    //             $hasOrders = Order::where('placed_by_distributor_id', $distributor->id)
    //                 ->whereIn('status', ['Pending', 'Approved', 'Partial Dispatch'])
    //                 ->exists();

    //             if ($hasOrders) {
    //                 DB::rollBack();
    //                 return response()->json([
    //                     'status' => false,
    //                     'message' => 'You have pending or active orders. Please contact support to delete your account.'
    //                 ], 422);
    //             }

    //             // Check for active team (Logic from web distributor checkInactivation)
    //             $hasTeam = DistributorTeam::where('distributor_id', $distributor->id)
    //                 ->where('status', 'Active') // Assuming 'Active' team check is correct
    //                 ->exists();

    //             // Note: Web logic says `hasTeam` blocks inactivation.
    //             if ($hasTeam) {
    //                 DB::rollBack();
    //                 return response()->json([
    //                     'status' => false,
    //                     'message' => 'Your account is linked to an active team. Please contact support to delete your account.'
    //                 ], 422);
    //             }

    //             // Step 2: Inactivate distributor (Logic from web distributor inactivate)
    //             $distributor->status = 'Inactive';
    //             $distributor->save();

    //         }

    //         // Step 3: Inactivate the main app user (sabse zaroori)
    //         // $user->delete(); // --- YEH GALAT THA ---
    //         $appUser->status = 'Inactive'; // <-- YEH SAHI HAI
    //         $appUser->save();

    //         DB::commit();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Account inactivated successfully.'
    //         ], 200);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('User account deletion (inactivation) failed: ' . $e->getMessage());

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Something went wrong while deactivating the account.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // public function destroy()
    // {
    //     try {
    //         $authUser = Auth::user();

    //         if (!$authUser) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Unauthorized. Please login again.'
    //             ], 401);
    //         }

    //         if (!in_array($authUser->type, ['dealer', 'distributor'])) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Only dealers and distributors can delete their account.'
    //             ], 403);
    //         }

    //         // Re-fetch fresh Eloquent instance
    //         $appUser = AppUserManagement::findOrFail($authUser->id);

    //         DB::beginTransaction();

    //         if ($appUser->type === 'dealer') {
    //             $dealer = Dealer::where('code', $appUser->code)->firstOrFail();

    //             $hasOrders = Order::where('placed_by_dealer_id', $dealer->id)
    //                 ->whereIn('status', ['Pending', 'Approved', 'Partial Dispatch'])
    //                 ->exists();

    //             if ($hasOrders) {
    //                 DB::rollBack();
    //                 return response()->json([
    //                     'status' => false,
    //                     'message' => 'Cannot delete account. You have pending or active orders.'
    //                 ], 400);
    //             }

    //             $dealer->status = 'Inactive';
    //             $dealer->distributor_id = null;
    //             $dealer->save();

    //             DistributorTeamDealer::where('dealer_id', $dealer->id)
    //                 ->update(['status' => 'Inactive']);
    //         } else if ($appUser->type === 'distributor') {
    //             $distributor = Distributor::where('code', $appUser->code)->firstOrFail();

    //             $hasOrders = Order::where('placed_by_distributor_id', $distributor->id)
    //                 ->whereIn('status', ['Pending', 'Approved', 'Partial Dispatch'])
    //                 ->exists();

    //             if ($hasOrders) {
    //                 DB::rollBack();
    //                 return response()->json([
    //                     'status' => false,
    //                     'message' => 'Cannot delete account. You have pending or active orders.'
    //                 ], 400);
    //             }

    //             $hasTeam = DistributorTeam::where('distributor_id', $distributor->id)
    //                 ->where('status', 'Active')
    //                 ->exists();

    //             if ($hasTeam) {
    //                 DB::rollBack();
    //                 return response()->json([
    //                     'status' => false,
    //                     'message' => 'Cannot delete account. You have active dealers in your team.'
    //                 ], 400);
    //             }

    //             $distributor->status = 'Inactive';
    //             $distributor->save();
    //         }

    //         // NOW SAFE TO SAVE
    //         $appUser->status = 'Inactive';
    //         $appUser->save();

    //         DB::commit();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Account deleted successfully. You have been logged out.'
    //         ], 200);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Account deletion failed: ' . $e->getMessage());

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Failed to delete account. Please try again.'
    //         ], 500);
    //     }
    // }

    public function destroy()
    {
        try {
            $authUser = Auth::user();

            // 1. Not logged in
            if (!$authUser) {
                return $this->apiResponse(false, 'Unauthorized. Please login again.');
            }

            // 2. Wrong user type
            if (!in_array($authUser->type, ['dealer', 'distributor'])) {
                return $this->apiResponse(false, 'Only dealers and distributors can delete their account.');
            }

            // Fresh model
            $appUser = AppUserManagement::findOrFail($authUser->id);

            DB::beginTransaction();

            // ==================== DEALER ====================
            if ($appUser->type === 'dealer') {
                $dealer = Dealer::where('code', $appUser->code)->first();

                if (!$dealer) {
                    DB::rollBack();
                    return $this->apiResponse(false, 'Your dealer account not found.');
                }

                // Check active orders
                $hasOrders = Order::where('placed_by_dealer_id', $dealer->id)
                    ->whereIn('status', ['Pending', 'Approved', 'Partial Dispatch'])
                    ->exists();

                if ($hasOrders) {
                    DB::rollBack();
                    return $this->apiResponse(false, 'Cannot delete account. You have pending or active orders.');
                }

                // Inactivate dealer
                $dealer->status = 'Inactive';
                $dealer->distributor_id = null;
                $dealer->save();

                // Remove from team
                DistributorTeamDealer::where('dealer_id', $dealer->id)
                    ->update(['status' => 'Inactive']);
            }

            // ==================== DISTRIBUTOR ====================
            else if ($appUser->type === 'distributor') {
                $distributor = Distributor::where('code', $appUser->code)->first();

                if (!$distributor) {
                    DB::rollBack();
                    return $this->apiResponse(false, 'Your distributor account not found.');
                }

                // Check orders
                $hasOrders = Order::where('placed_by_distributor_id', $distributor->id)
                    ->whereIn('status', ['Pending', 'Approved', 'Partial Dispatch'])
                    ->exists();

                if ($hasOrders) {
                    DB::rollBack();
                    return $this->apiResponse(false, 'Cannot delete account. You have pending or active orders.');
                }

                // Check team
                $hasTeam = DistributorTeam::where('distributor_id', $distributor->id)
                    ->where('status', 'Active')
                    ->exists();

                if ($hasTeam) {
                    DB::rollBack();
                    return $this->apiResponse(false, 'Cannot delete account. You have active dealers in your team.');
                }

                $distributor->status = 'Inactive';
                $distributor->save();
            }

            // Inactivate App User
            $appUser->status = 'Inactive';
            $appUser->save();

            DB::commit();

            return $this->apiResponse(true, 'Account deleted successfully. You have been logged out.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete Account API Error: ' . $e->getMessage());

            return $this->apiResponse(false, 'Something went wrong. Please try again later.');
        }
    }

    // Helper function — sirf 200 OK
    private function apiResponse($status, $message)
    {
        return response()->json([
            'status' => $status,
            'message' => $message
        ], 200);
    }


    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided password does not match your current password.'],
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Password updated successfully.'
        ], 200);
    }

    public function getMyProfile(Request $request)
    {
        // --- Step 1: Get the authenticated user from the token ---
        $appUser = $request->user();
        $userType = $appUser->type;
        $userCode = $appUser->code;

        $profileData = null;

        // --- Step 2: Fetch the correct profile based on the user's type ---
        if ($userType === 'dealer') {
            // Find the dealer profile and eager load its state and city relationships
            $profileData = Dealer::with(['state', 'city', 'distributor'])
                ->where('code', $userCode)
                ->first();
        } elseif ($userType === 'distributor') {
            // Find the distributor profile and eager load its relationships
            $profileData = Distributor::with(['state', 'city'])
                ->where('code', $userCode)
                ->first();
        }

        // --- Step 3: Handle the case where no profile is found ---
        if (!$profileData) {
            return response()->json([
                'status' => false,
                'message' => 'No profile was found for this user.'
            ], 404); // 404 Not Found
        }

        $profileData->type = $appUser->type;

        // --- Step 4: Return the complete profile data ---
        return response()->json([
            'status' => true,
            'data' => new ProfileResource($profileData)
        ], 200);
    }

    public function requestMyOrderLimit(Request $request)
    {
        // --- Step 1: Get the authenticated user ---
        $appUser = $request->user();

        if (!$appUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        // --- Step 2: Validate the input data (yeh dono ke liye same hai) ---
        $validated = $request->validate([
            'desired_order_limit' => 'required|numeric|min:1',
            'remarks' => 'nullable|string|max:1000',
        ]);

        // --- Step 3: User type ke hisaab se Models aur Keys set karo ---
        $userType = $appUser->type;
        $profileModel = null;
        $requestModel = null;
        $foreignKey = null;

        if ($userType === 'dealer') {
            $profileModel = Dealer::class;
            $requestModel = DealerOrderLimitRequest::class;
            $foreignKey = 'dealer_id';
        } else if ($userType === 'distributor') {
            $profileModel = Distributor::class;
            $requestModel = DistributorOrderLimitRequest::class;
            $foreignKey = 'distributor_id';
        } else {
            // Agar user na dealer hai na distributor
            return response()->json([
                'status' => false,
                'message' => 'Forbidden: This action is only available for dealers and distributors.'
            ], 403);
        }

        // --- Step 4: Profile dhoondo ---
        $profile = $profileModel::where('code', $appUser->code)->first();
        if (!$profile) {
            return response()->json(['status' => false, 'message' => "User profile ($userType) not found."], 404);
        }

        // --- Step 5: Check for existing pending request (Common logic) ---
        // Aapke dealer code mein yeh tha, aur yeh achhi practice hai, isliye dono ke liye add kar raha hoon.
        $existingRequest = $requestModel::where($foreignKey, $profile->id)
            ->where('status', 'Pending')
            ->exists();

        if ($existingRequest) {
            return response()->json([
                'status' => false,
                'message' => 'You already have a pending order limit request. Please wait for it to be processed.'
            ], 422);
        }

        // --- Step 6: Naya request create karo (try...catch ke saath) ---
        try {
            $limitRequest = $requestModel::create([
                $foreignKey => $profile->id,
                'order_limit' => $profile->order_limit, // Yeh aapke naye code mein tha
                'desired_order_limit' => $validated['desired_order_limit'],
                'remarks' => $validated['remarks'] ?? null,
                'status' => 'Pending',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Your request to change the order limit has been submitted successfully.',
                'data' => $limitRequest,
            ], 201);
        } catch (\Exception $e) {
            Log::error("Order limit request failed for $userType ($profile->id): " . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }


    // public function state(Request $request)
    // {
    //     try {
    //         $states = CityStateModel::select('state')->distinct()->get();

    //         return response()->json([
    //             'status' => true,
    //             'data' => $states
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('State fetch error: ' . $e->getMessage());
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Unable to fetch states.'
    //         ], 500);
    //     }
    // }

    // public function city(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'state' => 'required|string|max:100'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status'  => false,
    //             'message' => $validator->errors()->first(),
    //         ], 422);
    //     }

    //     try {

    //         $cities = CityStateModel::where('state', $request->state)->pluck('city');

    //         return response()->json([
    //             'status' => true,
    //             'data' => $cities
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('City fetch error: ' . $e->getMessage());
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Unable to fetch cities.'
    //         ], 500);
    //     }
    // }

    public function state(Request $request) // $request ko rakha hai, future ke liye
    {
        try {
            // Query banayein (Search filter hata diya gaya hai)
            $query = State::query();

            // Saare states fetch karein
            $states = $query->orderBy('state')
                ->select('id', 'state') // ID aur state name select karein
                ->get(); // <-- paginate() ko get() se badal diya gaya hai

            // Simple JSON Response (Custom pagination structure hata di gayi hai)
            return response()->json([
                'status' => true,
                'data'   => $states // Poori list return karein
            ], 200);
        } catch (\Exception $e) {
            Log::error('State fetch error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Unable to fetch states.'
            ], 500);
        }
    }

    /**
     * Ek specific state ID ke saare city ID aur Name ki list fetch karein.
     * (Pagination hata diya gaya hai)
     */
    public function city(Request $request)
    {
        // Validation (state_id abhi bhi zaroori hai)
        $validator = Validator::make($request->all(), [
            'state_id' => 'required|integer|exists:states,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            // Query banayein, state_id se filter karke
            $query = City::where('state_id', $request->state_id);

            // Search filter hata diya gaya hai

            // Saare matching cities fetch karein
            $cities = $query->orderBy('name') // Maan rahe hain ki City table mein 'name' column hai
                ->select('id', 'name') // ID aur city name select karein
                ->get(); // <-- paginate() ko get() se badal diya gaya hai

            // Simple JSON Response (Custom pagination structure hata di gayi hai)
            return response()->json([
                'status' => true,
                'data'   => $cities // Poori list return karein
            ], 200);
        } catch (\Exception $e) {
            Log::error('City fetch error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Unable to fetch cities.'
            ], 500);
        }
    }
}
