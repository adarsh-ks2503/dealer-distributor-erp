<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\State;
use App\Models\City;
use App\Models\Distributor;
use App\Models\DistributorContactPersonsDetail;
use App\Models\AppUserManagement;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\DistributorOrderLimitRequest;
use App\Models\DistributorTeam;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewDistributorAdded;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OrderLimitRequestStatusChanged;

class DistributorController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:Distributors-Index', ['only' => ['index']]);
        $this->middleware('permission:Distributors-Create', ['only' => ['create', 'store']]);
        $this->middleware('permission:Distributors-Edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:Distributors-View', ['only' => ['show']]);
        $this->middleware('permission:Distributors-InActive', ['only' => ['checkInactivation', 'inactivate']]);
        $this->middleware('permission:Distributors-Active', ['only' => ['activate']]);
        $this->middleware('permission:Distributors-OrderLimitRequests', ['only' => ['olRequests', 'storeOrderLimitRequest']]);
        $this->middleware('permission:Distributors-OrderLimitChange', ['only' => ['olRequestApprove', 'olRequestReject']]);
    }

    public function index()
    {
        $distributors = Distributor::with(['state', 'city', 'contactPersons'])
            ->orderBy('created_at', 'DESC')
            ->get();
        $olRequest = DistributorOrderLimitRequest::where('status', 'pending')->count();
        return view('dealers_distributors.distributors.index', compact('distributors', 'olRequest'));
    }

    public function create()
    {
        $states = State::all();
        return view('dealers_distributors.distributors.create', compact('states'));
    }

    public function store(Request $request)
    {
        if ($request->has('contact_person')) {
            $filtered = collect($request->input('contact_person'))->filter(function ($person) {
                return !empty($person['name']) || !empty($person['mobile_no']) || !empty($person['email']);
            })->values()->toArray();

            $request->merge(['contact_person' => $filtered]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:255'],
            'mobile_no' => ['required', 'digits:10'],
            'email' => ['nullable', 'email'],
            'gst_num' => ['nullable', 'string', 'max:50'],
            'pan_num' => ['nullable', 'string', 'max:50'],
            'order_limit' => ['nullable', 'numeric'],
            'remarks' => 'nullable|string',
            'address' => 'nullable|string',
            'pincode' => ['nullable', 'digits:6'],
            'state_id' => ['nullable', 'integer', 'exists:states,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'account_holder_name' => ['nullable', 'string', 'max:100'],
            'ifsc_code' => ['nullable', 'string', 'max:20'],
            'account_number' => ['nullable', 'string', 'max:30'],
            'contact_person.*.name' => ['nullable', 'string', 'max:255'],
            'contact_person.*.mobile_no' => ['nullable', 'digits:10'],
            'contact_person.*.email' => ['nullable', 'email', 'max:255'],
        ]);

        $conflictFields = [];

        if ($request->has('contact_person')) {
            foreach ($request->contact_person as $index => $contact) {
                $hasAny = !empty($contact['name']) || !empty($contact['mobile_no']) || !empty($contact['email']);

                if ($hasAny) {
                    if (empty($contact['name'])) {
                        $conflictFields["contact_person.$index.name"] = "Name is required when filling contact person details.";
                    }

                    if (empty($contact['mobile_no'])) {
                        $conflictFields["contact_person.$index.mobile_no"] = "Mobile number is required when filling contact person details.";
                    }

                    if (!empty($contact['mobile_no']) && !preg_match('/^\d{10}$/', $contact['mobile_no'])) {
                        $conflictFields["contact_person.$index.mobile_no"] = "Mobile number must be exactly 10 digits.";
                    }

                    if (!empty($contact['email']) && !filter_var($contact['email'], FILTER_VALIDATE_EMAIL)) {
                        $conflictFields["contact_person.$index.email"] = "Email format is invalid.";
                    }
                }
            }
        }

        if (
            DB::table('distributors')->where('code', $request->code)->exists() ||
            DB::table('dealers')->where('code', $request->code)->whereIn('status', ['Active','Pending','Inactive'])->exists()
        ) {
            $conflictFields['code'] = 'This code is already in use.';
        }

        if (
            DB::table('distributors')->where('mobile_no', $request->mobile_no)->exists() ||
            DB::table('dealers')->where('mobile_no', $request->mobile_no)->whereIn('status', ['Active','Pending','Inactive'])->exists()
            // DB::table('distributor_contact_persons_details')->where('mobile_no', $request->mobile_no)->exists() ||
            // DB::table('dealer_contact_persons_details')->where('mobile_no', $request->mobile_no)->exists()
        ) {
            $conflictFields['mobile_no'] = 'This mobile number is already in use.';
        }

        if ($request->email && (
            DB::table('distributors')->where('email', $request->email)->exists() ||
            DB::table('dealers')->where('email', $request->email)->whereIn('status', ['Active','Pending','Inactive'])->exists()
            // DB::table('distributor_contact_persons_details')->where('email', $request->email)->exists() ||
            // DB::table('dealer_contact_persons_details')->where('email', $request->email)->exists()
        )) {
            $conflictFields['email'] = 'This email is already in use.';
        }

        if ($request->gst_num && (
            DB::table('distributors')->where('gst_num', $request->gst_num)->exists() ||
            DB::table('dealers')->where('gst_num', $request->gst_num)->whereIn('status', ['Active','Pending','Inactive'])->exists()
        )) {
            $conflictFields['gst_num'] = 'This GST number is already in use.';
        }

        if ($request->pan_num && (
            DB::table('distributors')->where('pan_num', $request->pan_num)->exists() ||
            DB::table('dealers')->where('pan_num', $request->pan_num)->whereIn('status', ['Active','Pending','Inactive'])->exists()
        )) {
            $conflictFields['pan_num'] = 'This PAN number is already in use.';
        }

        // if ($request->has('contact_person')) {
        //     foreach ($request->contact_person as $index => $contact) {
        //         $c_mobile = $contact['mobile_no'];
        //         $c_email = $contact['email'];

        //         if (!empty($c_mobile) && (
        //             DB::table('distributors')->where('mobile_no', $c_mobile)->exists() ||
        //             DB::table('dealers')->where('mobile_no', $c_mobile)->exists() ||
        //             DB::table('distributor_contact_persons_details')->where('mobile_no', $c_mobile)->exists() ||
        //             DB::table('dealer_contact_persons_details')->where('mobile_no', $c_mobile)->exists()
        //         )) {
        //             $conflictFields["contact_person.$index.mobile_no"] = "This contact mobile number is already in use.";
        //         }

        //         if (!empty($c_email) && (
        //             DB::table('distributors')->where('email', $c_email)->exists() ||
        //             DB::table('dealers')->where('email', $c_email)->exists() ||
        //             DB::table('distributor_contact_persons_details')->where('email', $c_email)->exists() ||
        //             DB::table('dealer_contact_persons_details')->where('email', $c_email)->exists()
        //         )) {
        //             $conflictFields["contact_person.$index.email"] = "This contact email is already in use.";
        //         }
        //     }
        // }

        if (!empty($conflictFields)) {
            throw ValidationException::withMessages($conflictFields);
        }

        $creatorName = Auth::user()
                ? trim(Auth::user()->name . ' ' . (Auth::user()->last_name ?? ''))
                : null;

        DB::beginTransaction();

        try {
            $distributor = Distributor::create([
                'name' => $request->name,
                'code' => $request->code,
                'mobile_no' => $request->mobile_no,
                'email' => $request->email,
                'gst_num' => $request->gst_num,
                'pan_num' => $request->pan_num,
                'order_limit' => $request->order_limit,
                'allowed_order_limit' => $request->order_limit,
                'individual_allowed_order_limit' => $request->order_limit,
                'remarks' => $request->remarks,
                'address' => $request->address,
                'pincode' => $request->pincode,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'bank_name' => $request->bank_name,
                'account_holder_name' => $request->account_holder_name,
                'ifsc_code' => $request->ifsc_code,
                'account_number' => $request->account_number,
                'created_by' => $creatorName,
            ]);

            // Create user in app_user_management with state_id and city_id
            AppUserManagement::create([
                'name' => $distributor->name,
                'type' => 'distributor',
                'code' => $distributor->code,
                'email' => $distributor->email,
                'mobile_no' => $distributor->mobile_no,
                'password' => null,
                'status' => 'Active',
                'state_id' => $distributor->state_id,
                'city_id' => $distributor->city_id,
            ]);

            if ($request->has('contact_person')) {
                foreach ($request->contact_person as $contact) {
                    $distributor->contactPersons()->create([
                        'name' => $contact['name'],
                        'mobile_no' => $contact['mobile_no'],
                        'email' => $contact['email'],
                    ]);
                }
            }

            DB::commit();

            // Notify super admins by database and email (via Laravel channels)
            $superAdminRole = \Spatie\Permission\Models\Role::where('name', 'Super Admin')->first();

            if ($superAdminRole) {
                $superAdmins = User::role($superAdminRole)->get();
            } else {
                \Log::warning('Super Admin role not found!');
                $superAdmins = collect();
            }

            Notification::send($superAdmins, new NewDistributorAdded($distributor));

            return redirect()->route('distributors.index')->with('success', 'Distributor created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('distributors.index')->with('error', $e->getMessage() . ' An error occurred while saving the distributor.')->withInput();
        }
    }

    public function show($id)
    {
        $distributor = Distributor::with(['state', 'city', 'contactPersons'])->findOrFail($id);
        return view('dealers_distributors.distributors.show', compact('distributor'));
    }

    public function edit($id)
    {
        $distributor = Distributor::with('contactPersons')->findOrFail($id);
        $states = State::all();

        return view('dealers_distributors.distributors.edit', compact('distributor', 'states'));
    }

    public function update(Request $request, $id)
    {
        if ($request->has('contact_person')) {
            $filtered = collect($request->input('contact_person'))->filter(function ($person) {
                return !empty($person['name']) || !empty($person['mobile_no']) || !empty($person['email']);
            })->values()->toArray();

            $request->merge(['contact_person' => $filtered]);
        }
        $distributor = Distributor::with('contactPersons')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:255'],
            'mobile_no' => ['required', 'digits:10'],
            'email' => ['nullable', 'email'],
            'gst_num' => ['nullable', 'string', 'max:50'],
            'pan_num' => ['nullable', 'string', 'max:50'],
            'order_limit' => ['nullable', 'numeric'],
            'remarks' => 'nullable|string',
            'address' => 'nullable|string',
            'pincode' => 'nullable|digits:6',
            'state_id' => ['nullable', 'integer', 'exists:states,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'account_holder_name' => ['nullable', 'string', 'max:100'],
            'ifsc_code' => ['nullable', 'string', 'max:20'],
            'account_number' => ['nullable', 'string', 'max:30'],
            'contact_person.*.name' => ['nullable', 'string', 'max:255'],
            'contact_person.*.mobile_no' => ['nullable', 'digits:10'],
            'contact_person.*.email' => ['nullable', 'email', 'max:255'],
        ]);

        $conflictFields = [];

        if (
            DB::table('distributors')->where('code', $request->code)->where('id', '!=', $distributor->id)->exists() ||
            DB::table('dealers')->where('code', $request->code)->whereIn('status', ['Active','Pending','Inactive'])->exists()
        ) {
            $conflictFields['code'] = 'This code is already in use.';
        }

        if ($request->mobile_no) {
            if (
                DB::table('distributors')->where('mobile_no', $request->mobile_no)->where('id', '!=', $distributor->id)->exists() ||
                DB::table('dealers')->where('mobile_no', $request->mobile_no)->whereIn('status', ['Active','Pending','Inactive'])->exists()
                // DB::table('distributor_contact_persons_details')->where('mobile_no', $request->mobile_no)->exists() ||
                // DB::table('dealer_contact_persons_details')->where('mobile_no', $request->mobile_no)->exists()
            ) {
                $conflictFields['mobile_no'] = 'This mobile number is already in use.';
            }
        }

        if ($request->email) {
            if (
                DB::table('distributors')->where('email', $request->email)->where('id', '!=', $distributor->id)->exists() ||
                DB::table('dealers')->where('email', $request->email)->whereIn('status', ['Active','Pending','Inactive'])->exists()
                // DB::table('distributor_contact_persons_details')->where('email', $request->email)->exists() ||
                // DB::table('dealer_contact_persons_details')->where('email', $request->email)->exists()
            ) {
                $conflictFields['email'] = 'This email is already in use.';
            }
        }

        if ($request->gst_num && (
            DB::table('distributors')->where('gst_num', $request->gst_num)->where('id', '!=', $distributor->id)->exists() ||
            DB::table('dealers')->where('gst_num', $request->gst_num)->whereIn('status', ['Active','Pending','Inactive'])->exists()
        )) {
            $conflictFields['gst_num'] = 'This GST number is already in use.';
        }

        if ($request->pan_num && (
            DB::table('distributors')->where('pan_num', $request->pan_num)->where('id', '!=', $distributor->id)->exists() ||
            DB::table('dealers')->where('pan_num', $request->pan_num)->whereIn('status', ['Active','Pending','Inactive'])->exists()
        )) {
            $conflictFields['pan_num'] = 'This PAN number is already in use.';
        }

        if ($request->has('contact_person')) {
            foreach ($request->contact_person as $index => $contact) {
                $hasAny = !empty($contact['name']) || !empty($contact['mobile_no']) || !empty($contact['email']);

                if ($hasAny) {
                    if (empty($contact['name'])) {
                        $conflictFields["contact_person.$index.name"] = "Name is required when filling contact person details.";
                    }

                    if (empty($contact['mobile_no'])) {
                        $conflictFields["contact_person.$index.mobile_no"] = "Mobile number is required when filling contact person details.";
                    }

                    if (!empty($contact['mobile_no']) && !preg_match('/^\d{10}$/', $contact['mobile_no'])) {
                        $conflictFields["contact_person.$index.mobile_no"] = "Mobile number must be exactly 10 digits.";
                    }

                    if (!empty($contact['email']) && !filter_var($contact['email'], FILTER_VALIDATE_EMAIL)) {
                        $conflictFields["contact_person.$index.email"] = "Email format is invalid.";
                    }
                }
            }
        }

        if (!empty($conflictFields)) {
            throw ValidationException::withMessages($conflictFields);
        }

        DB::beginTransaction();

        try {
            $distributor->update([
                'name' => $request->name,
                'code' => $request->code,
                'mobile_no' => $request->mobile_no,
                'email' => $request->email,
                'gst_num' => $request->gst_num,
                'pan_num' => $request->pan_num,
                'order_limit' => $request->order_limit,
                'allowed_order_limit' => $request->order_limit,
                'remarks' => $request->remarks,
                'address' => $request->address ?? null,
                'pincode' => $request->pincode ?? null,
                'state_id' => $request->state_id ?? null,
                'city_id' => $request->city_id ?? null,
                'bank_name' => $request->bank_name ?? null,
                'account_holder_name' => $request->account_holder_name ?? null,
                'ifsc_code' => $request->ifsc_code ?? null,
                'account_number' => $request->account_number ?? null,
            ]);

            // Update app_user_management if exists
            $user = AppUserManagement::where('code', $distributor->code)->first();
            if ($user) {
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'mobile_no' => $request->mobile_no,
                    'state_id' => $request->state_id,
                    'city_id' => $request->city_id,
                ]);
            }

            $distributor->contactPersons()->delete();

            if ($request->has('contact_person')) {
                foreach ($request->contact_person as $contact) {
                    if (!empty($contact['name']) || !empty($contact['mobile_no']) || !empty($contact['email'])) {
                        $distributor->contactPersons()->create([
                            'name' => $contact['name'],
                            'mobile_no' => $contact['mobile_no'],
                            'email' => $contact['email'],
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('distributors.index')->with('success', 'Distributor updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('distributors.index')->with('error', $e->getMessage() . ' An error occurred while updating the distributor.');
        }
    }

    public function cities($state_id)
    {
        return City::where('state_id', $state_id)->get();
    }

    public function requestMyOrderLimit(Request $request)
    {
        $request->validate([
            'distributor_id' => 'required|exists:distributors,id',
            'desired_order_limit' => 'required|numeric|min:1',
            'submission_token' => 'required|string',
        ]);

        $token = $request['submission_token'];
        $sessionKey = 'submission_token_' . $token;

        if (session()->has($sessionKey)) {
            return redirect()->back()->with('error', 'This form has already been submitted.');
        }

        $existingPending = DistributorOrderLimitRequest::where('distributor_id', $request->distributor_id)
            ->where('status', 'Pending')
            ->exists();

        if ($existingPending) {
            return redirect()->back()->with('error', 'You already have a pending order limit change request. Please resolve the pending request made earlier before submitting a new one.');
        }

        session()->put($sessionKey, true);
        $distributor = Distributor::where('id', $request->distributor_id)->first();

        $limitRequest = DistributorOrderLimitRequest::create([
            'distributor_id' => $request->distributor_id,
            'order_limit' => $distributor->order_limit,
            'desired_order_limit' => $request->desired_order_limit,
            'status' => 'Pending',
            'remarks' => $request->remarks,
        ]);

        $superAdmins = User::whereHas('roles', function ($q) {
            $q->where('name', 'Super Admin');
        })->get();

        $data = [
            'request_id' => $limitRequest->id,
            'name' => $distributor->name,
            'order_limit' => $distributor->order_limit,
            'desired_order_limit' => $request['desired_order_limit'],
            'type' => 'Distributor',
        ];

        if ($superAdmins->isNotEmpty()) {
            Notification::send($superAdmins, new \App\Notifications\OrderLimitRequested($data));
        }

        return redirect()->route('distributors.index')->with('success', 'Order limit change request submitted successfully.');
    }


    public function olRequests()
    {
        $requests = DistributorOrderLimitRequest::with('distributor')->orderBy('created_at', 'Desc')->get();
        return view('dealers_distributors.distributors.olRequest', compact('requests'));
    }

    public function olRequestApprove($id, Request $request)
    {
        $olReq = DistributorOrderLimitRequest::findOrFail($id);
        $distributor = Distributor::findOrFail($olReq->distributor_id);
        $team = DistributorTeam::where('distributor_id', $distributor->id)->first();

        $orderQty = $distributor->order_limit - $distributor->allowed_order_limit;
        $difference = $olReq->desired_order_limit - $distributor->order_limit;

        if ($olReq->desired_order_limit > $distributor->order_limit) {
            $distributor->order_limit = $olReq->desired_order_limit;
            $distributor->allowed_order_limit += $difference;
            $distributor->individual_allowed_order_limit += $difference;
            $distributor->save();
        } elseif ($olReq->desired_order_limit < $distributor->order_limit && $olReq->desired_order_limit > $orderQty) {
            $distributor->order_limit = $olReq->desired_order_limit;
            $distributor->allowed_order_limit += $difference;
            $distributor->individual_allowed_order_limit += $difference;
            $distributor->save();
        } else {
            return back()->with('error', 'Cannot Approve the Order Limit Request as the desired order limit ' . $olReq->desired_order_limit . ' MT is less than the pending/approved order quantity of ' . $orderQty . ' MT for this distributor');
        }

        $dealerCount = $distributor->dealer()->count();
        if ($dealerCount > 0) {
            $dealersTotalLimit = $distributor->dealer()->sum('order_limit');
            $team->total_order_limit = $distributor->order_limit + $dealersTotalLimit;
            $team->save();
        } else {
            $distributor->allowed_order_limit = $distributor->order_limit;
        }

        $distributor->save();
        $olReq->update([
            'status_change_remarks' => $request->status_change_remarks,
            'status' => 'Approved',
        ]);

        // ----------------- Send Notification -----------------
        try {
            $appUser = AppUserManagement::where('code', $distributor->code)
                ->where('type', 'distributor')
                ->first();

            $webUser = \App\Models\User::where('name', 'LIKE', "%{$distributor->name}%")
                ->orWhere('email', $distributor->email)
                ->first();

            $notifiables = collect();
            if ($appUser) $notifiables->push($appUser);
            if ($webUser) $notifiables->push($webUser);

            if ($notifiables->isNotEmpty()) {
                $data = [
                    'request_id' => $olReq->id,
                    'status' => 'Approved',
                    'remarks' => $request->status_change_remarks,
                    'message' => "Your order limit request has been approved."
                ];

                Notification::send($notifiables, new OrderLimitRequestStatusChanged($data));
            } else {
                \Log::warning("No notifiable user found for distributor order limit request #{$olReq->id}");
            }
        } catch (\Exception $e) {
            \Log::error("Failed to send notification for distributor order limit request #{$olReq->id}: " . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Distributor Order Limit Request approved successfully.');
    }

    public function olRequestReject($id, Request $request)
    {
        $olReq = DistributorOrderLimitRequest::findOrFail($id);
        $distributor = Distributor::findOrFail($olReq->distributor_id);

        $olReq->update([
            'status_change_remarks' => $request->status_change_remarks,
            'status' => 'Rejected',
        ]);

        // ----------------- Send Notification -----------------
        try {
            $appUser = AppUserManagement::where('code', $distributor->code)
                ->where('type', 'distributor')
                ->first();

            $webUser = \App\Models\User::where('name', 'LIKE', "%{$distributor->name}%")
                ->orWhere('email', $distributor->email)
                ->first();

            $notifiables = collect();
            if ($appUser) $notifiables->push($appUser);
            if ($webUser) $notifiables->push($webUser);

            if ($notifiables->isNotEmpty()) {
                $data = [
                    'request_id' => $olReq->id,
                    'status' => 'Rejected',
                    'remarks' => $request->status_change_remarks,
                    'message' => "Your order limit request has been rejected."
                ];

                Notification::send($notifiables, new OrderLimitRequestStatusChanged($data));
            } else {
                \Log::warning("No notifiable user found for distributor order limit request #{$olReq->id}");
            }
        } catch (\Exception $e) {
            \Log::error("Failed to send notification for distributor order limit request #{$olReq->id}: " . $e->getMessage());
        }

        return redirect()->back()->with('error', 'Distributor Order Limit Request Rejected');
    }

    public function checkInactivation($id)
    {
        $distributor = Distributor::findOrFail($id);

        $hasOrders = Order::where('placed_by_distributor_id', $id)
            ->whereIn('status', ['Pending', 'Approved', 'Partial Dispatch'])
            ->exists();

        if ($hasOrders) {
            return response()->json([
                'blocked' => true,
                'message' => 'Distributor has pending/approved/partial-dispatch orders. Cannot inactivate.'
            ]);
        }

        $hasTeam = DistributorTeam::where('distributor_id', $id)->where('status', 'Active')->exists();

        if ($hasTeam) {
            return response()->json([
                'blocked' => true,
                'message' => 'Distributor has assigned dealers in team. Cannot inactivate.'
            ]);
        }

        return response()->json(['blocked' => false]);
    }

    public function inactivate(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $distributor = Distributor::findOrFail($request->id);

        DB::beginTransaction();

        try {
            $distributor->status = 'Inactive';
            $distributor->save();

            // Inactivate user in app_user_management
            $user = AppUserManagement::where('code', $distributor->code)->first();
            if ($user) {
                $user->status = 'Inactive';
                $user->save();
            }

            DB::commit();

            return redirect()->back()->with('success', 'Distributor and associated user marked as inactive.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function activate(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $distributor = Distributor::where('status', 'Inactive')->findOrFail($request->id);
        $distributor->status = 'Active';
        $distributor->save();

        // Activate user in app_user_management
        $user = AppUserManagement::where('code', $distributor->code)->first();
        if ($user) {
            $user->status = 'Active';
            $user->save();
        }

        return redirect()->back()->with('success', 'Distributor and associated user marked as active.');
    }
}
