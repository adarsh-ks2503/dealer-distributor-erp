<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Distributor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\DistributorOrderLimitRequest;
use App\Models\DistributorTeam;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

class DistributorController extends Controller
{
    public function index()
    {
        try {
            // $dealers = Dealer::with(['state', 'city', 'contactPersons'])->get();
            $dealers = Distributor::with(['state', 'city', 'contactPersons'])
                ->whereIn('status', ['Active'])
                ->get();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Distributors retrieved successfully',
                    'data' => $dealers,
                ],
                200,
            );
        } catch (\Exception $e) {
            Log::error('Distributors index error: ' . $e->getMessage());

            return response()->json(
                [
                    'status' => false,
                    'message' => 'Error retrieving Distributors',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function store(Request $request)
    {
        try {
            // Filter empty contact person rows
            if ($request->has('contact_person')) {
                $filtered = collect($request->input('contact_person'))
                    ->filter(function ($person) {
                        return !empty($person['name']) || !empty($person['mobile_no']) || !empty($person['email']);
                    })
                    ->values()
                    ->toArray();
                $request->merge(['contact_person' => $filtered]);
            }

            // Validation rules adapted for Distributor
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => ['required', 'string', 'max:255'],
                // 'distributor_id' => 'nullable|integer|exists:distributors,id', // REMOVED
                'mobile_no' => ['required', 'digits:10'],
                'email' => ['nullable', 'email'],
                'gst_num' => ['nullable', 'string', 'max:50'],
                'pan_num' => ['nullable', 'string', 'max:50'],
                'order_limit' => ['required', 'numeric'], // CHANGED to required
                'individual_allowed_order_limit' => ['required', 'numeric'], // ADDED
                'remarks' => 'nullable|string',
                'address' => 'nullable|string',
                'pincode' => ['nullable', 'digits:6'],
                'state_id' => ['nullable', 'integer', 'exists:states,id'],
                'city_id' => ['nullable', 'integer', 'exists:cities,id'],
                // 'type' => ['nullable', 'string'], // REMOVED
                'bank_name' => ['nullable', 'string', 'max:100'],
                'account_holder_name' => ['nullable', 'string', 'max:100'],
                'ifsc_code' => ['nullable', 'string', 'max:20'],
                'account_number' => ['nullable', 'string', 'max:30'],
                'contact_person.*.name' => ['required_with:contact_person.*.mobile_no', 'string', 'max:255'],
                'contact_person.*.mobile_no' => ['required_with:contact_person.*.name', 'digits:10'],
                'contact_person.*.email' => ['nullable', 'email', 'max:255'],
            ]);

            // Cross-table uniqueness check
            $conflictFields = [];

            // 1. Code uniqueness
            if (DB::table('distributors')->where('code', $request->code)->exists() || DB::table('dealers')->where('code', $request->code)->exists()) {
                $conflictFields['code'] = 'This code is already in use.';
            }

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

            // 5. Contact person validation and uniqueness
            if ($request->has('contact_person')) {
                foreach ($request->contact_person as $index => $contact) {
                    $c_mobile = $contact['mobile_no'] ?? null;
                    $c_email = $contact['email'] ?? null;

                    // Check if contact person has any data
                    $hasAny = !empty($contact['name']) || !empty($c_mobile) || !empty($c_email);

                    if ($hasAny) {
                        // Require name and mobile if any data is present
                        if (empty($contact['name'])) {
                            $conflictFields["contact_person.$index.name"] = 'Name is required when filling contact person details.';
                        }

                        if (empty($c_mobile)) {
                            $conflictFields["contact_person.$index.mobile_no"] = 'Mobile number is required when filling contact person details.';
                        }

                        // Mobile format validation
                        if (!empty($c_mobile) && !preg_match('/^\d{10}$/', $c_mobile)) {
                            $conflictFields["contact_person.$index.mobile_no"] = 'Mobile number must be exactly 10 digits.';
                        }

                        // Email format validation
                        if (!empty($c_email) && !filter_var($c_email, FILTER_VALIDATE_EMAIL)) {
                            $conflictFields["contact_person.$index.email"] = 'Email format is invalid.';
                        }

                        // Check uniqueness for contact person mobile
                        if (!empty($c_mobile)) {
                            if (DB::table('distributors')->where('mobile_no', $c_mobile)->exists() || DB::table('dealers')->where('mobile_no', $c_mobile)->exists() || DB::table('distributor_contact_persons_details')->where('mobile_no', $c_mobile)->exists() || DB::table('dealer_contact_persons_details')->where('mobile_no', $c_mobile)->exists()) {
                                $conflictFields["contact_person.$index.mobile_no"] = 'This contact mobile number is already in use.';
                            }
                        }

                        // Check uniqueness for contact person email
                        if (!empty($c_email)) {
                            if (DB::table('distributors')->where('email', $c_email)->exists() || DB::table('dealers')->where('email', $c_email)->exists() || DB::table('distributor_contact_persons_details')->where('email', $c_email)->exists() || DB::table('dealer_contact_persons_details')->where('email', $c_email)->exists()) {
                                $conflictFields["contact_person.$index.email"] = 'This contact email is already in use.';
                            }
                        }
                    }
                }
            }

            if (!empty($conflictFields)) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Validation failed',
                        'errors' => $conflictFields,
                    ],
                    422,
                );
            }

            // Begin transaction
            DB::beginTransaction();

            // Create distributor
            $distributor = Distributor::create([
                // CHANGED to Distributor
                'name' => $request->name,
                'code' => $request->code,
                // 'distributor_id' => $request->distributor_id, // REMOVED
                'mobile_no' => $request->mobile_no,
                'email' => $request->email,
                'gst_num' => $request->gst_num,
                'pan_num' => $request->pan_num,
                'order_limit' => $request->order_limit,
                'allowed_order_limit' => $request->order_limit,
                'individual_allowed_order_limit' => $request->individual_allowed_order_limit, // ADDED
                'remarks' => $request->remarks,
                'address' => $request->address,
                'pincode' => $request->pincode,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                // 'type' => $request->type, // REMOVED
                'bank_name' => $request->bank_name,
                'account_holder_name' => $request->account_holder_name,
                'ifsc_code' => $request->ifsc_code,
                'account_number' => $request->account_number,
            ]);

            // Create contact persons
            if ($request->has('contact_person')) {
                // Assumes Distributor model has a 'contactPersons' relationship
                $distributor->contactPersons()->createMany($request->contact_person);
            }

            DB::commit();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Distributor created successfully', // CHANGED message
                    'data' => $distributor->load(['state', 'city', 'contactPersons']),
                ],
                201,
            );
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Distributor store error: ' . $e->getMessage()); // CHANGED log message
            return response()->json(['status' => false, 'message' => 'An error occurred while saving the distributor.'], 500);
        }
    }

    // Update The Dealer
    public function update(Request $request, Distributor $distributor)
    {
        // 1. --- STATUS CHECK ---
        // Assuming Distributors can also have a 'Pending' status
        if ($distributor->status === 'Pending') {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'This distributor is pending approval and cannot be updated.',
                ],
                403,
            );
        }

        try {
            // Filter empty contact person rows
            if ($request->has('contact_person')) {
                $filtered = collect($request->input('contact_person'))
                    ->filter(function ($person) {
                        return !empty($person['name']) || !empty($person['mobile_no']) || !empty($person['email']);
                    })
                    ->values()
                    ->toArray();
                $request->merge(['contact_person' => $filtered]);
            }

            // 2. --- VALIDATION (Adapted for Distributor) ---
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                // 'distributor_id' => '...', // REMOVED
                'code' => ['sometimes', 'string', 'max:255', Rule::unique('distributors')->ignore($distributor->id)], // CHANGED table to 'distributors'
                'mobile_no' => ['required', 'digits:10', Rule::unique('distributors')->ignore($distributor->id)], // CHANGED table to 'distributors'
                'email' => ['nullable', 'email', Rule::unique('distributors')->ignore($distributor->id)], // CHANGED table to 'distributors'
                'gst_num' => ['nullable', 'string', 'max:50', Rule::unique('distributors')->ignore($distributor->id)], // CHANGED table to 'distributors'
                'pan_num' => ['nullable', 'string', 'max:50', Rule::unique('distributors')->ignore($distributor->id)], // CHANGED table to 'distributors'
                'order_limit' => ['required', 'numeric'],
                'individual_allowed_order_limit' => ['required', 'numeric'], // ADDED
                'remarks' => 'nullable|string',
                'address' => 'nullable|string',
                'pincode' => ['nullable', 'digits:6'],
                'state_id' => ['nullable', 'integer'],
                'city_id' => ['nullable', 'integer'],
                // 'type' => '...', // REMOVED
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
                    if (!empty($contact['mobile_no'])) {
                        // CHANGED table name and foreign key
                        $isDuplicateMobile = DB::table('distributor_contact_persons_details')->where('mobile_no', $contact['mobile_no'])->where('distributor_id', '!=', $distributor->id)->exists();
                        if ($isDuplicateMobile) {
                            return response()->json(['status' => false, 'message' => 'Validation failed', 'errors' => ["contact_person.$index.mobile_no" => ['This mobile number is already in use.']]], 422);
                        }
                    }
                    if (!empty($contact['email'])) {
                        // CHANGED table name and foreign key
                        $isDuplicateEmail = DB::table('distributor_contact_persons_details')->where('email', $contact['email'])->where('distributor_id', '!=', $distributor->id)->exists();
                        if ($isDuplicateEmail) {
                            return response()->json(['status' => false, 'message' => 'Validation failed', 'errors' => ["contact_person.$index.email" => ['This email is already in use.']]], 422);
                        }
                    }
                }
            }

            // 4. --- DATABASE TRANSACTION ---
            DB::beginTransaction();

            $contactPersonData = $validated['contact_person'] ?? null;
            unset($validated['contact_person']);

            $distributor->update($validated); // CHANGED to $distributor

            if ($contactPersonData !== null) {
                // Assumes Distributor model has a 'contactPersons' relationship
                $distributor->contactPersons()->delete();
                foreach ($contactPersonData as $contact) {
                    $distributor->contactPersons()->create([
                        'name' => $contact['name'],
                        'mobile_no' => $contact['mobile_no'],
                        'email' => $contact['email'] ?? null,
                    ]);
                }
            }

            DB::commit();

            // Note: 'distributor' relationship does not exist on the Distributor model
            $distributor->load(['state', 'city', 'contactPersons']);

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Distributor updated successfully', // CHANGED message
                    'data' => $distributor,
                ],
                200,
            );
        } catch (ValidationException $e) {
            return response()->json(['status' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Distributor update error: ' . $e->getMessage()); // CHANGED log message
            return response()->json(['status' => false, 'message' => 'Error updating distributor', 'error' => $e->getMessage()], 500);
        }
    }

    public function requestMyOrderLimit(Request $request)
    {
        // --- Step 1: Get the authenticated app user and their profile ---
        $appUser = $request->user();

        if ($appUser->type !== 'distributor') {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Forbidden: Only distributors can perform this action.',
                ],
                403,
            );
        }

        $distributor = Distributor::where('code', $appUser->code)->first();
        if (!$distributor) {
            return response()->json(['status' => false, 'message' => 'Distributor profile not found for this user.'], 404);
        }

        // --- Step 2: FIX - Simplified Validation ---
        // We only validate the fields that the request table can store.
        $validated = $request->validate([
            'desired_order_limit' => 'required|numeric|min:1',
            'remarks' => 'nullable|string|max:1000',
        ]);

        // --- Step 3: Check for an existing pending request ---
        // $existingRequest = DistributorOrderLimitRequest::where('distributor_id', $distributor->id)
        //     ->where('status', 'Pending')
        //     ->exists();

        // if ($existingRequest) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'You already have a pending order limit request. Please wait for it to be processed.'
        //      ], 422);
        // }

        // --- Step 4: Create the limit request record ---
        try {
            // --- FIX: Only create with columns that exist in the table ---
            $limitRequest = DistributorOrderLimitRequest::create([
                'distributor_id' => $distributor->id,
                'order_limit' => $distributor->order_limit, // The current limit
                'desired_order_limit' => $validated['desired_order_limit'], // The new desired limit
                'remarks' => $validated['remarks'] ?? null,
                'status' => 'Pending',
            ]);

            $superAdmins = User::whereHas('roles', function ($q) {
                $q->where('name', 'Super Admin');
            })->get();

            $data = [
                'request_id' => $limitRequest->id,
                'name' => $distributor->name,
                'order_limit' => $distributor->order_limit,
                'desired_order_limit' => $validated['desired_order_limit'],
                'type' => 'Distributor',
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
            Log::error('Distributor order limit request failed: ' . $e->getMessage());
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Something went wrong. Please try again later.',
                ],
                500,
            );
        }
    }

    public function deactivate(Distributor $distributor)
    {
        // CHANGED: Type-hint Distributor
        if ($distributor->status === 'Inactive') {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'This distributor is already inactive.', // CHANGED message
                ],
                409,
            );
        }

        try {
            $distributor->status = 'Inactive';
            $distributor->save();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Distributor has been successfully set to Inactive.', // CHANGED message
                    'data' => $distributor,
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

    // In app/Http/Controllers/Api/DistributorController.php

    // public function getMyTeam(Request $request)
    // {
    //     // ... (Your existing code to get the user and distributor profile)
    //     $appUser = $request->user();
    //     if ($appUser->type !== 'distributor') { /* ... error response ... */ }
    //     $distributor = Distributor::where('code', $appUser->code)->first();
    //     if (!$distributor) { /* ... error response ... */ }

    //     // --- Find the team ---
    //     $team = DistributorTeam::where('distributor_id', $distributor->id)->first();
    //     if (!$team) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'No team has been configured for you yet.'
    //         ], 404);
    //     }

    //     // --- THE FIX IS HERE ---
    //     // Fetch the dealers, specifying the table name for each column
    //     $dealers = $team->dealers()
    //         ->select(
    //             'dealers.id',
    //             'dealers.name',
    //             'dealers.code',
    //             'dealers.order_limit',
    //             'dealers.allowed_order_limit',
    //             'dealers.status'
    //         )
    //         ->orderBy('dealers.name', 'asc')
    //         ->get();

    //     // --- Return the successful response ---
    //     return response()->json([
    //         'status' => true,
    //         'data' => [
    //             'team_details' => [
    //                 'id' => $team->id,
    //                 'status' => $team->status,
    //                 'total_order_limit' => $team->total_order_limit,
    //                 'no_of_dealers' => $team->no_of_dealers,
    //             ],
    //             'dealers' => $dealers
    //         ]
    //     ], 200);
    // }

    // public function getMyTeam(Request $request)
    // {
    //     // --- Step 1: Get the authenticated app user and their profile ---
    //     $appUser = $request->user();

    //     if ($appUser->type !== 'distributor') {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Forbidden: Only distributors can perform this action.'
    //         ], 403);
    //     }

    //     $distributor = Distributor::where('code', $appUser->code)->first();
    //     if (!$distributor) {
    //         return response()->json(['status' => false, 'message' => 'Distributor profile not found.'], 404);
    //     }

    //     // --- Step 2: Find the team ---
    //     $team = DistributorTeam::where('distributor_id', $distributor->id)->first();
    //     if (!$team) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'No team has been configured for you yet.'
    //         ], 404);
    //     }

    //     // --- Step 3: Naya code - Pagination aur Sorting parameters ---
    //     $perPage = $request->query('per_page', 15); // Default 15 dealers per page
    //     $order = $request->query('order', 'asc');

    //     // Sort karne ke liye allowed columns (Security ke liye)
    //     $allowedSortColumns = ['name', 'code', 'order_limit', 'allowed_order_limit', 'status'];
    //     $sortByRaw = $request->query('sort_by', 'name'); // Default 'name' se sort

    //     // Agar user koi galat column ka naam daale, toh default 'name' use karo
    //     if (!in_array($sortByRaw, $allowedSortColumns)) {
    //         $sortByRaw = 'name';
    //     }

    //     // Column ke aage 'dealers.' lagana zaroori hai (ambiguous error se bachne ke liye)
    //     $sortBy = 'dealers.' . $sortByRaw;

    //     // --- Step 4: THE FIX IS HERE - Query ko Paginate karo ---
    //     $dealers = $team->dealers()
    //         ->select(
    //             'dealers.id',
    //             'dealers.name',
    //             'dealers.code',
    //             'dealers.order_limit',
    //             'dealers.allowed_order_limit',
    //             'dealers.status'
    //         )
    //         ->orderBy($sortBy, $order)  // Dynamic sorting
    //         ->paginate($perPage);       // get() ki jagah paginate()

    //     // --- Step 5: Pagination links mein parameters add karo ---
    //     $dealers->appends($request->except('page'));

    //     // --- Step 6: Return the successful response ---
    //     return response()->json([
    //         'status' => true,
    //         'data' => [
    //             'team_details' => [
    //                 'id' => $team->id,
    //                 'status' => $team->status,
    //                 'total_order_limit' => $team->total_order_limit,
    //                 'no_of_dealers' => $team->no_of_dealers,
    //             ],
    //             // 'dealers' ab ek pagination object hoga
    //             'dealers' => $dealers
    //         ]
    //     ], 200);
    // }

    public function getMyTeam(Request $request)
    {
        // --- Step 1: Get the authenticated app user and their profile ---
        $appUser = $request->user();

        if ($appUser->type !== 'distributor') {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Forbidden: Only distributors can perform this action.',
                ],
                403,
            );
        }

        $distributor = Distributor::where('code', $appUser->code)->first();
        if (!$distributor) {
            return response()->json(['status' => false, 'message' => 'Distributor profile not found.'], 404);
        }

        // --- Step 2: Find the team ---
        // $team = DistributorTeam::where('distributor_id', $distributor->id)->first();
        // if (!$team) {
        //     return response()->json(
        //         [
        //             'status' => false,
        //             'message' => 'No team has been configured for you yet.',
        //         ],
        //         404,
        //     );
        // }

        // --- Step 2: Find the team ---
        $team = DistributorTeam::where('distributor_id', $distributor->id)->where('status', 'Active')->first();
        if (!$team) {

            // --- YAHAN BADLAAV KIYA GAYA HAI ---
            return response()->json(
                [
                    'status' => false,
                    'message' => 'No team has been configured for you yet.',
                    'data' => null // 'data' key add kar di gayi hai
                ],
                200 // HTTP status 404 se 200 kar diya gaya hai
            );
            // --- BADLAAV KHATAM ---
        }

        // ===================================================================
        // --- Step 3: Naya code - Pagination aur Sorting parameters ---
        // ===================================================================

        // 'per_page' optional hai (default 15)
        $perPage = $request->input('per_page', 1);
        $order = $request->input('order', 'asc'); // 'query()' ki jagah 'input()' behtar hai

        // Sort karne ke liye allowed columns (Security ke liye)
        $allowedSortColumns = ['name', 'code', 'order_limit', 'allowed_order_limit', 'status'];
        $sortByRaw = $request->input('sort_by', 'name'); // Default 'name' se sort

        // Agar user koi galat column ka naam daale, toh default 'name' use karo
        if (!in_array($sortByRaw, $allowedSortColumns)) {
            $sortByRaw = 'name';
        }

        // Column ke aage 'dealers.' lagana zaroori hai
        $sortBy = 'dealers.' . $sortByRaw;

        // ===================================================================
        // --- Step 4: Dealer list ke liye query banayein ---
        // ===================================================================

        // Pehle query start karo
        $query = $team->dealers()->wherePivot('status', 'Active')->select('dealers.id', 'dealers.name', 'dealers.code', 'dealers.order_limit', 'dealers.allowed_order_limit', 'dealers.status');

        // !! NAYA: Optional Search Filter (name aur code par) !!
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('dealers.name', 'like', $searchTerm)->orWhere('dealers.code', 'like', $searchTerm);
            });
        }

        // --- Step 5: Sorting aur Pagination apply karo ---
        // $dealersPaginator = $query
        //     ->orderBy($sortBy, $order) // Dynamic sorting
        //     ->paginate($perPage); // Paginate

        // $dealersPaginator = $query
        //     ->orderBy($sortBy, $order) // Dynamic sorting
        //     ->orderBy('dealers.id', 'asc') // Unique tie-breaker to fix duplicate bug
        //     ->paginate($perPage); // Paginate

        $dealersPaginator = $query
            ->orderBy($sortBy, $order) // Dynamic sorting
            ->orderBy('dealers.id', 'asc') // Unique tie-breaker (Pehle se fixed hai)

            // --- YAHAN BADLAAV KIYA GAYA HAI ---
            ->paginate($perPage, ['*'], 'current_page'); // Paginate
            // --- BADLAAV KHATAM ---

        // $dealers->appends(...) waali line API ke liye zaroori nahi hai,
        // kyunki hum custom JSON bhej rahe hain.

        // ===================================================================
        // --- Step 6: Custom JSON Response (jaisa Orders API mein tha) ---
        // ===================================================================

        return response()->json(
            [
                'status' => true,
                'data' => [
                    // Team details waise hi rahengi
                    'team_details' => [
                        'id' => $team->id,
                        'status' => $team->status,
                        'total_order_limit' => $team->total_order_limit,
                        'no_of_dealers' => $team->no_of_dealers,
                    ],

                    // 'dealers' key ab custom pagination object hoga
                    'dealers' => [
                        'data' => $dealersPaginator->items(),
                        'current_page' => $dealersPaginator->currentPage(),
                        'per_page' => (int) $dealersPaginator->perPage(),
                        'total' => $dealersPaginator->total(),
                        'total_pages' => $dealersPaginator->lastPage(),
                    ],
                ],
            ],
            200,
        );
    }
}
