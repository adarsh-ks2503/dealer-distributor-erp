<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dealer;
use App\Models\DistributorTeam;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\DealerOrderLimitRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Distributor;
use Illuminate\Validation\ValidationException;
use App\Rules\UniqueAcrossMultipleTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Notifications\NewDealerAdded;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class DealerController extends Controller
{
    // List All Dealers with Active Status
    public function index()
    {
        try {
            // $dealers = Dealer::with(['state', 'city', 'contactPersons'])->get();
            $dealers = Dealer::with(['state', 'city', 'contactPersons'])
                ->whereIn('status', ['Active'])
                ->get();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Dealers retrieved successfully',
                    'data' => $dealers,
                ],
                200,
            );
        } catch (\Exception $e) {
            Log::error('Dealer index error: ' . $e->getMessage());

            return response()->json(
                [
                    'status' => false,
                    'message' => 'Error retrieving dealers',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    // Store The Dealer
    // public function store(Request $request)
    // {
    //     try {
    //         // =============================================================
    //         // --- ADDED SECURITY CHECK ---
    //         // =============================================================
    //         $appUser = $request->user();

    //         // Check if a user is logged in and if their type is 'distributor'
    //         if (!$appUser || $appUser->type !== 'distributor') {
    //             return response()->json(
    //                 [
    //                     'status' => false,
    //                     'message' => 'Forbidden: Only authenticated distributors can create dealers.',
    //                 ],
    //                 403,
    //             ); // 403 Forbidden
    //         }
    //         // =============================================================
    //         // Filter contact persons (same logic as your original)
    //         if ($request->has('contact_person')) {
    //             $filtered = collect($request->input('contact_person'))
    //                 ->filter(function ($person) {
    //                     return !empty($person['name']) || !empty($person['mobile_no']) || !empty($person['email']);
    //                 })
    //                 ->values()
    //                 ->toArray();

    //             $request->merge(['contact_person' => $filtered]);
    //         }

    //         // Validation rules
    //         $validated = $request->validate([
    //             'name' => 'required|string|max:255',
    //             'code' => ['required', 'string', 'max:255'],
    //             'distributor_id' => 'nullable|integer|exists:distributors,id',
    //             'mobile_no' => ['required', 'digits:10'],
    //             'email' => ['nullable', 'email'],
    //             'gst_num' => ['nullable', 'string', 'max:50'],
    //             'pan_num' => ['nullable', 'string', 'max:50'],
    //             'order_limit' => ['nullable', 'numeric'],
    //             'remarks' => 'nullable|string',
    //             'address' => 'nullable|string',
    //             'pincode' => ['nullable', 'digits:6'],
    //             'state_id' => ['nullable', 'integer'],
    //             'city_id' => ['nullable', 'integer'],
    //             'type' => ['nullable', 'string'],
    //             'bank_name' => ['nullable', 'string', 'max:100'],
    //             'account_holder_name' => ['nullable', 'string', 'max:100'],
    //             'ifsc_code' => ['nullable', 'string', 'max:20'],
    //             'account_number' => ['nullable', 'string', 'max:30'],
    //             'contact_person.*.name' => ['nullable', 'string', 'max:255'],
    //             'contact_person.*.mobile_no' => ['nullable', 'digits:10'],
    //             'contact_person.*.email' => ['nullable', 'email', 'max:255'],
    //         ]);

    //         // Cross-table uniqueness check
    //         $conflictFields = [];

    //         // 1. Code uniqueness
    //         if (DB::table('distributors')->where('code', $request->code)->exists() || DB::table('dealers')->where('code', $request->code)->exists()) {
    //             $conflictFields['code'] = 'This code is already in use.';
    //         }

    //         // 2. Mobile No uniqueness
    //         if (DB::table('distributors')->where('mobile_no', $request->mobile_no)->exists() || DB::table('dealers')->where('mobile_no', $request->mobile_no)->exists() || DB::table('distributor_contact_persons_details')->where('mobile_no', $request->mobile_no)->exists() || DB::table('dealer_contact_persons_details')->where('mobile_no', $request->mobile_no)->exists()) {
    //             $conflictFields['mobile_no'] = 'This mobile number is already in use.';
    //         }

    //         // 3. Email uniqueness
    //         if ($request->email && (DB::table('distributors')->where('email', $request->email)->exists() || DB::table('dealers')->where('email', $request->email)->exists() || DB::table('distributor_contact_persons_details')->where('email', $request->email)->exists() || DB::table('dealer_contact_persons_details')->where('email', $request->email)->exists())) {
    //             $conflictFields['email'] = 'This email is already in use.';
    //         }

    //         // 4. GST & PAN uniqueness
    //         if ($request->gst_num && (DB::table('distributors')->where('gst_num', $request->gst_num)->exists() || DB::table('dealers')->where('gst_num', $request->gst_num)->exists())) {
    //             $conflictFields['gst_num'] = 'This GST number is already in use.';
    //         }

    //         if ($request->pan_num && (DB::table('distributors')->where('pan_num', $request->pan_num)->exists() || DB::table('dealers')->where('pan_num', $request->pan_num)->exists())) {
    //             $conflictFields['pan_num'] = 'This PAN number is already in use.';
    //         }

    //         // 5. Contact person validation and uniqueness
    //         if ($request->has('contact_person')) {
    //             foreach ($request->contact_person as $index => $contact) {
    //                 $c_mobile = $contact['mobile_no'] ?? null;
    //                 $c_email = $contact['email'] ?? null;

    //                 // Check if contact person has any data
    //                 $hasAny = !empty($contact['name']) || !empty($c_mobile) || !empty($c_email);

    //                 if ($hasAny) {
    //                     // Require name and mobile if any data is present
    //                     if (empty($contact['name'])) {
    //                         $conflictFields["contact_person.$index.name"] = 'Name is required when filling contact person details.';
    //                     }

    //                     if (empty($c_mobile)) {
    //                         $conflictFields["contact_person.$index.mobile_no"] = 'Mobile number is required when filling contact person details.';
    //                     }

    //                     // Mobile format validation
    //                     if (!empty($c_mobile) && !preg_match('/^\d{10}$/', $c_mobile)) {
    //                         $conflictFields["contact_person.$index.mobile_no"] = 'Mobile number must be exactly 10 digits.';
    //                     }

    //                     // Email format validation
    //                     if (!empty($c_email) && !filter_var($c_email, FILTER_VALIDATE_EMAIL)) {
    //                         $conflictFields["contact_person.$index.email"] = 'Email format is invalid.';
    //                     }

    //                     // Check uniqueness for contact person mobile
    //                     if (!empty($c_mobile)) {
    //                         if (DB::table('distributors')->where('mobile_no', $c_mobile)->exists() || DB::table('dealers')->where('mobile_no', $c_mobile)->exists() || DB::table('distributor_contact_persons_details')->where('mobile_no', $c_mobile)->exists() || DB::table('dealer_contact_persons_details')->where('mobile_no', $c_mobile)->exists()) {
    //                             $conflictFields["contact_person.$index.mobile_no"] = 'This contact mobile number is already in use.';
    //                         }
    //                     }

    //                     // Check uniqueness for contact person email
    //                     if (!empty($c_email)) {
    //                         if (DB::table('distributors')->where('email', $c_email)->exists() || DB::table('dealers')->where('email', $c_email)->exists() || DB::table('distributor_contact_persons_details')->where('email', $c_email)->exists() || DB::table('dealer_contact_persons_details')->where('email', $c_email)->exists()) {
    //                             $conflictFields["contact_person.$index.email"] = 'This contact email is already in use.';
    //                         }
    //                     }
    //                 }
    //             }
    //         }

    //         if (!empty($conflictFields)) {
    //             return response()->json(
    //                 [
    //                     'status' => false,
    //                     'message' => 'Validation failed',
    //                     'errors' => $conflictFields,
    //                 ],
    //                 422,
    //             );
    //         }

    //         // Begin transaction
    //         DB::beginTransaction();

    //         // Create dealer
    //         $dealer = Dealer::create([
    //             'name' => $request->name,
    //             'code' => $request->code,
    //             'distributor_id' => $request->distributor_id,
    //             'mobile_no' => $request->mobile_no,
    //             'email' => $request->email,
    //             'gst_num' => $request->gst_num,
    //             'pan_num' => $request->pan_num,
    //             'order_limit' => $request->order_limit,
    //             'allowed_order_limit' => $request->order_limit,
    //             'remarks' => $request->remarks,
    //             'address' => $request->address,
    //             'pincode' => $request->pincode,
    //             'state_id' => $request->state_id,
    //             'city_id' => $request->city_id,
    //             'type' => $request->type,
    //             'bank_name' => $request->bank_name,
    //             'account_holder_name' => $request->account_holder_name,
    //             'ifsc_code' => $request->ifsc_code,
    //             'account_number' => $request->account_number,
    //         ]);

    //         // Create contact persons
    //         if ($request->has('contact_person')) {
    //             foreach ($request->contact_person as $contact) {
    //                 $dealer->contactPersons()->create([
    //                     'name' => $contact['name'],
    //                     'mobile_no' => $contact['mobile_no'],
    //                     'email' => $contact['email'],
    //                 ]);
    //             }
    //         }

    //         DB::commit();

    //         // Load relationships for response
    //         $dealer->load(['state', 'city', 'contactPersons']);

    //         return response()->json(
    //             [
    //                 'status' => true,
    //                 'message' => 'Dealer created successfully',
    //                 'data' => $dealer,
    //             ],
    //             201,
    //         );
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Dealer store error: ' . $e->getMessage());

    //         return response()->json(
    //             [
    //                 'status' => false,
    //                 'message' => 'Error creating dealer',
    //                 'error' => $e->getMessage(),
    //             ],
    //             500,
    //         );
    //     }
    // }

    // public function store(Request $request)
    // {
    //     try {
    //         // =============================================================
    //         // --- ADDED SECURITY CHECK ---
    //         // =============================================================
    //         $appUser = $request->user();

    //         // Check if a user is logged in and if their type is 'distributor'
    //         if (!$appUser || $appUser->type !== 'distributor') {
    //             return response()->json(
    //                 [
    //                     'status' => false,
    //                     'message' => 'Forbidden: Only authenticated distributors can create dealers.',
    //                 ],
    //                 403,
    //             ); // 403 Forbidden
    //         }
    //         // =============================================================
    //         // Filter contact persons (same logic as your original)
    //         if ($request->has('contact_person')) {
    //             $filtered = collect($request->input('contact_person'))
    //                 ->filter(function ($person) {
    //                     return !empty($person['name']) || !empty($person['mobile_no']) || !empty($person['email']);
    //                 })
    //                 ->values()
    //                 ->toArray();

    //             $request->merge(['contact_person' => $filtered]);
    //         }

    //         // Validation rules
    //         $validated = $request->validate([
    //             'name' => 'required|string|max:255',
    //             // 'code' => ['required', 'string', 'max:255'], // --- CHANGE 1: REMOVED ---
    //             'distributor_id' => 'nullable|integer|exists:distributors,id',
    //             'mobile_no' => ['required', 'digits:10'],
    //             'email' => ['nullable', 'email'],
    //             'gst_num' => ['nullable', 'string', 'max:50'],
    //             'pan_num' => ['nullable', 'string', 'max:50'],
    //             'order_limit' => ['nullable', 'numeric'],
    //             'remarks' => 'nullable|string',
    //             'address' => 'nullable|string',
    //             'pincode' => ['nullable', 'digits:6'],
    //             'state_id' => ['nullable', 'integer'],
    //             'city_id' => ['nullable', 'integer'],
    //             'type' => ['nullable', 'string'],
    //             'bank_name' => ['nullable', 'string', 'max:100'],
    //             'account_holder_name' => ['nullable', 'string', 'max:100'],
    //             'ifsc_code' => ['nullable', 'string', 'max:20'],
    //             'account_number' => ['nullable', 'string', 'max:30'],
    //             'contact_person.*.name' => ['nullable', 'string', 'max:255'],
    //             'contact_person.*.mobile_no' => ['nullable', 'digits:10'],
    //             'contact_person.*.email' => ['nullable', 'email', 'max:255'],
    //         ]);

    //         // Cross-table uniqueness check
    //         $conflictFields = [];

    //         // 1. Code uniqueness
    //         // --- CHANGE 2: REMOVED ---
    //         // if (DB::table('distributors')->where('code', $request->code)->exists() || DB::table('dealers')->where('code', $request->code)->exists()) {
    //         //     $conflictFields['code'] = 'This code is already in use.';
    //         // }

    //         // 2. Mobile No uniqueness
    //         if (DB::table('distributors')->where('mobile_no', $request->mobile_no)->exists() || DB::table('dealers')->where('mobile_no', $request->mobile_no)->exists() || DB::table('distributor_contact_persons_details')->where('mobile_no', $request->mobile_no)->exists() || DB::table('dealer_contact_persons_details')->where('mobile_no', $request->mobile_no)->exists()) {
    //             $conflictFields['mobile_no'] = 'This mobile number is already in use.';
    //         }

    //         // 3. Email uniqueness
    //         if ($request->email && (DB::table('distributors')->where('email', $request->email)->exists() || DB::table('dealers')->where('email', $request->email)->exists() || DB::table('distributor_contact_persons_details')->where('email', $request->email)->exists() || DB::table('dealer_contact_persons_details')->where('email', $request->email)->exists())) {
    //             $conflictFields['email'] = 'This email is already in use.';
    //         }

    //         // 4. GST & PAN uniqueness
    //         if ($request->gst_num && (DB::table('distributors')->where('gst_num', $request->gst_num)->exists() || DB::table('dealers')->where('gst_num', $request->gst_num)->exists())) {
    //             $conflictFields['gst_num'] = 'This GST number is already in use.';
    //         }

    //         if ($request->pan_num && (DB::table('distributors')->where('pan_num', $request->pan_num)->exists() || DB::table('dealers')->where('pan_num', $request->pan_num)->exists())) {
    //             $conflictFields['pan_num'] = 'This PAN number is already in use.';
    //         }

    //         // 5. Contact person validation and uniqueness
    //         // (Aapka original contact person logic bilkul sahi hai, use waise hi rakha hai)
    //         if ($request->has('contact_person')) {
    //             foreach ($request->contact_person as $index => $contact) {
    //                 // ... (Aapka poora original contact person logic yahan hai) ...
    //                 $c_mobile = $contact['mobile_no'] ?? null;
    //                 $c_email = $contact['email'] ?? null;
    //                 $hasAny = !empty($contact['name']) || !empty($c_mobile) || !empty($c_email);

    //                 if ($hasAny) {
    //                     if (empty($contact['name'])) {
    //                         $conflictFields["contact_person.$index.name"] = 'Name is required when filling contact person details.';
    //                     }
    //                     if (empty($c_mobile)) {
    //                         $conflictFields["contact_person.$index.mobile_no"] = 'Mobile number is required when filling contact person details.';
    //                     }
    //                     if (!empty($c_mobile) && !preg_match('/^\d{10}$/', $c_mobile)) {
    //                         $conflictFields["contact_person.$index.mobile_no"] = 'Mobile number must be exactly 10 digits.';
    //                     }
    //                     if (!empty($c_email) && !filter_var($c_email, FILTER_VALIDATE_EMAIL)) {
    //                         $conflictFields["contact_person.$index.email"] = 'Email format is invalid.';
    //                     }
    //                     if (!empty($c_mobile)) {
    //                         if (DB::table('distributors')->where('mobile_no', $c_mobile)->exists() || DB::table('dealers')->where('mobile_no', $c_mobile)->exists() || DB::table('distributor_contact_persons_details')->where('mobile_no', $c_mobile)->exists() || DB::table('dealer_contact_persons_details')->where('mobile_no', $c_mobile)->exists()) {
    //                             $conflictFields["contact_person.$index.mobile_no"] = 'This contact mobile number is already in use.';
    //                         }
    //                     }
    //                     if (!empty($c_email)) {
    //                         if (DB::table('distributors')->where('email', $c_email)->exists() || DB::table('dealers')->where('email', $c_email)->exists() || DB::table('distributor_contact_persons_details')->where('email', $c_email)->exists() || DB::table('dealer_contact_persons_details')->where('email', $c_email)->exists()) {
    //                             $conflictFields["contact_person.$index.email"] = 'This contact email is already in use.';
    //                         }
    //                     }
    //                 }
    //             }
    //         }


    //         if (!empty($conflictFields)) {
    //             return response()->json(
    //                 [
    //                     'status' => false,
    //                     'message' => 'Validation failed',
    //                     'errors' => $conflictFields,
    //                 ],
    //                 422,
    //             );
    //         }

    //         // Begin transaction
    //         DB::beginTransaction();

    //         // =============================================================
    //         // --- CHANGE 3: Generate Auto-Incrementing Code ---
    //         // =============================================================
    //         $prefix = 'DLR';
    //         // Dono tables se 'DLR' wale code dhoondho
    //         $dealerCodes = DB::table('dealers')->where('code', 'LIKE', $prefix . '%')->pluck('code');
    //         $distributorCodes = DB::table('distributors')->where('code', 'LIKE', $prefix . '%')->pluck('code');

    //         // Sabse bada number (numeric part) nikalo
    //         $maxNum = $dealerCodes->merge($distributorCodes)
    //             ->map(function ($code) use ($prefix) {
    //                 return (int) substr($code, strlen($prefix)); // 'DLR005' se '5' nikalega
    //             })
    //             ->max();

    //         // Naya number banao aur format karo (e.g., 6 -> '006' or 1000 -> '1000')
    //         $newNum = ($maxNum ?? 0) + 1;
    //         $newCode = $prefix . str_pad($newNum, 3, '0', STR_PAD_LEFT); // 'DLR006'
    //         // =============================================================


    //         // Create dealer
    //         $dealer = Dealer::create([
    //             'name' => $request->name,
    //             // 'code' => $request->code, // <-- OLD
    //             'code' => $newCode, // --- CHANGE 4: Use new generated code ---
    //             'distributor_id' => $request->distributor_id,
    //             'mobile_no' => $request->mobile_no,
    //             'email' => $request->email,
    //             'gst_num' => $request->gst_num,
    //             'pan_num' => $request->pan_num,
    //             'order_limit' => $request->order_limit,
    //             'allowed_order_limit' => $request->order_limit,
    //             'remarks' => $request->remarks,
    //             'address' => $request->address,
    //             'pincode' => $request->pincode,
    //             'state_id' => $request->state_id,
    //             'city_id' => $request->city_id,
    //             'type' => $request->type,
    //             'bank_name' => $request->bank_name,
    //             'account_holder_name' => $request->account_holder_name,
    //             'ifsc_code' => $request->ifsc_code,
    //             'account_number' => $request->account_number,
    //             'status' => 'Pending', // --- NOTE: Status 'Pending' set kar raha hoon
    //         ]);

    //         // Create contact persons
    //         if ($request->has('contact_person')) {
    //             foreach ($request->contact_person as $contact) {
    //                  // Sirf tabhi create karo jab data ho
    //                 if (!empty($contact['name']) && !empty($contact['mobile_no'])) {
    //                     $dealer->contactPersons()->create([
    //                         'name' => $contact['name'],
    //                         'mobile_no' => $contact['mobile_no'],
    //                         'email' => $contact['email'],
    //                     ]);
    //                 }
    //             }
    //         }

    //         DB::commit();

    //         // Load relationships for response
    //         $dealer->load(['state', 'city', 'contactPersons']);

    //         return response()->json(
    //             [
    //                 'status' => true,
    //                 'message' => 'Dealer created successfully',
    //                 'data' => $dealer,
    //             ],
    //             201,
    //         );
    //     } catch (ValidationException $e) { // --- CHANGE 5: Added specific catch ---
    //         // Isse $request->validate() ke errors pakde jaayenge
    //         return response()->json(['status' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Dealer store error: ' . $e->getMessage());

    //         // --- CHANGE 5 (Continued): Specific error for race condition ---
    //         if ($e instanceof \Illuminate\Database\QueryException && $e->errorInfo[1] == 1062) {
    //              return response()->json([
    //                  'status' => false,
    //                  'message' => 'Failed to generate a unique code (duplicate entry). Please try again.'
    //              ], 500); // 500 ya 409 Conflict
    //         }

    //         return response()->json(
    //             [
    //                 'status' => false,
    //                 'message' => 'Error creating dealer',
    //                 'error' => $e->getMessage(),
    //             ],
    //             500,
    //         );
    //     }
    // }


    public function store(Request $request)
    {
        try {
            // =============================================================
            // --- ADDED SECURITY CHECK ---
            // =============================================================
            $appUser = $request->user();

            if (!$appUser || $appUser->type !== 'distributor') {
                return response()->json(
                    ['status' => false, 'message' => 'Forbidden: Only authenticated distributors can create dealers.'], 403
                );
            }
            // =============================================================

            // Filter contact persons
            if ($request->has('contact_person')) {
                $filtered = collect($request->input('contact_person'))
                    ->filter(function ($person) {
                        return !empty($person['name']) || !empty($person['mobile_no']) || !empty($person['email']);
                    })
                    ->values()
                    ->toArray();
                $request->merge(['contact_person' => $filtered]);
            }

            // --- CHANGE 1: $request->validate() ko Validator::make() se badla ---

            // Validation rules
            $rules = [
                'name' => 'required|string|max:255',
                'distributor_id' => 'nullable|integer|exists:distributors,id',
                'mobile_no' => ['required', 'digits:10'],
                'email' => ['nullable', 'email'],
                'gst_num' => ['nullable', 'string', 'max:50'],
                'pan_num' => ['nullable', 'string', 'max:50'],
                'order_limit' => ['nullable', 'numeric'],
                'remarks' => 'nullable|string',
                'address' => 'nullable|string',
                'pincode' => ['nullable', 'digits:6'],
                'state_id' => ['nullable', 'integer'],
                'city_id' => ['nullable', 'integer'],
                'type' => ['nullable', 'string'],
                'bank_name' => ['nullable', 'string', 'max:100'],
                'account_holder_name' => ['nullable', 'string', 'max:100'],
                'ifsc_code' => ['nullable', 'string', 'max:20'],
                'account_number' => ['nullable', 'string', 'max:30'],
                'contact_person.*.name' => ['nullable', 'string', 'max:255'],
                'contact_person.*.mobile_no' => ['nullable', 'digits:10'],
                'contact_person.*.email' => ['nullable', 'email', 'max:255'],
            ];

            // Validator banayein
            $validator = Validator::make($request->all(), $rules);

            // Pehle validation ke errors nikalein (agar hain toh)
            $validationErrors = $validator->fails() ? $validator->errors()->all() : [];

            // --- Aapka manual validation (waisa hi) ---
            $conflictFields = [];

            // 2. Mobile No uniqueness
            if (DB::table('distributors')->where('mobile_no', $request->mobile_no)->exists() || DB::table('dealers')->where('mobile_no', $request->mobile_no)->exists() || DB::table('distributor_contact_persons_details')->where('mobile_no', $request->mobile_no)->exists() || DB::table('dealer_contact_persons_details')->where('mobile_no', $request->mobile_no)->exists()) {
                $conflictFields['mobile_no'] = 'This mobile number is already in use.';
            }
            // 3. Email uniqueness
            if ($request->email && (DB::table('distributors')->where('email', $request->email)->exists() || DB::table('dealers')->where('email', $request->email)->exists() || DB::table('distributor_contact_persons_details')->where('email', $request->email)->exists() || DB::table('dealer_contact_persons_details')->where('email', $request->email)->exists())) {
                $conflictFields['email'] = 'This email is already in use.';
            }
            // 4. GST & PAN uniqueness
            if ($request->gst_num && (DB::table('distributors')->where('gst_num', $request->gst_num)->exists() || DB::table('dealers')->where('gst_num', $request->gst_num)->exists())) {
                $conflictFields['gst_num'] = 'This GST number is already in use.';
            }
            if ($request->pan_num && (DB::table('distributors')->where('pan_num', $request->pan_num)->exists() || DB::table('dealers')->where('pan_num', $request->pan_num)->exists())) {
                $conflictFields['pan_num'] = 'This PAN number is already in use.';
            }
            // 5. Contact person validation
            if ($request->has('contact_person')) {
                foreach ($request->contact_person as $index => $contact) {
                    $c_mobile = $contact['mobile_no'] ?? null;
                    $c_email = $contact['email'] ?? null;
                    $hasAny = !empty($contact['name']) || !empty($c_mobile) || !empty($c_email);

                    if ($hasAny) {
                        if (empty($contact['name'])) {
                            $conflictFields["contact_person.$index.name"] = 'Name is required when filling contact person details.';
                        }
                        if (empty($c_mobile)) {
                            $conflictFields["contact_person.$index.mobile_no"] = 'Mobile number is required when filling contact person details.';
                        }
                        if (!empty($c_mobile) && !preg_match('/^\d{10}$/', $c_mobile)) {
                            $conflictFields["contact_person.$index.mobile_no"] = 'Mobile number must be exactly 10 digits.';
                        }
                        if (!empty($c_email) && !filter_var($c_email, FILTER_VALIDATE_EMAIL)) {
                            $conflictFields["contact_person.$index.email"] = 'Email format is invalid.';
                        }
                        if (!empty($c_mobile)) {
                            if (DB::table('distributors')->where('mobile_no', $c_mobile)->exists() || DB::table('dealers')->where('mobile_no', $c_mobile)->exists() || DB::table('distributor_contact_persons_details')->where('mobile_no', $c_mobile)->exists() || DB::table('dealer_contact_persons_details')->where('mobile_no', $c_mobile)->exists()) {
                                $conflictFields["contact_person.$index.mobile_no"] = 'This contact mobile number is already in use.';
                            }
                        }
                        if (!empty($c_email)) {
                            if (DB::table('distributors')->where('email', $c_email)->exists() || DB::table('dealers')->where('email', $c_email)->exists() || DB::table('distributor_contact_persons_details')->where('email', $c_email)->exists() || DB::table('dealer_contact_persons_details')->where('email', $c_email)->exists()) {
                                $conflictFields["contact_person.$index.email"] = 'This contact email is already in use.';
                            }
                        }
                    }
                }
            }

            // Manual errors ko bhi flat array mein daalein
            $conflictErrors = array_values($conflictFields);

            // --- CHANGE 2: Dono errors ko merge (combine) karein ---
            $allErrors = array_merge($validationErrors, $conflictErrors);

            // Check karein ki koi bhi error hai ya nahi
            if (!empty($allErrors)) {

                // --- CHANGE 3: Errors ko naye format mein badlein ---
                $formattedErrors = collect($allErrors)->map(function ($message) {
                    // Image ke according, har error ek object hai jismein "Reason" key hai
                    return ['reason' => $message];
                })->all();

                // Naya response format bhejein
                return response()->json(
                    [
                        'status' => false, // Image ke according (Capital S)
                        'message' => 'validations failed', // Image ke according (Capital M)
                        'errors' => $formattedErrors, // Image ke according (Capital E)
                    ],
                    422,
                );
            }

            // Agar koi error nahi hai, toh validated data lein
            $validatedData = $validator->validated();

            // Begin transaction
            DB::beginTransaction();

            // ... (Code Generation Logic waisa hi hai) ...
            $prefix = 'DLR';
            $dealerCodes = DB::table('dealers')->where('code', 'LIKE', $prefix . '%')->pluck('code');
            $distributorCodes = DB::table('distributors')->where('code', 'LIKE', $prefix . '%')->pluck('code');
            $maxNum = $dealerCodes->merge($distributorCodes)
                ->map(function ($code) use ($prefix) {
                    return (int) substr($code, strlen($prefix));
                })
                ->max();
            $newNum = ($maxNum ?? 0) + 1;
            $newCode = $prefix . str_pad($newNum, 3, '0', STR_PAD_LEFT);


            // --- CHANGE 4: Dealer::create() ko $validatedData se update kiya ---
            $dealer = Dealer::create([
                'name' => $validatedData['name'], // $request->name ki jagah
                'code' => $newCode,
                'distributor_id' => $validatedData['distributor_id'] ?? null,
                'mobile_no' => $validatedData['mobile_no'],
                'email' => $validatedData['email'] ?? null,
                'gst_num' => $validatedData['gst_num'] ?? null,
                'pan_num' => $validatedData['pan_num'] ?? null,
                'order_limit' => $validatedData['order_limit'] ?? null,
                'allowed_order_limit' => $validatedData['order_limit'] ?? null,
                'remarks' => $validatedData['remarks'] ?? null,
                'address' => $validatedData['address'] ?? null,
                'pincode' => $validatedData['pincode'] ?? null,
                'state_id' => $validatedData['state_id'] ?? null,
                'city_id' => $validatedData['city_id'] ?? null,
                'type' => $validatedData['type'] ?? null,
                'bank_name' => $validatedData['bank_name'] ?? null,
                'account_holder_name' => $validatedData['account_holder_name'] ?? null,
                'ifsc_code' => $validatedData['ifsc_code'] ?? null,
                'account_number' => $validatedData['account_number'] ?? null,
                'status' => 'Pending',
                'created_by' => $appUser->name, // --- ADDED CREATED_BY FIELD ---
            ]);

            // Create contact persons
            if (!empty($validatedData['contact_person'])) {
                foreach ($validatedData['contact_person'] as $contact) {
                    if (!empty($contact['name']) && !empty($contact['mobile_no'])) {
                        $dealer->contactPersons()->create([
                            'name' => $contact['name'],
                            'mobile_no' => $contact['mobile_no'],
                            'email' => $contact['email'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            $superAdminRole = \Spatie\Permission\Models\Role::where('name', 'Super Admin')->first();

            if ($superAdminRole) {
                $superAdmins = User::role($superAdminRole)->get();
            } else {
                \Log::warning('Super Admin role not found!');
                $superAdmins = collect();
            }

            Notification::send($superAdmins, new NewDealerAdded($dealer));

            // Load relationships for response
            $dealer->load(['state', 'city', 'contactPersons']);

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Dealer created successfully',
                    'data' => $dealer,
                ],
                201,
            );

        } catch (ValidationException $e) { // --- CHANGE 5: Catch block ko bhi update kiya ---

            // Errors ko flat array banayein
            $allErrors = collect($e->errors())->flatten()->all();

            // Naye format mein badlein
            $formattedErrors = collect($allErrors)->map(function ($message) {
                return ['reason' => $message];
            })->all();

            return response()->json(
                [
                    'status' => false,
                    'message' => 'validations failed',
                    'errors' => $formattedErrors,
                ],
                422,
            );

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Dealer store error: ' . $e->getMessage());

            if ($e instanceof \Illuminate\Database\QueryException && $e->errorInfo[1] == 1062) {
                 return response()->json([
                     'status' => false,
                     'message' => 'Failed to generate a unique code (duplicate entry). Please try again.'
                 ], 500);
            }

            return response()->json(
                [
                    'status' => false,
                    'message' => 'Error creating dealer',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    // Update The Dealer
    public function update(Request $request, Dealer $dealer)
    {
        // 1. --- STATUS CHECK ---
        // First, check if the dealer's status is 'Pending'.
        if ($dealer->status === 'Pending') {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'This dealer is pending approval and cannot be updated.',
                ],
                403,
            );
        }

        try {
            // Filter empty contact person rows before validation
            if ($request->has('contact_person')) {
                $filtered = collect($request->input('contact_person'))
                    ->filter(function ($person) {
                        return !empty($person['name']) || !empty($person['mobile_no']) || !empty($person['email']);
                    })
                    ->values()
                    ->toArray();
                $request->merge(['contact_person' => $filtered]);
            }

            // 2. --- MAIN VALIDATION ---
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'distributor_id' => 'nullable|integer|exists:distributors,id',
                'code' => ['sometimes', 'string', 'max:255', Rule::unique('dealers')->ignore($dealer->id)],
                'mobile_no' => ['required', 'digits:10', Rule::unique('dealers')->ignore($dealer->id)],
                'email' => ['nullable', 'email', Rule::unique('dealers')->ignore($dealer->id)],
                'gst_num' => ['nullable', 'string', 'max:50', Rule::unique('dealers')->ignore($dealer->id)],
                'pan_num' => ['nullable', 'string', 'max:50', Rule::unique('dealers')->ignore($dealer->id)],
                'order_limit' => ['required', 'numeric'],
                'remarks' => 'nullable|string',
                'address' => 'nullable|string',
                'pincode' => ['nullable', 'digits:6'],
                'state_id' => ['nullable', 'integer'],
                'city_id' => ['nullable', 'integer'],
                'type' => ['required', 'string'],
                'bank_name' => ['nullable', 'string', 'max:100'],
                'account_holder_name' => ['nullable', 'string', 'max:100'],
                'ifsc_code' => ['nullable', 'string', 'max:20'],
                'account_number' => ['nullable', 'string', 'max:30'],
                'contact_person.*.name' => ['required_with:contact_person.*.mobile_no', 'string', 'max:255'],
                'contact_person.*.mobile_no' => ['required_with:contact_person.*.name', 'digits:10'],
                'contact_person.*.email' => ['nullable', 'email', 'max:255'],
            ]);

            // 3. --- CUSTOM VALIDATION FOR CONTACT PERSON UNIQUENESS ---
            if ($request->has('contact_person')) {
                foreach ($request->contact_person as $index => $contact) {
                    // Check mobile number uniqueness (excluding contacts of the current dealer)
                    if (!empty($contact['mobile_no'])) {
                        $isDuplicateMobile = DB::table('dealer_contact_persons_details')
                            ->where('mobile_no', $contact['mobile_no'])
                            ->where('dealer_id', '!=', $dealer->id) // Ignore current dealer's contacts
                            ->exists();
                        if ($isDuplicateMobile) {
                            return response()->json(
                                [
                                    'status' => false,
                                    'message' => 'Validation failed',
                                    'errors' => ["contact_person.$index.mobile_no" => ['This mobile number is already in use by another contact.']],
                                ],
                                422,
                            );
                        }
                    }

                    // Check email uniqueness (excluding contacts of the current dealer)
                    if (!empty($contact['email'])) {
                        $isDuplicateEmail = DB::table('dealer_contact_persons_details')
                            ->where('email', $contact['email'])
                            ->where('dealer_id', '!=', $dealer->id) // Ignore current dealer's contacts
                            ->exists();
                        if ($isDuplicateEmail) {
                            return response()->json(
                                [
                                    'status' => false,
                                    'message' => 'Validation failed',
                                    'errors' => ["contact_person.$index.email" => ['This email is already in use by another contact.']],
                                ],
                                422,
                            );
                        }
                    }
                }
            }

            // 4. --- DATABASE TRANSACTION ---
            DB::beginTransaction();

            $contactPersonData = $validated['contact_person'] ?? null;
            unset($validated['contact_person']);

            $dealer->update($validated);

            if ($contactPersonData !== null) {
                $dealer->contactPersons()->delete();
                foreach ($contactPersonData as $contact) {
                    $dealer->contactPersons()->create([
                        'name' => $contact['name'],
                        'mobile_no' => $contact['mobile_no'],
                        'email' => $contact['email'] ?? null,
                    ]);
                }
            }

            DB::commit();

            $dealer->load(['state', 'city', 'contactPersons', 'distributor']);

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Dealer updated successfully',
                    'data' => $dealer,
                ],
                200,
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Dealer update error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Error updating dealer', 'error' => $e->getMessage()], 500);
        }
    }
    
    public function requestMyOrderLimit(Request $request)
    {
        // --- Step 1: Get the authenticated app user ---
        $appUser = $request->user();

        // Security Check 1: User must be a dealer.
        if (!$appUser || $appUser->type !== 'dealer') {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Forbidden: Only dealers can perform this action.',
                ],
                403,
            );
        }

        // --- Step 2: Find the dealer's profile from the authenticated user ---
        $dealer = \App\Models\Dealer::where('code', $appUser->code)->first();
        if (!$dealer) {
            return response()->json(['status' => false, 'message' => 'Dealer profile not found.'], 404);
        }

        // --- Step 3: Validate the request data ---
        $validated = $request->validate([
            'desired_order_limit' => 'required|numeric|min:1',
            'remarks' => 'nullable|string|max:1000',
        ]);

        // --- Step 4: Check for an existing pending request ---
        $existingRequest = \App\Models\DealerOrderLimitRequest::where('dealer_id', $dealer->id)->where('status', 'Pending')->exists();
        if ($existingRequest) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'You already have a pending order limit request. Please wait for it to be processed.',
                ],
                422,
            );
        }

        // --- Step 5: Create the limit request ---
        try {
            $limitRequest = \App\Models\DealerOrderLimitRequest::create([
                'dealer_id' => $dealer->id, // Security Check 2: ID is taken from the token, not the request.
                'order_limit' => $dealer->order_limit,
                'desired_order_limit' => $validated['desired_order_limit'],
                'remarks' => $validated['remarks'] ?? null,
                'status' => 'Pending',
            ]);

            // Notify Super Admins
            $superAdminRole = \Spatie\Permission\Models\Role::where('name', 'Super Admin')->first();

            if ($superAdminRole) {
                $superAdmins = User::role($superAdminRole)->get();
            } else {
                \Log::warning('Super Admin role not found!');
                $superAdmins = collect();
            }

            $data = [
                'request_id' => $limitRequest->id,
                'name' => $dealer->name,
                'order_limit' => $dealer->order_limit,
                'desired_order_limit' => $validated['desired_order_limit'],
                'type' => 'Dealer',
            ];

            Notification::send($superAdmins, new \App\Notifications\OrderLimitRequested($data));

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Your request to change the order limit has been submitted successfully.',
                    'data' => $limitRequest,
                ],
                201,
            );
        } catch (\Exception $e) {
            Log::error('Order limit request failed: ' . $e->getMessage());
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Something went wrong. Please try again later.',
                ],
                500,
            );
        }
    }

    // Inactive The Dealer
    public function deactivate(Dealer $dealer)
    {
        if ($dealer->status === 'Inactive') {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'This dealer is already inactive.',
                ],
                409,
            );
        }

        try {
            $dealer->status = 'Inactive';
            $dealer->save();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Dealer has been successfully set to Inactive.',
                    'data' => $dealer,
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Something went wrong. Please try again later.',
                ],
                500,
            );
        }
    }

    // public function storeByDistributor(Request $request)
    // {
    //     // --- Step 1: Logged-in User ko Get aur Validate Karo ---
    //     $appUser = $request->user();

    //     // Sirf distributor hi is action ko perform kar sakta hai
    //     if ($appUser->type !== 'distributor') {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Forbidden: Only distributors can add new dealers.'
    //         ], 403);
    //     }

    //     // Distributor ka poora profile get karo uske code se
    //     $distributor = Distributor::where('code', $appUser->code)->first();
    //     if (!$distributor) {
    //         return response()->json(['status' => false, 'message' => 'Distributor profile not found.'], 404);
    //     }

    //     // --- Step 2: Naye Dealer ke Data ko Validate Karo ---
    //     $validatedData = $request->validate([
    //         'name'          => 'required|string|max:255',
    //         'code'          => 'required|string|unique:dealers,code',
    //         'mobile_no'     => 'required|string|unique:dealers,mobile_no',
    //         'email'         => 'nullable|email|unique:dealers,email',
    //         'status'        => 'required|in:Active,Inactive,Pending',
    //         'type'          => 'required|in:Wholesale,Retail',
    //         'address'       => 'nullable|string',
    //         'state_id'      => 'required|integer|exists:states,id', // State zaroori hai
    //         'city_id'       => 'nullable|integer|exists:cities,id',
    //         'order_limit'   => 'required|integer|min:0',
    //         'allowed_order_limit' => 'required|integer|min:0',
    //     ]);

    //     // --- Step 3: SABSE ZAROORI BUSINESS RULE - State Check ---
    //     if ($distributor->state_id != $validatedData['state_id']) {
    //         // Agar state match nahi karta, toh validation error throw karo
    //         throw ValidationException::withMessages([
    //             'state_id' => ['The new dealer must be in the same state as the distributor.']
    //         ]);
    //     }

    //     // --- Step 4: Dealer Data Prepare aur Auto-Assign Karo ---
    //     $dealerData = $validatedData;
    //     // Yeh hai "auto-assign" logic
    //     $dealerData['distributor_id'] = $distributor->id;

    //     // --- Step 5: Naya Dealer Create Karo ---
    //     $dealer = Dealer::create($dealerData);

    //     // (Optional) Yahan aap distributor team/limit update karne ka logic daal sakte hain
    //     // $distributor->increment('allowed_order_limit', $dealer->order_limit);

    //     // --- Step 6: Success Response Bhejo ---
    //     return response()->json([
    //         'status' => true,
    //         'message' => 'New dealer created and assigned to your team successfully!',
    //         'data' => $dealer
    //     ], 201); // 201 Created
    // }

    // public function storeByDistributor(Request $request)
    // {
    //     // --- Step 1: Logged-in Distributor ko Get aur Validate Karo ---
    //     $appUser = $request->user();
    //     if ($appUser->type !== 'distributor') {
    //         return response()->json(['status' => false, 'message' => 'Forbidden: Only distributors can perform this action.'], 403);
    //     }
    //     $distributor = Distributor::where('code', $appUser->code)->firstOrFail();

    //     // --- Step 2: Naye Dealer ke Data ko Validate Karo ---
    //     $validatedData = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'code' => ['required', 'string', new UniqueAcrossMultipleTables(['dealers', 'distributors'])],
    //         'mobile_no' => ['required', 'digits:10', new UniqueAcrossMultipleTables(['dealers', 'distributors', 'dealer_contact_persons_details', 'distributor_contact_persons_details'])],
    //         'email' => ['nullable', 'email', new UniqueAcrossMultipleTables(['dealers', 'distributors', 'dealer_contact_persons_details', 'distributor_contact_persons_details'])],
    //         'gst_num' => ['nullable', 'string', 'max:50', new UniqueAcrossMultipleTables(['dealers', 'distributors'])],
    //         'pan_num' => ['nullable', 'string', 'max:50', new UniqueAcrossMultipleTables(['dealers', 'distributors'])],
    //         'order_limit' => 'required|integer|min:0',
    //         'allowed_order_limit' => 'required|integer|min:0',
    //         'state_id' => 'required|integer|exists:states,id',
    //         'type' => 'required|in:Wholesale,Retail',
    //         // ... baaki nullable fields
    //         'contact_person' => 'nullable|array',
    //         'contact_person.*.name' => 'required_with:contact_person|string',
    //         'contact_person.*.mobile_no' => ['required_with:contact_person', 'digits:10', new UniqueAcrossMultipleTables(['dealers', 'distributors', 'dealer_contact_persons_details', 'distributor_contact_persons_details'])],
    //         'contact_person.*.email' => ['nullable', 'email', new UniqueAcrossMultipleTables(['dealers', 'distributors', 'dealer_contact_persons_details', 'distributor_contact_persons_details'])],
    //     ]);

    //     // Wrap everything in a transaction for safety
    //     DB::beginTransaction();
    //     try {
    //         // --- Step 3: Dealer Data Prepare aur Auto-Assign Karo ---
    //         $dealerData = $validatedData;
    //         $dealerData['distributor_id'] = $distributor->id;
    //         $dealerData['status'] = 'Active'; // Zaroori: Team mein turant add karne ke liye

    //         // --- Step 4: Naya Dealer Create Karo ---
    //         $dealer = Dealer::create($dealerData);

    //         // --- Step 5: Contact Persons Add Karo ---
    //         if (!empty($validatedData['contact_person'])) {
    //             $dealer->contactPersons()->createMany($validatedData['contact_person']);
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'New dealer created and added to your team successfully!',
    //             'data' => $dealer->load('contactPersons')
    //         ], 201);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         \Log::error('Dealer creation by distributor failed: ' . $e->getMessage());
    //         return response()->json(['status' => false, 'message' => 'An unexpected error occurred.'], 500);
    //     }
    // }

    // public function storeByDistributor(Request $request)
    // {
    //     try {
    //         // --- Step 1: Get Logged-in Distributor ---
    //         $appUser = $request->user();
    //         if ($appUser->type !== 'distributor') {
    //             return response()->json(['status' => false, 'message' => 'Forbidden: Only distributors can perform this action.'], 403);
    //         }
    //         $distributor = Distributor::where('code', $appUser->code)->firstOrFail();

    //         // =============================================================
    //         // --- Step 2: NEW BUSINESS RULE - Check if a team exists ---
    //         // =============================================================
    //         $teamExists = DistributorTeam::where('distributor_id', $distributor->id)->exists();
    //         if (!$teamExists) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'You must create a team first before you can add dealers.'
    //             ], 422); // 422 Unprocessable Entity - a business rule failed
    //         }

    //         // --- Step 3: Validate New Dealer's Data ---
    //         $validatedData = $request->validate([
    //             'name' => 'required|string|max:255',
    //             'code' => ['required', 'string', new UniqueAcrossMultipleTables(['dealers', 'distributors'])],
    //             'state_id' => 'required|integer|exists:states,id',
    //             // ... all your other validation rules ...
    //             'contact_person' => 'nullable|array',
    //         ]);

    //         // --- Step 4: State Match Check ---
    //         if ($distributor->state_id != $validatedData['state_id']) {
    //             throw ValidationException::withMessages([
    //                 'state_id' => ['The new dealer must be in the same state as you.']
    //             ]);
    //         }

    //         DB::beginTransaction();

    //         // =============================================================
    //         // --- Step 5: FIX - Separate dealer data from contact person data ---
    //         // =============================================================
    //         $dealerPayload = collect($validatedData)->except('contact_person')->toArray();
    //         $contactPersonsPayload = $validatedData['contact_person'] ?? [];

    //         // Auto-assign distributor_id and set status to Active
    //         $dealerPayload['distributor_id'] = $distributor->id;
    //         $dealerPayload['status'] = 'Active';

    //         // --- Step 6: Create the Dealer and Contact Persons ---
    //         $dealer = Dealer::create($dealerPayload);

    //         if (!empty($contactPersonsPayload)) {
    //             $dealer->contactPersons()->createMany($contactPersonsPayload);
    //         }

    //         // (Optional) You can add logic here to update the DistributorTeam record,
    //         // for example, incrementing the `no_of_dealers` count.

    //         DB::commit();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'New dealer created and added to your team successfully!',
    //             'data' => $dealer->load('contactPersons')
    //         ], 201);

    //     } catch (ValidationException $e) {
    //         // This will catch the state_id validation error
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Validation failed',
    //             'errors' => $e->errors()
    //         ], 422);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         \Log::error('Dealer creation by distributor failed: ' . $e->getMessage());
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'An unexpected error occurred. Please check the logs.'
    //         ], 500);
    //     }
    // }

    // In app/Http/Controllers/Api/DealerController.php

    public function storeByDistributor(Request $request)
    {
        try {
            // --- Step 1: Get Logged-in Distributor ---
            $appUser = $request->user();
            if ($appUser->type !== 'distributor') {
                return response()->json(['status' => false, 'message' => 'Forbidden: Only distributors can perform this action.'], 403);
            }
            $distributor = Distributor::where('code', $appUser->code)->firstOrFail();

            // --- Step 2: Check if a team exists ---
            $teamExists = DistributorTeam::where('distributor_id', $distributor->id)->exists();
            if (!$teamExists) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'You must create a team first before you can add dealers.',
                    ],
                    422,
                );
            }

            // =============================================================
            // --- Step 3: FIX - COMPLETE VALIDATION BLOCK ---
            // =============================================================
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'code' => ['required', 'string', new UniqueAcrossMultipleTables(['dealers', 'distributors'])],
                'mobile_no' => ['required', 'digits:10', new UniqueAcrossMultipleTables(['dealers', 'distributors', 'dealer_contact_persons_details', 'distributor_contact_persons_details'])],
                'email' => ['nullable', 'email', new UniqueAcrossMultipleTables(['dealers', 'distributors', 'dealer_contact_persons_details', 'distributor_contact_persons_details'])],
                'gst_num' => ['nullable', 'string', 'max:50', new UniqueAcrossMultipleTables(['dealers', 'distributors'])],
                'pan_num' => ['nullable', 'string', 'max:50', new UniqueAcrossMultipleTables(['dealers', 'distributors'])],
                'order_limit' => 'required|integer|min:0',
                'allowed_order_limit' => 'required|integer|min:0',
                'state_id' => 'required|integer|exists:states,id',
                'city_id' => 'nullable|integer|exists:cities,id',
                'type' => 'required|in:Wholesale,Retail',
                'status' => 'required|in:Active,Inactive,Pending',
                'address' => 'nullable|string',
                'pincode' => 'nullable|digits:6',
                'bank_name' => 'nullable|string',
                'account_holder_name' => 'nullable|string',
                'ifsc_code' => 'nullable|string',
                'account_number' => 'nullable|string',
                'remarks' => 'nullable|string',
                'contact_person' => 'nullable|array',
                'contact_person.*.name' => 'required_with:contact_person|string',
                'contact_person.*.mobile_no' => ['required_with:contact_person', 'digits:10', new UniqueAcrossMultipleTables(['dealers', 'distributors', 'dealer_contact_persons_details', 'distributor_contact_persons_details'])],
                'contact_person.*.email' => ['nullable', 'email', new UniqueAcrossMultipleTables(['dealers', 'distributors', 'dealer_contact_persons_details', 'distributor_contact_persons_details'])],
            ]);

            // --- Step 4: State Match Check ---
            if ($distributor->state_id != $validatedData['state_id']) {
                throw ValidationException::withMessages(['state_id' => ['The new dealer must be in the same state as you.']]);
            }

            DB::beginTransaction();

            // --- Step 5: Separate payloads ---
            $dealerPayload = collect($validatedData)->except('contact_person')->toArray();
            $contactPersonsPayload = $validatedData['contact_person'] ?? [];

            // Auto-assign distributor_id and override status to Active
            $dealerPayload['distributor_id'] = $distributor->id;
            $dealerPayload['status'] = 'Active';

            // --- Step 6: Create the Dealer and Contact Persons ---
            $dealer = Dealer::create($dealerPayload); // This will now have mobile_no and all other fields

            if (!empty($contactPersonsPayload)) {
                $dealer->contactPersons()->createMany($contactPersonsPayload);
            }

            DB::commit();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'New dealer created and added to your team successfully!',
                    'data' => $dealer->load('contactPersons'),
                ],
                201,
            );
        } catch (ValidationException $e) {
            return response()->json(['status' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Dealer creation by distributor failed: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'An unexpected error occurred. Please check the logs.'], 500);
        }
    }
}
