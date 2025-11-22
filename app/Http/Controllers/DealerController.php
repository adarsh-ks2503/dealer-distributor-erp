<?php

namespace App\Http\Controllers;

use App\Models\Dealer;
use App\Models\State;
use App\Models\City;
use App\Models\Distributor;
use App\Models\DistributorContactPersonsDetail;
use App\Models\AppUserManagement;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\DealerOrderLimitRequest;
use App\Models\Order;
use App\Models\DistributorTeam;
use App\Models\DistributorTeamDealer;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewDealerAdded;
use App\Notifications\DealerApprovedNotification;
use App\Notifications\DealerRejectedNotification;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OrderLimitRequestStatusChanged;

class DealerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:Dealers-Index', ['only' => ['index']]);
        $this->middleware('permission:Dealers-Create', ['only' => ['create', 'store']]);
        $this->middleware('permission:Dealers-Edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:Dealers-View', ['only' => ['show']]);
        $this->middleware('permission:Dealers-Approve', ['only' => ['approvalRequests', 'approve', 'reject']]);
        $this->middleware('permission:Dealers-InActive', ['only' => ['checkInactivation', 'inactivate']]);
        $this->middleware('permission:Dealers-Active', ['only' => ['activate']]);
        $this->middleware('permission:Dealers-OrderLimitRequests', ['only' => ['olRequests', 'storeOrderLimitRequest']]);
        $this->middleware('permission:Dealers-OrderLimitChange', ['only' => ['olRequestApprove', 'olRequestReject']]);
    }

    public function index()
    {
        $dealers = Dealer::with(['state', 'city', 'contactPersons'])
            ->whereIn('status', ['Active', 'Inactive'])
            ->orderBy('created_at', 'DESC')
            ->get();
        $requestCount = Dealer::where('status', 'Pending')->count();
        $olRequest = DealerOrderLimitRequest::where('status', 'pending')->count();
        return view('dealers_distributors.dealers.index', compact('dealers', 'requestCount', 'olRequest'));
    }

    public function create()
    {
        $states = State::all();
        return view('dealers_distributors.dealers.create', compact('states'));
    }

    public function show($id)
    {
        $dealer = Dealer::with(['state', 'city', 'contactPersons', 'distributor'])->findOrFail($id);
        return view('dealers_distributors.dealers.show', compact('dealer'));
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
            'type' => ['nullable', 'string'],
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

        //         if (!empty($c_mobile)) {
        //             if (
        //                 DB::table('distributors')->where('mobile_no', $c_mobile)->exists() ||
        //                 DB::table('dealers')->where('mobile_no', $c_mobile)->exists() ||
        //                 DB::table('distributor_contact_persons_details')->where('mobile_no', $c_mobile)->exists() ||
        //                 DB::table('dealer_contact_persons_details')->where('mobile_no', $c_mobile)->exists()
        //             ) {
        //                 $conflictFields["contact_person.$index.mobile_no"] = "This contact mobile number is already in use.";
        //             }
        //         }

        //         if ($c_email && (
        //             DB::table('distributors')->where('email', $c_email)->exists() ||
        //             DB::table('dealers')->where('email', $c_email)->exists() ||
        //             DB::table('distributor_contact_persons_details')->where('email', $c_email)->exists() ||
        //             DB::table('dealer_contact_persons_details')->where('email', $c_email)->exists()
        //         )) {
        //             $conflictFields["contact_person.$index.email"] = "This contact email is already in use.";
        //         }
        //     }
        // }

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

        $creatorName = Auth::user()
                ? trim(Auth::user()->name . ' ' . (Auth::user()->last_name ?? ''))
                : null;

        DB::beginTransaction();

        try {
            $dealer = Dealer::create([
                'name' => $request->name,
                'code' => $request->code,
                'mobile_no' => $request->mobile_no,
                'email' => $request->email,
                'gst_num' => $request->gst_num,
                'pan_num' => $request->pan_num,
                'order_limit' => $request->order_limit,
                'allowed_order_limit' => $request->order_limit,
                'remarks' => $request->remarks,
                'address' => $request->address,
                'pincode' => $request->pincode,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'type' => $request->type,
                'bank_name' => $request->bank_name,
                'account_holder_name' => $request->account_holder_name,
                'ifsc_code' => $request->ifsc_code,
                'account_number' => $request->account_number,
                'created_by' => $creatorName,
            ]);

            if ($request->has('contact_person')) {
                foreach ($request->contact_person as $contact) {
                    $dealer->contactPersons()->create([
                        'name' => $contact['name'],
                        'mobile_no' => $contact['mobile_no'],
                        'email' => $contact['email'],
                    ]);
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

            return redirect()->route('dealers.index')->with('success', 'Dealer created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $states = State::all();
        $dealer = Dealer::with(['state', 'city', 'contactPersons'])->findOrFail($id);

        return view('dealers_distributors.dealers.edit', compact('dealer', 'states'));
    }

    public function update(Request $request, $id)
    {
        if ($request->has('contact_person')) {
            $filtered = collect($request->input('contact_person'))->filter(function ($person) {
                return !empty($person['name']) || !empty($person['mobile_no']) || !empty($person['email']);
            })->values()->toArray();

            $request->merge(['contact_person' => $filtered]);
        }
        $dealer = Dealer::with('contactPersons')->findOrFail($id);

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
            'type' => ['nullable', 'string'],
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
            DB::table('dealers')->where('code', $request->code)->where('id', '!=', $dealer->id)->whereIn('status', ['Active','Pending','Inactive'])->exists() ||
            DB::table('distributors')->where('code', $request->code)->exists()
        ) {
            $conflictFields['code'] = 'This code is already in use.';
        }

        if (
            DB::table('dealers')->where('mobile_no', $request->mobile_no)->whereIn('status', ['Active','Pending','Inactive'])->where('id', '!=', $dealer->id)->exists() ||
            DB::table('distributors')->where('mobile_no', $request->mobile_no)->exists()
            // DB::table('dealer_contact_persons_details')->where('mobile_no', $request->mobile_no)->where('dealer_id', '!=', $dealer->id)->exists() ||
            // DB::table('distributor_contact_persons_details')->where('mobile_no', $request->mobile_no)->exists()
        ) {
            $conflictFields['mobile_no'] = 'This mobile number is already in use.';
        }

        if ($request->email && (
            DB::table('dealers')->where('email', $request->email)->where('id', '!=', $dealer->id)->whereIn('status', ['Active','Pending','Inactive'])->exists() ||
            DB::table('distributors')->where('email', $request->email)->exists()
            // DB::table('dealer_contact_persons_details')->where('email', $request->email)->where('dealer_id', '!=', $dealer->id)->exists() ||
            // DB::table('distributor_contact_persons_details')->where('email', $request->email)->exists()
        )) {
            $conflictFields['email'] = 'This email is already in use.';
        }

        if ($request->gst_num && (
            DB::table('dealers')->where('gst_num', $request->gst_num)->where('id', '!=', $dealer->id)->whereIn('status', ['Active','Pending','Inactive'])->exists() ||
            DB::table('distributors')->where('gst_num', $request->gst_num)->exists()
        )) {
            $conflictFields['gst_num'] = 'This GST number is already in use.';
        }

        if ($request->pan_num && (
            DB::table('dealers')->where('pan_num', $request->pan_num)->where('id', '!=', $dealer->id)->whereIn('status', ['Active','Pending','Inactive'])->exists() ||
            DB::table('distributors')->where('pan_num', $request->pan_num)->exists()
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
            $dealer->update([
                'name' => $request->name,
                'code' => $request->code,
                'mobile_no' => $request->mobile_no,
                'email' => $request->email,
                'gst_num' => $request->gst_num,
                'pan_num' => $request->pan_num,
                'order_limit' => $request->order_limit,
                'allowed_order_limit' => $request->order_limit,
                'remarks' => $request->remarks,
                'address' => $request->address,
                'pincode' => $request->pincode,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'type' => $request->type,
                'bank_name' => $request->bank_name,
                'account_holder_name' => $request->account_holder_name,
                'ifsc_code' => $request->ifsc_code,
                'account_number' => $request->account_number,
            ]);

            $dealer->contactPersons()->delete();

            if ($request->has('contact_person')) {
                foreach ($request->contact_person as $contact) {
                    $dealer->contactPersons()->create([
                        'name' => $contact['name'],
                        'mobile_no' => $contact['mobile_no'],
                        'email' => $contact['email'],
                    ]);
                }
            }

            // Update app_user_management if exists
            $user = AppUserManagement::where('code', $dealer->code)->first();
            if ($user) {
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'mobile_no' => $request->mobile_no,
                    'state_id' => $request->state_id,
                    'city_id' => $request->city_id,
                ]);
            }

            DB::commit();
            return redirect()->route('dealers.index')->with('success', 'Dealer updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('dealers.index')->with('error', 'An error occurred while updating the dealer.')->withInput();
        }
    }

    public function approvalRequests()
    {
        $dealers = Dealer::with('state')->whereIn('status', ['Pending', 'Rejected'])->orderBy('created_at','DESC')->get();
        return view('dealers_distributors.dealers.approvalRequests', compact('dealers'));
    }

    public function approve($id)
    {
        DB::beginTransaction();

        try {
            $dealer = Dealer::findOrFail($id);
            $dealer->status = 'Active';
            $dealer->approval_time = Carbon::now();
            $dealer->save();

            $creatorName = $dealer->created_by;

            // Find the AppUserManagement (Distributor) who created it
            $creatorUser = \App\Models\AppUserManagement::where('name', $creatorName)
                ->where('type', 'distributor')
                ->first();

            if ($creatorUser) {
                // If they have a linked web user, notify them too
                $webUser = \App\Models\User::where('name', 'LIKE', "%{$creatorName}%")
                    ->orWhere('email', $creatorUser->email)
                    ->first();

                $notifiables = collect();

                if ($webUser) {
                    $notifiables->push($webUser);
                }

                // Always notify AppUser (for FCM later)
                $notifiables->push($creatorUser);

                Notification::send($notifiables->filter(), new DealerApprovedNotification($dealer));
            }

            // Create user in app_user_management with state_id and city_id
            AppUserManagement::create([
                'name' => $dealer->name,
                'type' => 'dealer',
                'code' => $dealer->code,
                'email' => $dealer->email,
                'mobile_no' => $dealer->mobile_no,
                'password' => null,
                'status' => 'Active',
                'state_id' => $dealer->state_id,
                'city_id' => $dealer->city_id,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Dealer approved successfully and user created.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while approving the dealer: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        $dealer = Dealer::findOrFail($id);
        $dealer->status = 'Rejected';
        $dealer->save();

        $creatorName = $dealer->created_by;

        // Find the AppUserManagement (Distributor) who created it
        $creatorUser = \App\Models\AppUserManagement::where('name', $creatorName)
            ->where('type', 'distributor')
            ->first();

        if ($creatorUser) {
            // If they have a linked web user, notify them too
            $webUser = \App\Models\User::where('name', 'LIKE', "%{$creatorName}%")
                ->orWhere('email', $creatorUser->email)
                ->first();

            $notifiables = collect();

            if ($webUser) {
                $notifiables->push($webUser);
            }

            // Always notify AppUser (for FCM later)
            $notifiables->push($creatorUser);

            Notification::send($notifiables->filter(), new DealerRejectedNotification($dealer));
        }

        return redirect()->back()->with('success', 'Dealer Rejected');
    }

    public function storeOrderLimitRequest(Request $request)
    {
        $request->validate([
            'dealer_id' => 'required|exists:dealers,id',
            'desired_order_limit' => 'required|numeric|min:1',
            'submission_token' => 'required|string',
        ]);

        $token = $request['submission_token'];
        $sessionKey = 'submission_token_' . $token;

        // Prevent double submission using the same form token
        if (session()->has($sessionKey)) {
            return redirect()->back()->with('error', 'This form has already been submitted.');
        }

        // Check if there's already a pending request for this dealer
        $existingPending = DealerOrderLimitRequest::where('dealer_id', $request->dealer_id)
            ->where('status', 'Pending')
            ->exists();

        if ($existingPending) {
            return redirect()->back()->with('error', 'You already have a pending order limit change request. Please resolve the pending request made earlier before submitting a new one.');
        }

        // Mark this form submission as processed
        session()->put($sessionKey, true);

        $dealer = Dealer::findOrFail($request->dealer_id);

        $limitRequest = DealerOrderLimitRequest::create([
            'dealer_id' => $request->dealer_id,
            'order_limit' => $dealer->order_limit,
            'desired_order_limit' => $request->desired_order_limit,
            'status' => 'Pending',
            'remarks' => $request->remarks ?? null,
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
            'desired_order_limit' => $request['desired_order_limit'],
            'type' => 'Dealer',
        ];

        Notification::send($superAdmins, new \App\Notifications\OrderLimitRequested($data));

        return redirect()->route('dealers.index')->with('success', 'Order limit change request submitted successfully.');
    }

    public function olRequests()
    {
        $requests = DealerOrderLimitRequest::with('dealer')->orderBy('created_at', 'desc')->get();
        return view('dealers_distributors.dealers.olRequest', compact('requests'));
    }

    public function olRequestApprove($id, Request $request)
    {
        $olReq = DealerOrderLimitRequest::findOrFail($id);
        $dealer = Dealer::findOrFail($olReq->dealer_id);

        $requestedLimit = $olReq->desired_order_limit;
        $currentAllowedLimit = $dealer->allowed_order_limit;
        $currentOrderLimit = $dealer->order_limit;

        $pendingOrder = $currentOrderLimit - $currentAllowedLimit;
        if ($requestedLimit < $pendingOrder) {
            return redirect()->back()->with('error', "Requested order limit ({$requestedLimit}) is less than pending order ({$pendingOrder}).");
        }

        $differenceLimit = $requestedLimit - $currentOrderLimit;

        $dealer->order_limit = $requestedLimit;
        $dealer->allowed_order_limit += $differenceLimit;
        $dealer->save();

        $olReq->update([
            'status_change_remarks' => $request->status_change_remarks,
            'status' => 'Approved',
        ]);

        // Send notification to the app user and optional web user
        try {
            $appUser = AppUserManagement::where('code', $dealer->code)
                ->where('type', 'dealer')
                ->first();

            $webUser = \App\Models\User::where('name', 'LIKE', "%{$dealer->name}%")
                ->orWhere('email', $dealer->email)
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
                \Log::warning("No notifiable user found for order limit request #{$olReq->id}");
            }
        } catch (\Exception $e) {
            \Log::error("Failed to send notification for order limit request #{$olReq->id}: " . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Dealer Order Limit Request approved successfully.');
    }

    public function olRequestReject($id, Request $request){
        $olReq = DealerOrderLimitRequest::findOrFail($id);
        $dealer = Dealer::findOrFail($olReq->dealer_id);

        $olReq->update([
            'status_change_remarks' => $request->status_change_remarks,
            'status' => 'Rejected',
        ]);

        // Send notification to the app user and optional web user
        try {
            $appUser = AppUserManagement::where('code', $dealer->code)
                ->where('type', 'dealer')
                ->first();

            $webUser = \App\Models\User::where('name', 'LIKE', "%{$dealer->name}%")
                ->orWhere('email', $dealer->email)
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
                \Log::warning("No notifiable user found for order limit request #{$olReq->id}");
            }
        } catch (\Exception $e) {
            \Log::error("Failed to send notification for order limit request #{$olReq->id}: " . $e->getMessage());
        }

        return redirect()->back()->with('error', 'Dealer Order Limit Request Rejected');
    }

    public function checkInactivation($id)
    {
        $dealer = Dealer::with('distributor')->findOrFail($id);

        $hasOrders = Order::where('placed_by_dealer_id', $id)
            ->whereIn('status', ['Pending', 'Approved', 'Partial Dispatch'])
            ->exists();

        if ($hasOrders) {
            return response()->json([
                'blocked' => true,
                'message' => 'Dealer has pending/approved/partial-dispatch orders. Cannot inactivate.'
            ]);
        }

        if ($dealer->distributor_id) {
            return response()->json([
                'confirmationRequired' => true,
                'message' => "Dealer is in team of <strong>{$dealer->distributor->name}</strong>. Inactivation will remove dealer from the team. Proceed?"
            ]);
        }

        return response()->json(['blocked' => false]);
    }

    public function inactivate(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        DB::beginTransaction();
        try {
            $dealer = Dealer::findOrFail($request->id);

            if ($dealer->status === 'Inactive') {
                return redirect()->back()->with('info', 'Dealer is already inactive.');
            }

            $dealer->status = 'Inactive';
            $dealer->distributor_id = null; // ← Critical
            $dealer->save();

            $user = AppUserManagement::where('code', $dealer->code)->first();
            if ($user) {
                $user->status = 'Inactive';
                $user->save();
            }

            // Sync pivot
            DistributorTeamDealer::where('dealer_id', $dealer->id)
                ->update(['status' => 'Inactive']);

            DB::commit();
            return redirect()->back()->with('success', 'Dealer inactivated and removed from team.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function activate(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        DB::beginTransaction();
        try {
            $dealer = Dealer::where('status', 'Inactive')->findOrFail($request->id);

            $dealer->status = 'Active';
            $dealer->save();

            $user = AppUserManagement::where('code', $dealer->code)->first();
            if ($user) {
                $user->status = 'Active';
                $user->save();
            }

            // DO NOT REACTIVATE PIVOT
            // Dealer is free

            DB::commit();
            return redirect()->back()->with('success', 'Dealer activated. Ready to assign to a team.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
