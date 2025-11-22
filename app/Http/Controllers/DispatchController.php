<?php

namespace App\Http\Controllers;

use App\Models\ItemSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\Distributor;
use App\Models\Dealer;
use App\Models\Warehouse;
use App\Models\State;
use App\Models\Dispatch;
use App\Models\LoadingPointMaster;
use App\Models\DispatchAttachment;
use App\Models\DispatchItem;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\LoadingCharge;
use App\Models\InsuranceCharge;
use App\Models\GstRate;
use App\Models\OrderAllocation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Notifications\DispatchCreated;
use App\Notifications\DispatchApproved;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Models\AppUserManagement;

// Changes by md raza start
use App\Helpers\NumberHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use App\Models\CompanySetting;
// Changes by md raza end

class DispatchController extends Controller
{
    public function __construct()
    {
        // Basic permissions for viewing and creating
        $this->middleware('permission:Dispatch-Index', ['only' => ['index']]);
        $this->middleware('permission:Dispatch-Create', ['only' => ['create', 'store']]);
        $this->middleware('permission:Dispatch-View', ['only' => ['show', 'getDispatchItems']]);

        // Permission for approving a dispatch
        $this->middleware('permission:Dispatch-Approve', ['only' => ['approve']]);

        // Permission for exporting the dispatch as a PDF
        $this->middleware('permission:Dispatch-ExportPdf', ['only' => ['downloadDispatchPDF']]);
    }
    public function index()
    {
        $warehouses = Warehouse::get();
        $dispatches = Dispatch::with('dealer', 'distributor', 'dispatchItems')->orderBy('created_at', 'DESC')->get();
        return view('dispatch.index', compact('warehouses', 'dispatches'));
    }

    public function create(Request $request)
    {
        // Validate request data
        $request->validate([
            'order_type' => 'required|in:dealer,distributor',
            'party_id' => 'required|integer',
            'order_ids' => 'array',
            'warehouse_id' => 'required|integer',
        ]);

        $orderType = $request->order_type;
        $partyId = $request->party_id;
        $orderIds = $request->order_ids ?? [];
        $warehouse_id = $request->warehouse_id;
        $paymentSlipName = null;
        $type = $request->order_type;

        // Fetch master data
        $states = State::all();
        $loadingPoints = LoadingPointMaster::where('warehouse_id', $warehouse_id)->get();
        $sizes = ItemSize::where('status', 'Active')->get();
        $orders = Order::with('allocations')->whereIn('id', $orderIds)->get(); // Fetch all orders with allocations
        $singleItemName = Item::first()->item_name ?? 'TMT Bar';
        $loadingCharge = LoadingCharge::first()->amount ?? 265; // Consider warehouse-specific if needed
        $insuranceCharge = InsuranceCharge::first()->amount ?? 40;
        $gstRate = GstRate::first()->rate ?? 18;
        $warehouses = Warehouse::get();

        // Fetch party based on order type
        if ($orderType === 'dealer') {
            $party = Dealer::where('id', $partyId)->with('state', 'city')->firstOrFail();
        } else {
            $party = Distributor::where('id', $partyId)->with('state', 'city')->firstOrFail();
        }

        return view('dispatch.create', compact('warehouses', 'singleItemName', 'sizes', 'orders', 'loadingPoints', 'states', 'orderType', 'partyId', 'orderIds', 'warehouse_id', 'party', 'loadingCharge', 'insuranceCharge', 'gstRate', 'paymentSlipName', 'type'));
    }

    public function store(Request $request)
    {
        $paymentSlipPath = null;
        $attachmentPaths = [];
        // dd($request->hasFile('attachments'));
        // dd($request->allFiles());
        // Define validation rules with comprehensive custom error messages
        $validator = Validator::make($request->all(), [
            'dispatch_number' => 'required',
            'type' => ['required', 'in:distributor,dealer'],
            'distributor_id' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->type === 'distributor';
                }),
                'integer',
                'exists:distributors,id',
            ],
            'dealer_id' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->type === 'dealer';
                }),
                'integer',
                'exists:dealers,id',
            ],
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_address' => ['required', 'string', 'max:1000'],
            'recipient_state' => ['required', 'integer', 'exists:states,id'],
            'recipient_city' => ['required', 'integer', 'exists:cities,id'],
            'recipient_pincode' => ['required', 'string', 'regex:/^[0-9]{5,6}$/', 'max:10'],
            'consignee_name' => ['required', 'string', 'max:255'],
            'consignee_address' => ['required', 'string', 'max:1000'],
            'consignee_state' => ['required', 'integer', 'exists:states,id'],
            'consignee_city' => ['required', 'integer', 'exists:cities,id'],
            'consignee_pincode' => ['required', 'string', 'regex:/^[0-9]{5,6}$/', 'max:10'],
            'consignee_mobile_no' => ['nullable', 'string', 'regex:/^[0-9]{10,15}$/', 'max:15'],
            'dispatch_date' => ['required', 'date'],
            'loading_point_id' => ['nullable', 'integer', 'exists:loading_point_masters,id'],
            'bill_to' => ['nullable', 'string', 'max:255'],
            'bill_number' => ['nullable', 'string', 'alpha_num:ascii', 'max:50', 'unique:dispatches,bill_number'],
            'dispatch_out_time' => ['nullable', 'date_format:H:i'],
            'payment_slip' => ['nullable', 'file', 'max:2048'], // Removed mimes restriction, kept size limit
            'dispatch_remarks' => ['nullable', 'string', 'max:2000'],
            'transporter_name' => ['nullable', 'string', 'max:255'],
            'vehicle_no' => ['nullable', 'string', 'regex:/^[A-Z0-9 -]{5,20}$/', 'max:20'],
            'driver_name' => ['nullable', 'string', 'max:255'],
            'driver_mobile_no' => ['nullable', 'string', 'regex:/^[0-9]{10,15}$/', 'max:15'],
            'e_way_bill_no' => ['nullable', 'string', 'alpha_num:ascii', 'max:50'],
            'bilty_no' => ['nullable', 'string', 'alpha_num:ascii', 'max:50'],
            'transport_remarks' => ['nullable', 'string', 'max:2000'],
            'terms_conditions' => ['nullable', 'string', 'max:5000'],
            'additional_charges' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.order_id' => ['required', 'integer', 'exists:orders,id'],
            'items.*.allocation_id' => ['required', 'integer', 'exists:order_allocations,id'],
            'items.*.order_qty' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'items.*.already_disp' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'items.*.remaining_qty' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.size' => ['required', 'integer', 'exists:item_sizes,id'],
            'items.*.dispatch_qty' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'items.*.basic_price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'items.*.gauge_diff' => ['required', 'numeric', 'min:-999999.99', 'max:999999.99'],
            'items.*.final_price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'items.*.loading_charge' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'items.*.insurance' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'items.*.gst' => ['required', 'numeric', 'min:0', 'max:100'],
            'items.*.token_amount' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'items.*.total_amount' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
            'items.*.payment_term' => ['required', 'string', 'in:Advance,Next Day,15 Days Later,30 Days Later'],
            'items.*.remark' => ['nullable', 'string', 'max:2000'],
            'attachments' => ['nullable', 'array'], // Made attachments optional
            'attachments.*.document' => ['nullable', 'file', 'max:2048'], // Allow more file types
            'attachments.*.remark' => ['nullable', 'string', 'max:2000'],
        ], [
            // Custom error messages for all fields and scenarios
            'type.required' => 'The dispatch type is required.',
            'type.in' => 'The dispatch type must be either distributor or dealer.',
            'distributor_id.required_if' => 'The distributor ID is required when type is distributor.',
            'distributor_id.integer' => 'The distributor ID must be an integer.',
            'distributor_id.exists' => 'The selected distributor does not exist.',
            'dealer_id.required_if' => 'The dealer ID is required when type is dealer.',
            'dealer_id.integer' => 'The dealer ID must be an integer.',
            'dealer_id.exists' => 'The selected dealer does not exist.',
            'recipient_name.required' => 'The recipient name is required.',
            'recipient_name.string' => 'The recipient name must be a string.',
            'recipient_name.max' => 'The recipient name may not be greater than 255 characters.',
            'recipient_address.required' => 'The billing address is required.',
            'recipient_address.string' => 'The billing address must be a string.',
            'recipient_address.max' => 'The billing address may not be greater than 1000 characters.',
            'recipient_state.required' => 'The billing state is required.',
            'recipient_state.integer' => 'The billing state must be an integer.',
            'recipient_state.exists' => 'The selected billing state does not exist.',
            'recipient_city.required' => 'The billing city is required.',
            'recipient_city.integer' => 'The billing city must be an integer.',
            'recipient_city.exists' => 'The selected billing city does not exist.',
            'recipient_pincode.required' => 'The billing pincode is required.',
            'recipient_pincode.string' => 'The billing pincode must be a string.',
            'recipient_pincode.regex' => 'The billing pincode format is invalid (5-6 digits).',
            'recipient_pincode.max' => 'The billing pincode may not be greater than 10 characters.',
            'consignee_name.required' => 'The consignee name is required.',
            'consignee_name.string' => 'The consignee name must be a string.',
            'consignee_name.max' => 'The consignee name may not be greater than 255 characters.',
            'consignee_address.required' => 'The delivery address is required.',
            'consignee_address.string' => 'The delivery address must be a string.',
            'consignee_address.max' => 'The delivery address may not be greater than 1000 characters.',
            'consignee_state.required' => 'The delivery state is required.',
            'consignee_state.integer' => 'The delivery state must be an integer.',
            'consignee_state.exists' => 'The selected delivery state does not exist.',
            'consignee_city.required' => 'The delivery city is required.',
            'consignee_city.integer' => 'The delivery city must be an integer.',
            'consignee_city.exists' => 'The selected delivery city does not exist.',
            'consignee_pincode.required' => 'The delivery pincode is required.',
            'consignee_pincode.string' => 'The delivery pincode must be a string.',
            'consignee_pincode.regex' => 'The delivery pincode format is invalid (5-6 digits).',
            'consignee_pincode.max' => 'The delivery pincode may not be greater than 10 characters.',
            'consignee_mobile_no.string' => 'The consignee mobile number must be a string.',
            'consignee_mobile_no.regex' => 'The consignee mobile number format is invalid (10-15 digits).',
            'consignee_mobile_no.max' => 'The consignee mobile number may not be greater than 15 characters.',
            'dispatch_date.required' => 'The dispatch date is required.',
            'dispatch_date.date' => 'The dispatch date must be a valid date.',
            // 'dispatch_date.after_or_equal' => 'The dispatch date cannot be in the past.',
            'loading_point_id.integer' => 'The loading point ID must be an integer.',
            'loading_point_id.exists' => 'The selected loading point does not exist.',
            'bill_to.string' => 'The bill to must be a string.',
            'bill_to.max' => 'The bill to may not be greater than 255 characters.',
            'bill_number.string' => 'The bill number must be a string.',
            'bill_number.alpha_num' => 'The bill number must contain only alphanumeric characters.',
            'bill_number.max' => 'The bill number may not be greater than 50 characters.',
            'bill_number.unique' => 'The bill number has already been taken.',
            'dispatch_out_time.date_format' => 'The dispatch out time must be in the format HH:MM.',
            'payment_slip.file' => 'The payment slip must be a file.',
            'payment_slip.max' => 'The payment slip may not be greater than 2MB.',
            'dispatch_remarks.string' => 'The dispatch remarks must be a string.',
            'dispatch_remarks.max' => 'The dispatch remarks may not be greater than 2000 characters.',
            'transporter_name.string' => 'The transporter name must be a string.',
            'transporter_name.max' => 'The transporter name may not be greater than 255 characters.',
            'vehicle_no.string' => 'The vehicle number must be a string.',
            'vehicle_no.regex' => 'The vehicle number format is invalid (alphanumeric with spaces or hyphens, 5-20 characters).',
            'vehicle_no.max' => 'The vehicle number may not be greater than 20 characters.',
            'driver_name.string' => 'The driver name must be a string.',
            'driver_name.max' => 'The driver name may not be greater than 255 characters.',
            'driver_mobile_no.string' => 'The driver mobile number must be a string.',
            'driver_mobile_no.regex' => 'The driver mobile number format is invalid (10-15 digits).',
            'driver_mobile_no.max' => 'The driver mobile number may not be greater than 15 characters.',
            'e_way_bill_no.string' => 'The E-Way bill number must be a string.',
            'e_way_bill_no.alpha_num' => 'The E-Way bill number must contain only alphanumeric characters.',
            'e_way_bill_no.max' => 'The E-Way bill number may not be greater than 50 characters.',
            'bilty_no.string' => 'The bilty number must be a string.',
            'bilty_no.alpha_num' => 'The bilty number must contain only alphanumeric characters.',
            'bilty_no.max' => 'The bilty number may not be greater than 50 characters.',
            'transport_remarks.string' => 'The transport remarks must be a string.',
            'transport_remarks.max' => 'The transport remarks may not be greater than 2000 characters.',
            'terms_conditions.string' => 'The terms and conditions must be a string.',
            'terms_conditions.max' => 'The terms and conditions may not be greater than 5000 characters.',
            'additional_charges.numeric' => 'The additional charges must be a number.',
            'additional_charges.min' => 'The additional charges must be at least 0.',
            'additional_charges.max' => 'The additional charges may not be greater than 99,999,999.99.',
            'items.required' => 'At least one item is required for the dispatch.',
            'items.array' => 'The items must be an array.',
            'items.min' => 'At least one item is required for the dispatch.',
            'items.*.order_id.required' => 'The order ID is required for item #:index.',
            'items.*.order_id.integer' => 'The order ID must be an integer for item #:index.',
            'items.*.order_id.exists' => 'The selected order does not exist for item #:index.',
            'items.*.allocation_id.required' => 'The allocation ID is required for item #:index.',
            'items.*.allocation_id.integer' => 'The allocation ID must be an integer for item #:index.',
            'items.*.allocation_id.exists' => 'The selected allocation does not exist for item #:index.',
            'items.*.order_qty.required' => 'The order quantity is required for item #:index.',
            'items.*.order_qty.numeric' => 'The order quantity must be a number for item #:index.',
            'items.*.order_qty.min' => 'The order quantity must be at least 0 for item #:index.',
            'items.*.order_qty.max' => 'The order quantity may not be greater than 999,999.99 for item #:index.',
            'items.*.already_disp.required' => 'The already dispatched quantity is required for item #:index.',
            'items.*.already_disp.numeric' => 'The already dispatched quantity must be a number for item #:index.',
            'items.*.already_disp.min' => 'The already dispatched quantity must be at least 0 for item #:index.',
            'items.*.already_disp.max' => 'The already dispatched quantity may not be greater than 999,999.99 for item #:index.',
            'items.*.remaining_qty.required' => 'The remaining quantity is required for item #:index.',
            'items.*.remaining_qty.numeric' => 'The remaining quantity must be a number for item #:index.',
            'items.*.remaining_qty.min' => 'The remaining quantity must be at least 0 for item #:index.',
            'items.*.remaining_qty.max' => 'The remaining quantity may not be greater than 999,999.99 for item #:index.',
            'items.*.item_name.required' => 'The item name is required for item #:index.',
            'items.*.item_name.string' => 'The item name must be a string for item #:index.',
            'items.*.item_name.max' => 'The item name may not be greater than 255 characters for item #:index.',
            'items.*.size.required' => 'The size is required for item #:index.',
            'items.*.size.integer' => 'The size must be an integer for item #:index.',
            'items.*.size.exists' => 'The selected size does not exist for item #:index.',
            'items.*.dispatch_qty.required' => 'The dispatch quantity is required for item #:index.',
            'items.*.dispatch_qty.numeric' => 'The dispatch quantity must be a number for item #:index.',
            'items.*.dispatch_qty.min' => 'The dispatch quantity must be at least 0.01 for item #:index.',
            'items.*.dispatch_qty.max' => 'The dispatch quantity may not be greater than 999,999.99 for item #:index.',
            'items.*.basic_price.required' => 'The basic price is required for item #:index.',
            'items.*.basic_price.numeric' => 'The basic price must be a number for item #:index.',
            'items.*.basic_price.min' => 'The basic price must be at least 0 for item #:index.',
            'items.*.basic_price.max' => 'The basic price may not be greater than 999,999.99 for item #:index.',
            'items.*.gauge_diff.required' => 'The gauge difference is required for item #:index.',
            'items.*.gauge_diff.numeric' => 'The gauge difference must be a number for item #:index.',
            'items.*.gauge_diff.min' => 'The gauge difference must be at least -999,999.99 for item #:index.',
            'items.*.gauge_diff.max' => 'The gauge difference may not be greater than 999,999.99 for item #:index.',
            'items.*.final_price.required' => 'The final price is required for item #:index.',
            'items.*.final_price.numeric' => 'The final price must be a number for item #:index.',
            'items.*.final_price.min' => 'The final price must be at least 0 for item #:index.',
            'items.*.final_price.max' => 'The final price may not be greater than 999,999.99 for item #:index.',
            'items.*.loading_charge.required' => 'The loading charge is required for item #:index.',
            'items.*.loading_charge.numeric' => 'The loading charge must be a number for item #:index.',
            'items.*.loading_charge.min' => 'The loading charge must be at least 0 for item #:index.',
            'items.*.loading_charge.max' => 'The loading charge may not be greater than 999,999.99 for item #:index.',
            'items.*.insurance.required' => 'The insurance charge is required for item #:index.',
            'items.*.insurance.numeric' => 'The insurance charge must be a number for item #:index.',
            'items.*.insurance.min' => 'The insurance charge must be at least 0 for item #:index.',
            'items.*.insurance.max' => 'The insurance charge may not be greater than 999,999.99 for item #:index.',
            'items.*.gst.required' => 'The GST percentage is required for item #:index.',
            'items.*.gst.numeric' => 'The GST percentage must be a number for item #:index.',
            'items.*.gst.min' => 'The GST percentage must be at least 0 for item #:index.',
            'items.*.gst.max' => 'The GST percentage may not be greater than 100 for item #:index.',
            'items.*.token_amount.numeric' => 'The token amount must be a number for item #:index.',
            'items.*.token_amount.min' => 'The token amount must be at least 0 for item #:index.',
            'items.*.token_amount.max' => 'The token amount may not be greater than 999,999.99 for item #:index.',
            'items.*.total_amount.required' => 'The total amount is required for item #:index.',
            'items.*.total_amount.numeric' => 'The total amount must be a number for item #:index.',
            'items.*.total_amount.min' => 'The total amount must be at least 0 for item #:index.',
            'items.*.total_amount.max' => 'The total amount may not be greater than 999,999,999.99 for item #:index.',
            'items.*.payment_term.string' => 'The payment term must be a string for item #:index.',
            'items.*.payment_term.in' => 'The payment term must be one of: Advance, Next Day, 15 Days Later, 30 Days Later for item #:index.',
            'items.*.remark.string' => 'The remark must be a string for item #:index.',
            'items.*.remark.max' => 'The remark may not be greater than 2000 characters for item #:index.',
            'attachments.array' => 'The attachments must be an array.',
            'attachments.*.document.file' => 'The document must be a file for attachment #:index.',
            'attachments.*.document.mimes' => 'The document must be a file of type: jpg, jpeg, png, pdf, doc, docx, xls, xlsx for attachment #:index.',
            'attachments.*.document.max' => 'The document may not be greater than 2MB for attachment #:index.',
            'attachments.*.remark.string' => 'The remark must be a string for attachment #:index.',
            'attachments.*.remark.max' => 'The remark may not be greater than 2000 characters for attachment #:index.',
        ]);

        // Replace :index in messages with actual index +1 for user-friendliness
        $messages = $validator->messages();
        foreach ($messages->keys() as $key) {
            if (str_contains($key, 'items.') || str_contains($key, 'attachments.')) {
                $index = explode('.', $key)[1];
                $humanIndex = (int)$index + 1;
                foreach ($messages->get($key) as $message) {
                    $messages->add($key, str_replace('#:index', $humanIndex, $message));
                }
            }
        }

        // After validation hook for additional business logic checks
        $validator->after(function ($validator) use ($request) {
            if ($validator->errors()->any()) {
                return;
            }

            // Check for duplicate allocation and size pairs in items
            $pairs = [];
            foreach ($request->input('items', []) as $index => $item) {
                $pair = $item['allocation_id'] . '-' . $item['size'];
                if (in_array($pair, $pairs)) {
                    $validator->errors()->add("items.$index.size", 'Duplicate allocation and size combination for item ' . ($index + 1) . '.');
                } else {
                    $pairs[] = $pair;
                }
            }

            // Check dispatch_qty against allocation remaining_qty with threshold at order level
            $order_proposed = [];
            foreach ($request->input('items', []) as $index => $item) {
                $order_id = $item['order_id'];
                if (!isset($order_proposed[$order_id])) {
                    $order_proposed[$order_id] = 0;
                }
                $order_proposed[$order_id] += (float) $item['dispatch_qty'];
            }

            foreach ($order_proposed as $order_id => $proposed) {
                $total_remaining = OrderAllocation::where('order_id', $order_id)->sum('remaining_qty');
                if ($proposed > $total_remaining + 5) {
                    $validator->errors()->add('items', 'Total dispatch quantity exceeds remaining quantity + 5 MT threshold for order #' . $order_id . '. Remaining: ' . $total_remaining . ' MT');
                }
            }

            // Per-item checks (tampering only, no strict quantity check)
            foreach ($request->input('items', []) as $index => $item) {
                $allocationId = $item['allocation_id'];

                $allocation = OrderAllocation::find($allocationId);
                if (!$allocation) {
                    $validator->errors()->add("items.$index.allocation_id", 'Invalid allocation selected for item ' . ($index + 1) . '.');
                    continue;
                }

                // Verify frontend quantities match DB (security check)
                // if ((float) $item['remaining_qty'] != $allocation->remaining_qty) {
                //     $validator->errors()->add("items.$index.remaining_qty", 'Remaining quantity mismatch for item ' . ($index + 1) . '. Please refresh and try again.');
                // }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Get validated data
        $validated = $validator->validated();

        // Start transaction for data integrity
        try {
            DB::beginTransaction();

            // Generate unique dispatch_number
            $date = $validated['dispatch_date'];

            // Handle payment_slip upload
            $paymentSlipPath = null;
            if ($request->hasFile('payment_slip')) {
                $paymentSlipPath = $request->file('payment_slip')->store('dispatches/payment_slips', 'public');
            }

            $creatorName = Auth::user()
                ? trim(Auth::user()->name . ' ' . (Auth::user()->last_name ?? ''))
                : null;

            // Create the dispatch record
            $dispatch = Dispatch::create([
                'dispatch_number' => $validated['dispatch_number'],
                'type' => $validated['type'],
                'distributor_id' => $validated['type'] === 'distributor' ? $validated['distributor_id'] : null,
                'dealer_id' => $validated['type'] === 'dealer' ? $validated['dealer_id'] : null,
                'recipient_name' => $validated['recipient_name'],
                'recipient_address' => $validated['recipient_address'],
                'recipient_state_id' => $validated['recipient_state'],
                'recipient_city_id' => $validated['recipient_city'],
                'recipient_pincode' => $validated['recipient_pincode'],
                'consignee_name' => $validated['consignee_name'],
                'consignee_address' => $validated['consignee_address'],
                'consignee_state_id' => $validated['consignee_state'],
                'consignee_city_id' => $validated['consignee_city'],
                'consignee_pincode' => $validated['consignee_pincode'],
                'consignee_mobile_no' => $validated['consignee_mobile_no'] ?? null,
                'dispatch_date' => $validated['dispatch_date'],
                'warehouse_id' => $request['warehouse_id'] ?? null,
                'bill_to' => $validated['bill_to'] ?? null,
                'bill_number' => $validated['bill_number'],
                'dispatch_out_time' => $validated['dispatch_out_time'] ?? null,
                'payment_slip' => $paymentSlipPath,
                'dispatch_remarks' => $validated['dispatch_remarks'] ?? null,
                'transporter_name' => $validated['transporter_name'],
                'vehicle_no' => $validated['vehicle_no'] ?? null,
                'driver_name' => $validated['driver_name'] ?? null,
                'driver_mobile_no' => $validated['driver_mobile_no'] ?? null,
                'e_way_bill_no' => $validated['e_way_bill_no'] ?? null,
                'bilty_no' => $validated['bilty_no'] ?? null,
                'transport_remarks' => $validated['transport_remarks'] ?? null,
                'terms_conditions' => $validated['terms_conditions'] ?? null,
                'additional_charges' => (float) ($validated['additional_charges'] ?? 0.00),
                'total_amount' => 0.00, // To be updated
                'created_by' => $creatorName,
                'status' => 'pending'
            ]);

            // Process items, recalculate totals for security
            $grandTotal = (float) ($validated['additional_charges'] ?? 0.00);
            foreach ($validated['items'] as $item) {
                $finalPrice = (float) $item['basic_price'] + (float) $item['gauge_diff'];
                $baseTotal = $finalPrice + (float) $item['loading_charge'] + (float) $item['insurance'];
                $baseTotal *= (float) $item['dispatch_qty'];
                $gstAmount = $baseTotal * ((float) $item['gst'] / 100);
                $tokenAmount = (float) ($item['token_amount'] ?? 0.00);
                $itemTotal = $baseTotal + $gstAmount - $tokenAmount;

                // Verify calculated final_price and total_amount match submitted (prevent tampering)
                if (abs($finalPrice - (float) $item['final_price']) > 0.01) {
                    throw new Exception('Final price mismatch for an item. Possible tampering detected.');
                }
                if (abs($itemTotal - (float) $item['total_amount']) > 0.01) {
                    throw new Exception('Total amount mismatch for an item. Possible tampering detected.');
                }

                DispatchItem::create([
                    'dispatch_id' => $dispatch->id,
                    'item_id' => 1,
                    'order_id' => $item['order_id'],
                    'allocation_id' => $item['allocation_id'],
                    'order_qty' => (float) $item['order_qty'],
                    'already_disp' => (float) $item['already_disp'],
                    'remaining_qty' => (float) $item['remaining_qty'],
                    'item_name' => $item['item_name'],
                    'size_id' => $item['size'],
                    // 'length' => (float) $item['length'],
                    'dispatch_qty' => (float) $item['dispatch_qty'],
                    'basic_price' => (float) $item['basic_price'],
                    'gauge_diff' => (float) $item['gauge_diff'],
                    'final_price' => $finalPrice,
                    'loading_charge' => (float) $item['loading_charge'],
                    'insurance' => (float) $item['insurance'],
                    'gst' => (float) $item['gst'],
                    'token_amount' => $tokenAmount > 0 ? $tokenAmount : null,
                    'total_amount' => $itemTotal,
                    'payment_term' => $item['payment_term'],
                    'status' => 'pending',
                    'remark' => $item['remark'] ?? null,
                ]);

                $grandTotal += $itemTotal;

                // Update allocation remaining_qty
                // $allocation = OrderAllocation::findOrFail($item['allocation_id']);
                // $allocation->remaining_qty -= (float) $item['dispatch_qty'];
                // if ($allocation->remaining_qty < 0) {
                //     throw new Exception('Negative remaining quantity detected for allocation #' . $item['allocation_id'] . '.');
                // }
                // $allocation->save();
            }

            // Update dispatch total_amount
            $dispatch->total_amount = $grandTotal;
            $dispatch->save();

            // Handle attachments
            $attachmentPaths = [];
            $files = $request->file('attachments');
            if ($files && is_array($files)) {
                Log::info('Attachments found in request', ['count' => count($files)]);
                foreach ($files as $key => $attachment) {
                    if (isset($attachment['document']) && $attachment['document'] instanceof \Illuminate\Http\UploadedFile && $attachment['document']->isValid()) {
                        Log::info('Processing attachment', [
                            'key' => $key,
                            'filename' => $attachment['document']->getClientOriginalName(),
                            'size' => $attachment['document']->getSize(),
                        ]);
                        $extension = $attachment['document']->getClientOriginalExtension();
                        $filename = 'attachment_' . $validated['dispatch_number'] . '_' . $key . '_' . time() . '_' . Str::random(10) . '.' . $extension;
                        $path = $attachment['document']->storeAs('dispatches/attachments', $filename, 'public');
                        $attachmentPaths[] = $path;
                        DispatchAttachment::create([
                            'dispatch_id' => $dispatch->id,
                            'document' => $path,
                            'remark' => $request->input("attachments.$key.remark") ?? null,
                        ]);
                    } else {
                        Log::warning('Invalid or missing document for attachment', ['key' => $key]);
                    }
                }
            } else {
                Log::info('No attachments found in request', ['input' => $request->input('attachments')]);
            }

            DB::commit();

            $superAdminRole = \Spatie\Permission\Models\Role::where('name', 'Super Admin')->first();

            if ($superAdminRole) {
                $superAdmins = \App\Models\User::role($superAdminRole)->get();
                if ($superAdmins->isNotEmpty()) {
                    // Determine the placer (Dealer or Distributor)
                    $placer = null;
                    if ($request->type === 'dealer' && isset($request->dealer_id)) {
                        $placer = \App\Models\Dealer::find($request->dealer_id);
                    } elseif ($request->type === 'distributor' && isset($request->distributor_id)) {
                        $placer = \App\Models\Distributor::find($request->distributor_id);
                    }

                    Notification::send($superAdmins, new \App\Notifications\DispatchCreated($dispatch, $placer));
                }
            } else {
                \Log::warning('Super Admin role not found when sending new dispatch notification.');
            }

            return redirect()->route('dispatch.index')
                ->with('success', 'Dispatch created successfully.');
        } catch (Exception $e) {
            DB::rollBack();

            // Clean up uploaded files on failure
            if ($paymentSlipPath) {
                Storage::disk('public')->delete($paymentSlipPath);
            }
            foreach ($attachmentPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            return redirect()->back()
                ->with('error', 'Failed to create dispatch: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(int $id)
    {
        $dispatch = Dispatch::with([
            'distributor',
            'dealer',
            'items.order',
            'items.size',

            // <-- THIS IS THE KEY LINE
            'items.allocation.distributor',
            'items.allocation.dealer',

            'warehouse',
            'attachments',
            'recipientState',
            'recipientCity',
            'consigneeState',
            'consigneeCity',
        ])->findOrFail($id);

        return view('dispatch.show', compact('dispatch'));
    }

    public function edit($id)
    {
        \Log::debug('Loading dispatch with id: ' . $id);
        $dispatch = Dispatch::with(['items.order.allocations.allocatable', 'attachments'])->findOrFail($id);

        \Log::debug('Dispatch type: ' . $dispatch->type . ', distributor_id: ' . $dispatch->distributor_id);
        if ($dispatch->type === 'dealer') {
            $party = Dealer::where('id', $dispatch->dealer_id)->with('state', 'city')->firstOrFail();
        } else {
            \Log::debug('Checking Distributor class existence');
            if (!class_exists('App\\Models\\Distributor')) {
                \Log::error('Class App\\Models\\Distributor not found');
            } else {
                \Log::debug('Distributor class exists, proceeding');
            }
            $party = Distributor::where('id', $dispatch->distributor_id)->with('state', 'city')->firstOrFail();
        }

        $states = State::all();
        $sizes = ItemSize::where('status', 'Active')->get();
        $orders = Order::with(['allocations.allocatable'])->whereIn('id', $dispatch->items->pluck('order_id'))->get();
        $singleItemName = Item::first()->item_name ?? 'TMT Bar';
        $loadingCharge = LoadingCharge::first()->amount ?? 265;
        $insuranceCharge = InsuranceCharge::first()->amount ?? 40;
        $gstRate = GstRate::first()->rate ?? 18;
        $warehouses = Warehouse::get();
        $dispatch->dispatch_out_time = $dispatch->dispatch_out_time ? \Carbon\Carbon::parse($dispatch->dispatch_out_time)->format('H:i') : null;

        return view('dispatch.edit', compact(
            'warehouses',
            'dispatch',
            'singleItemName',
            'sizes',
            'orders',
            'states',
            'party',
            'loadingCharge',
            'insuranceCharge',
            'gstRate',
        ));
    }

    public function update(Request $request, $id)
    {
        // Find the existing dispatch record
        $dispatch = Dispatch::findOrFail($id);

        // Define validation rules with comprehensive custom error messages
        $validator = Validator::make($request->all(), [
            'dispatch_number' => ['required', 'string', 'max:50', Rule::unique('dispatches')->ignore($dispatch->id)],
            'type' => ['required', 'in:distributor,dealer'],
            'distributor_id' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->type === 'distributor';
                }),
                'integer',
                'exists:distributors,id',
            ],
            'dealer_id' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->type === 'dealer';
                }),
                'integer',
                'exists:dealers,id',
            ],
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_address' => ['required', 'string', 'max:1000'],
            'recipient_state' => ['required', 'integer', 'exists:states,id'],
            'recipient_city' => ['required', 'integer', 'exists:cities,id'],
            'recipient_pincode' => ['required', 'string', 'regex:/^[0-9]{5,6}$/', 'max:10'],
            'consignee_name' => ['required', 'string', 'max:255'],
            'consignee_address' => ['required', 'string', 'max:1000'],
            'consignee_state' => ['required', 'integer', 'exists:states,id'],
            'consignee_city' => ['required', 'integer', 'exists:cities,id'],
            'consignee_pincode' => ['required', 'string', 'regex:/^[0-9]{5,6}$/', 'max:10'],
            'consignee_mobile_no' => ['nullable', 'string', 'regex:/^[0-9]{10,15}$/', 'max:15'],
            'dispatch_date' => ['required', 'date'],
            'loading_point_id' => ['nullable', 'integer', 'exists:loading_point_masters,id'],
            'bill_to' => ['nullable', 'string', 'max:255'],
            'bill_number' => ['nullable', 'string', 'alpha_num:ascii', 'max:50', Rule::unique('dispatches')->ignore($dispatch->id)],
            'dispatch_out_time' => ['nullable', 'date_format:H:i'],
            'payment_slip' => ['nullable', 'file', 'max:2048'],
            'dispatch_remarks' => ['nullable', 'string', 'max:2000'],
            'transporter_name' => ['nullable', 'string', 'max:255'],
            'vehicle_no' => ['nullable', 'string', 'regex:/^[A-Z0-9 -]{5,20}$/', 'max:20'],
            'driver_name' => ['nullable', 'string', 'max:255'],
            'driver_mobile_no' => ['nullable', 'string', 'regex:/^[0-9]{10,15}$/', 'max:15'],
            'e_way_bill_no' => ['nullable', 'string', 'alpha_num:ascii', 'max:50'],
            'bilty_no' => ['nullable', 'string', 'alpha_num:ascii', 'max:50'],
            'transport_remarks' => ['nullable', 'string', 'max:2000'],
            'terms_conditions' => ['nullable', 'string', 'max:5000'],
            'additional_charges' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.order_id' => ['required', 'integer', 'exists:orders,id'],
            'items.*.allocation_id' => ['required', 'integer', 'exists:order_allocations,id'],
            'items.*.order_qty' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'items.*.already_disp' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'items.*.remaining_qty' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.size' => ['required', 'integer', 'exists:item_sizes,id'],
            // 'items.*.length' => ['required', 'numeric', 'min:0', 'max:999.99'],
            'items.*.dispatch_qty' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'items.*.basic_price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'items.*.gauge_diff' => ['required', 'numeric', 'min:-999999.99', 'max:999999.99'],
            'items.*.final_price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'items.*.loading_charge' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'items.*.insurance' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'items.*.gst' => ['required', 'numeric', 'min:0', 'max:100'],
            'items.*.token_amount' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'items.*.total_amount' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
            'items.*.payment_term' => ['nullable', 'string', 'in:Advance,Next Day,15 Days Later,30 Days Later'],
            'items.*.remark' => ['nullable', 'string', 'max:2000'],
            'attachments' => ['nullable', 'array'],
            'attachments.*.document' => ['nullable', 'file', 'max:2048'],
            'attachments.*.remark' => ['nullable', 'string', 'max:2000'],
        ], [
            // Custom error messages for all fields and scenarios
            'type.required' => 'The dispatch type is required.',
            'type.in' => 'The dispatch type must be either distributor or dealer.',
            'distributor_id.required_if' => 'The distributor ID is required when type is distributor.',
            'distributor_id.integer' => 'The distributor ID must be an integer.',
            'distributor_id.exists' => 'The selected distributor does not exist.',
            'dealer_id.required_if' => 'The dealer ID is required when type is dealer.',
            'dealer_id.integer' => 'The dealer ID must be an integer.',
            'dealer_id.exists' => 'The selected dealer does not exist.',
            'recipient_name.required' => 'The recipient name is required.',
            'recipient_name.string' => 'The recipient name must be a string.',
            'recipient_name.max' => 'The recipient name may not be greater than 255 characters.',
            'recipient_address.required' => 'The billing address is required.',
            'recipient_address.string' => 'The billing address must be a string.',
            'recipient_address.max' => 'The billing address may not be greater than 1000 characters.',
            'recipient_state.required' => 'The billing state is required.',
            'recipient_state.integer' => 'The billing state must be an integer.',
            'recipient_state.exists' => 'The selected billing state does not exist.',
            'recipient_city.required' => 'The billing city is required.',
            'recipient_city.integer' => 'The billing city must be an integer.',
            'recipient_city.exists' => 'The selected billing city does not exist.',
            'recipient_pincode.required' => 'The billing pincode is required.',
            'recipient_pincode.string' => 'The billing pincode must be a string.',
            'recipient_pincode.regex' => 'The billing pincode format is invalid (5-6 digits).',
            'recipient_pincode.max' => 'The billing pincode may not be greater than 10 characters.',
            'consignee_name.required' => 'The consignee name is required.',
            'consignee_name.string' => 'The consignee name must be a string.',
            'consignee_name.max' => 'The consignee name may not be greater than 255 characters.',
            'consignee_address.required' => 'The delivery address is required.',
            'consignee_address.string' => 'The delivery address must be a string.',
            'consignee_address.max' => 'The delivery address may not be greater than 1000 characters.',
            'consignee_state.required' => 'The delivery state is required.',
            'consignee_state.integer' => 'The delivery state must be an integer.',
            'consignee_state.exists' => 'The selected delivery state does not exist.',
            'consignee_city.required' => 'The delivery city is required.',
            'consignee_city.integer' => 'The delivery city must be an integer.',
            'consignee_city.exists' => 'The selected delivery city does not exist.',
            'consignee_pincode.required' => 'The delivery pincode is required.',
            'consignee_pincode.string' => 'The delivery pincode must be a string.',
            'consignee_pincode.regex' => 'The delivery pincode format is invalid (5-6 digits).',
            'consignee_pincode.max' => 'The delivery pincode may not be greater than 10 characters.',
            'consignee_mobile_no.string' => 'The consignee mobile number must be a string.',
            'consignee_mobile_no.regex' => 'The consignee mobile number format is invalid (10-15 digits).',
            'consignee_mobile_no.max' => 'The consignee mobile number may not be greater than 15 characters.',
            'dispatch_date.required' => 'The dispatch date is required.',
            'dispatch_date.date' => 'The dispatch date must be a valid date.',
            'loading_point_id.integer' => 'The loading point ID must be an integer.',
            'loading_point_id.exists' => 'The selected loading point does not exist.',
            'bill_to.string' => 'The bill to must be a string.',
            'bill_to.max' => 'The bill to may not be greater than 255 characters.',
            'bill_number.string' => 'The bill number must be a string.',
            'bill_number.alpha_num' => 'The bill number must contain only alphanumeric characters.',
            'bill_number.max' => 'The bill number may not be greater than 50 characters.',
            'bill_number.unique' => 'The bill number has already been taken.',
            'dispatch_out_time.date_format' => 'The dispatch out time must be in the format HH:MM.',
            'payment_slip.file' => 'The payment slip must be a file.',
            'payment_slip.max' => 'The payment slip may not be greater than 2MB.',
            'dispatch_remarks.string' => 'The dispatch remarks must be a string.',
            'dispatch_remarks.max' => 'The dispatch remarks may not be greater than 2000 characters.',
            'transporter_name.string' => 'The transporter name must be a string.',
            'transporter_name.max' => 'The transporter name may not be greater than 255 characters.',
            'vehicle_no.string' => 'The vehicle number must be a string.',
            'vehicle_no.regex' => 'The vehicle number format is invalid (alphanumeric with spaces or hyphens, 5-20 characters).',
            'vehicle_no.max' => 'The vehicle number may not be greater than 20 characters.',
            'driver_name.string' => 'The driver name must be a string.',
            'driver_name.max' => 'The driver name may not be greater than 255 characters.',
            'driver_mobile_no.string' => 'The driver mobile number must be a string.',
            'driver_mobile_no.regex' => 'The driver mobile number format is invalid (10-15 digits).',
            'driver_mobile_no.max' => 'The driver mobile number may not be greater than 15 characters.',
            'e_way_bill_no.string' => 'The E-Way bill number must be a string.',
            'e_way_bill_no.alpha_num' => 'The E-Way bill number must contain only alphanumeric characters.',
            'e_way_bill_no.max' => 'The E-Way bill number may not be greater than 50 characters.',
            'bilty_no.string' => 'The bilty number must be a string.',
            'bilty_no.alpha_num' => 'The bilty number must contain only alphanumeric characters.',
            'bilty_no.max' => 'The bilty number may not be greater than 50 characters.',
            'transport_remarks.string' => 'The transport remarks must be a string.',
            'transport_remarks.max' => 'The transport remarks may not be greater than 2000 characters.',
            'terms_conditions.string' => 'The terms and conditions must be a string.',
            'terms_conditions.max' => 'The terms and conditions may not be greater than 5000 characters.',
            'additional_charges.numeric' => 'The additional charges must be a number.',
            'additional_charges.min' => 'The additional charges must be at least 0.',
            'additional_charges.max' => 'The additional charges may not be greater than 99,999,999.99.',
            'items.required' => 'At least one item is required for the dispatch.',
            'items.array' => 'The items must be an array.',
            'items.min' => 'At least one item is required for the dispatch.',
            'items.*.order_id.required' => 'The order ID is required for item #:index.',
            'items.*.order_id.integer' => 'The order ID must be an integer for item #:index.',
            'items.*.order_id.exists' => 'The selected order does not exist for item #:index.',
            'items.*.allocation_id.required' => 'The allocation ID is required for item #:index.',
            'items.*.allocation_id.integer' => 'The allocation ID must be an integer for item #:index.',
            'items.*.allocation_id.exists' => 'The selected allocation does not exist for item #:index.',
            'items.*.order_qty.required' => 'The order quantity is required for item #:index.',
            'items.*.order_qty.numeric' => 'The order quantity must be a number for item #:index.',
            'items.*.order_qty.min' => 'The order quantity must be at least 0 for item #:index.',
            'items.*.order_qty.max' => 'The order quantity may not be greater than 999,999.99 for item #:index.',
            'items.*.already_disp.required' => 'The already dispatched quantity is required for item #:index.',
            'items.*.already_disp.numeric' => 'The already dispatched quantity must be a number for item #:index.',
            'items.*.already_disp.min' => 'The already dispatched quantity must be at least 0 for item #:index.',
            'items.*.already_disp.max' => 'The already dispatched quantity may not be greater than 999,999.99 for item #:index.',
            'items.*.remaining_qty.required' => 'The remaining quantity is required for item #:index.',
            'items.*.remaining_qty.numeric' => 'The remaining quantity must be a number for item #:index.',
            'items.*.remaining_qty.min' => 'The remaining quantity must be at least 0 for item #:index.',
            'items.*.remaining_qty.max' => 'The remaining quantity may not be greater than 999,999.99 for item #:index.',
            'items.*.item_name.required' => 'The item name is required for item #:index.',
            'items.*.item_name.string' => 'The item name must be a string for item #:index.',
            'items.*.item_name.max' => 'The item name may not be greater than 255 characters for item #:index.',
            'items.*.size.required' => 'The size is required for item #:index.',
            'items.*.size.integer' => 'The size must be an integer for item #:index.',
            'items.*.size.exists' => 'The selected size does not exist for item #:index.',
            'items.*.dispatch_qty.required' => 'The dispatch quantity is required for item #:index.',
            'items.*.dispatch_qty.numeric' => 'The dispatch quantity must be a number for item #:index.',
            'items.*.dispatch_qty.min' => 'The dispatch quantity must be at least 0.01 for item #:index.',
            'items.*.dispatch_qty.max' => 'The dispatch quantity may not be greater than 999,999.99 for item #:index.',
            'items.*.basic_price.required' => 'The basic price is required for item #:index.',
            'items.*.basic_price.numeric' => 'The basic price must be a number for item #:index.',
            'items.*.basic_price.min' => 'The basic price must be at least 0 for item #:index.',
            'items.*.basic_price.max' => 'The basic price may not be greater than 999,999.99 for item #:index.',
            'items.*.gauge_diff.required' => 'The gauge difference is required for item #:index.',
            'items.*.gauge_diff.numeric' => 'The gauge difference must be a number for item #:index.',
            'items.*.gauge_diff.min' => 'The gauge difference must be at least -999,999.99 for item #:index.',
            'items.*.gauge_diff.max' => 'The gauge difference may not be greater than 999,999.99 for item #:index.',
            'items.*.final_price.required' => 'The final price is required for item #:index.',
            'items.*.final_price.numeric' => 'The final price must be a number for item #:index.',
            'items.*.final_price.min' => 'The final price must be at least 0 for item #:index.',
            'items.*.final_price.max' => 'The final price may not be greater than 999,999.99 for item #:index.',
            'items.*.loading_charge.required' => 'The loading charge is required for item #:index.',
            'items.*.loading_charge.numeric' => 'The loading charge must be a number for item #:index.',
            'items.*.loading_charge.min' => 'The loading charge must be at least 0 for item #:index.',
            'items.*.loading_charge.max' => 'The loading charge may not be greater than 999,999.99 for item #:index.',
            'items.*.insurance.required' => 'The insurance charge is required for item #:index.',
            'items.*.insurance.numeric' => 'The insurance charge must be a number for item #:index.',
            'items.*.insurance.min' => 'The insurance charge must be at least 0 for item #:index.',
            'items.*.insurance.max' => 'The insurance charge may not be greater than 999,999.99 for item #:index.',
            'items.*.gst.required' => 'The GST percentage is required for item #:index.',
            'items.*.gst.numeric' => 'The GST percentage must be a number for item #:index.',
            'items.*.gst.min' => 'The GST percentage must be at least 0 for item #:index.',
            'items.*.gst.max' => 'The GST percentage may not be greater than 100 for item #:index.',
            'items.*.token_amount.numeric' => 'The token amount must be a number for item #:index.',
            'items.*.token_amount.min' => 'The token amount must be at least 0 for item #:index.',
            'items.*.token_amount.max' => 'The token amount may not be greater than 999,999.99 for item #:index.',
            'items.*.total_amount.required' => 'The total amount is required for item #:index.',
            'items.*.total_amount.numeric' => 'The total amount must be a number for item #:index.',
            'items.*.total_amount.min' => 'The total amount must be at least 0 for item #:index.',
            'items.*.total_amount.max' => 'The total amount may not be greater than 999,999,999.99 for item #:index.',
            'items.*.payment_term.string' => 'The payment term must be a string for item #:index.',
            'items.*.payment_term.in' => 'The payment term must be one of: Advance, Next Day, 15 Days Later, 30 Days Later for item #:index.',
            'items.*.remark.string' => 'The remark must be a string for item #:index.',
            'items.*.remark.max' => 'The remark may not be greater than 2000 characters for item #:index.',
            'attachments.array' => 'The attachments must be an array.',
            'attachments.*.document.file' => 'The document must be a file for attachment #:index.',
            'attachments.*.document.max' => 'The document may not be greater than 2MB for attachment #:index.',
            'attachments.*.remark.string' => 'The remark must be a string for attachment #:index.',
            'attachments.*.remark.max' => 'The remark may not be greater than 2000 characters for attachment #:index.',
        ]);

        // Replace :index in messages with actual index +1 for user-friendliness
        $messages = $validator->messages();
        foreach ($messages->keys() as $key) {
            if (str_contains($key, 'items.') || str_contains($key, 'attachments.')) {
                $index = explode('.', $key)[1];
                $humanIndex = (int)$index + 1;
                foreach ($messages->get($key) as $message) {
                    $messages->add($key, str_replace('#:index', $humanIndex, $message));
                }
            }
        }

        // After validation hook for additional business logic checks
        $validator->after(function ($validator) use ($request) {
            if ($validator->errors()->any()) {
                return;
            }

            // Check for duplicate allocation and size pairs in items
            $pairs = [];
            foreach ($request->input('items', []) as $index => $item) {
                $pair = $item['allocation_id'] . '-' . $item['size'];
                if (in_array($pair, $pairs)) {
                    $validator->errors()->add("items.$index.size", 'Duplicate allocation and size combination for item ' . ($index + 1) . '.');
                } else {
                    $pairs[] = $pair;
                }
            }

            // Check dispatch_qty against allocation remaining_qty with threshold at order level
            $order_proposed = [];
            foreach ($request->input('items', []) as $index => $item) {
                $order_id = $item['order_id'];
                if (!isset($order_proposed[$order_id])) {
                    $order_proposed[$order_id] = 0;
                }
                $order_proposed[$order_id] += (float) $item['dispatch_qty'];
            }

            foreach ($order_proposed as $order_id => $proposed) {
                $total_remaining = OrderAllocation::where('order_id', $order_id)->sum('remaining_qty');
                if ($proposed > $total_remaining + 5) {
                    $validator->errors()->add('items', 'Total dispatch quantity exceeds remaining quantity + 5 MT threshold for order #' . $order_id . '. Remaining: ' . $total_remaining . ' MT');
                }
            }

            // Per-item checks (tampering only, no strict quantity check)
            foreach ($request->input('items', []) as $index => $item) {
                $allocationId = $item['allocation_id'];

                $allocation = OrderAllocation::find($allocationId);
                if (!$allocation) {
                    $validator->errors()->add("items.$index.allocation_id", 'Invalid allocation selected for item ' . ($index + 1) . '.');
                    continue;
                }

                // Verify frontend quantities match DB (security check)
                // if ((float) $item['remaining_qty'] != $allocation->remaining_qty) {
                //     $validator->errors()->add("items.$index.remaining_qty", 'Remaining quantity mismatch for item ' . ($index + 1) . '. Please refresh and try again.');
                // }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Get validated data
        $validated = $validator->validated();

        // Start transaction for data integrity
        try {
            DB::beginTransaction();

            // Handle payment_slip upload
            $paymentSlipPath = $dispatch->payment_slip;
            if ($request->hasFile('payment_slip')) {
                // Delete old payment slip if it exists
                if ($paymentSlipPath) {
                    Storage::disk('public')->delete($paymentSlipPath);
                }
                $paymentSlipPath = $request->file('payment_slip')->store('dispatches/payment_slips', 'public');
            }

            // Update the dispatch record
            $dispatch->update([
                'dispatch_number' => $validated['dispatch_number'],
                'type' => $validated['type'],
                'distributor_id' => $validated['type'] === 'distributor' ? $validated['distributor_id'] : null,
                'dealer_id' => $validated['type'] === 'dealer' ? $validated['dealer_id'] : null,
                'recipient_name' => $validated['recipient_name'],
                'recipient_address' => $validated['recipient_address'],
                'recipient_state_id' => $validated['recipient_state'],
                'recipient_city_id' => $validated['recipient_city'],
                'recipient_pincode' => $validated['recipient_pincode'],
                'consignee_name' => $validated['consignee_name'],
                'consignee_address' => $validated['consignee_address'],
                'consignee_state_id' => $validated['consignee_state'],
                'consignee_city_id' => $validated['consignee_city'],
                'consignee_pincode' => $validated['consignee_pincode'],
                'consignee_mobile_no' => $validated['consignee_mobile_no'] ?? null,
                'dispatch_date' => $validated['dispatch_date'],
                'warehouse_id' => $request['warehouse_id'] ?? null,
                'bill_to' => $validated['bill_to'] ?? null,
                'bill_number' => $validated['bill_number'],
                'dispatch_out_time' => $validated['dispatch_out_time'] ?? null,
                'payment_slip' => $paymentSlipPath,
                'dispatch_remarks' => $validated['dispatch_remarks'] ?? null,
                'transporter_name' => $validated['transporter_name'],
                'vehicle_no' => $validated['vehicle_no'] ?? null,
                'driver_name' => $validated['driver_name'] ?? null,
                'driver_mobile_no' => $validated['driver_mobile_no'] ?? null,
                'e_way_bill_no' => $validated['e_way_bill_no'] ?? null,
                'bilty_no' => $validated['bilty_no'] ?? null,
                'transport_remarks' => $validated['transport_remarks'] ?? null,
                'terms_conditions' => $validated['terms_conditions'] ?? null,
                'additional_charges' => (float) ($validated['additional_charges'] ?? 0.00),
                'total_amount' => 0.00, // To be updated
                'status' => 'pending'
            ]);

            // Delete existing items to replace with new ones
            $oldItems = DispatchItem::where('dispatch_id', $dispatch->id)->get();
            foreach ($oldItems as $oldItem) {
                $oldItem->delete();
            }

            // Process items, recalculate totals for security
            $grandTotal = (float) ($validated['additional_charges'] ?? 0.00);
            foreach ($validated['items'] as $item) {
                $finalPrice = (float) $item['basic_price'] + (float) $item['gauge_diff'];
                $baseTotal = $finalPrice + (float) $item['loading_charge'] + (float) $item['insurance'];
                $baseTotal *= (float) $item['dispatch_qty'];
                $gstAmount = $baseTotal * ((float) $item['gst'] / 100);
                $tokenAmount = (float) ($item['token_amount'] ?? 0.00);
                $itemTotal = $baseTotal + $gstAmount - $tokenAmount;

                // Verify calculated final_price and total_amount match submitted (prevent tampering)
                if (abs($finalPrice - (float) $item['final_price']) > 0.01) {
                    throw new Exception('Final price mismatch for an item. Possible tampering detected.');
                }
                if (abs($itemTotal - (float) $item['total_amount']) > 0.01) {
                    throw new Exception('Total amount mismatch for an item. Possible tampering detected.');
                }

                DispatchItem::create([
                    'dispatch_id' => $dispatch->id,
                    'item_id' => 1,
                    'order_id' => $item['order_id'],
                    'allocation_id' => $item['allocation_id'],
                    'order_qty' => (float) $item['order_qty'],
                    'already_disp' => (float) $item['already_disp'],
                    'remaining_qty' => (float) $item['remaining_qty'],
                    'item_name' => $item['item_name'],
                    'size_id' => $item['size'],
                    // 'length' => (float) $item['length'],
                    'dispatch_qty' => (float) $item['dispatch_qty'],
                    'basic_price' => (float) $item['basic_price'],
                    'gauge_diff' => (float) $item['gauge_diff'],
                    'final_price' => $finalPrice,
                    'loading_charge' => (float) $item['loading_charge'],
                    'insurance' => (float) $item['insurance'],
                    'gst' => (float) $item['gst'],
                    'token_amount' => $tokenAmount > 0 ? $tokenAmount : null,
                    'total_amount' => $itemTotal,
                    'payment_term' => $item['payment_term'] ?? null,
                    'status' => 'pending',
                    'remark' => $item['remark'] ?? null,
                ]);

                $grandTotal += $itemTotal;
            }

            // Update dispatch total_amount
            $dispatch->total_amount = $grandTotal;
            $dispatch->save();

            // Handle attachments
            $attachmentPaths = [];
            $files = $request->file('attachments');
            // Delete existing attachments
            $oldAttachments = DispatchAttachment::where('dispatch_id', $dispatch->id)->get();
            foreach ($oldAttachments as $oldAttachment) {
                if ($oldAttachment->document) {
                    Storage::disk('public')->delete($oldAttachment->document);
                }
                $oldAttachment->delete();
            }

            if ($files && is_array($files)) {
                Log::info('Attachments found in request', ['count' => count($files)]);
                foreach ($files as $key => $attachment) {
                    if (isset($attachment['document']) && $attachment['document'] instanceof \Illuminate\Http\UploadedFile && $attachment['document']->isValid()) {
                        Log::info('Processing attachment', [
                            'key' => $key,
                            'filename' => $attachment['document']->getClientOriginalName(),
                            'size' => $attachment['document']->getSize(),
                        ]);
                        $extension = $attachment['document']->getClientOriginalExtension();
                        $filename = 'attachment_' . $validated['dispatch_number'] . '_' . $key . '_' . time() . '_' . Str::random(10) . '.' . $extension;
                        $path = $attachment['document']->storeAs('dispatches/attachments', $filename, 'public');
                        $attachmentPaths[] = $path;
                        DispatchAttachment::create([
                            'dispatch_id' => $dispatch->id,
                            'document' => $path,
                            'remark' => $request->input("attachments.$key.remark") ?? null,
                        ]);
                    } else {
                        Log::warning('Invalid or missing document for attachment', ['key' => $key]);
                    }
                }
            } else {
                Log::info('No attachments found in request', ['input' => $request->input('attachments')]);
            }

            DB::commit();

            return redirect()->route('dispatch.index')
                ->with('success', 'Dispatch updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();

            // Clean up uploaded files on failure
            if ($paymentSlipPath && $paymentSlipPath !== $dispatch->payment_slip) {
                Storage::disk('public')->delete($paymentSlipPath);
            }
            foreach ($attachmentPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            return redirect()->back()
                ->with('error', 'Failed to update dispatch: ' . $e->getMessage())
                ->withInput();
        }
    }


    public function getParties(Request $request)
    {
        $type = $request->get('type');

        if ($type === 'dealer') {
            $parties = Dealer::select('id', 'name')->get();
        } elseif ($type === 'distributor') {
            $parties = Distributor::select('id', 'name')->get();
        } else {
            $parties = collect();
        }

        return response()->json($parties);
    }

    public function getOrders(Request $request)
    {
        $type = $request->query('type');
        $partyId = $request->query('party_id');

        if (!$type || !$partyId) {
            return response()->json([], 400); // Bad Request
        }

        $query = Order::select('id', 'order_number', 'created_at')
            ->where('type', $type)
            ->whereIn('status', ['approved', 'partial dispatch'])
            ->with(['allocations' => function ($q) {
                $q->select('order_id', 'qty', 'remaining_qty');
            }]);

        if ($type === 'dealer') {
            $query->where('placed_by_dealer_id', $partyId);
        } elseif ($type === 'distributor') {
            $query->where('placed_by_distributor_id', $partyId);
        }

        $orders = $query->get();

        return response()->json($orders);
    }

    public function destroy(Dispatch $dispatch)
    {
        // Optional: Double-check status (defense in depth)
        if (strtolower($dispatch->status) !== 'pending') {
            return redirect()->route('dispatch.index')
                ->with('error', 'Only pending dispatches can be deleted.');
        }

        DB::transaction(function () use ($dispatch) {
            // Delete related records first (adjust based on your relationships)
            $dispatch->items()->delete();           // dispatch_items
            $dispatch->attachments()->delete();     // dispatch_attachments
            $dispatch->delete();                    // main dispatch
        });

        return redirect()->route('dispatch.index')
            ->with('success', 'Dispatch deleted successfully.');
    }


    public function generateDispatchNumber(Request $request)
    {
        $date = $request->input('date'); // expected: YYYY-MM-DD

        if (!$date) {
            return response()->json(['error' => 'Invalid date'], 400);
        }

        try {
            $parsedDate = \Carbon\Carbon::parse($date);

            // Determine financial year (starts April 1st)
            $year = $parsedDate->month >= 4 ? $parsedDate->year : $parsedDate->year - 1;
            $nextYear = substr($year + 1, -2); // Get last two digits of next year
            $financialYear = "{$year}-{$nextYear}";

            // Define financial year start and end dates
            $yearStart = \Carbon\Carbon::create($year, 4, 1)->startOfDay();
            $yearEnd = \Carbon\Carbon::create($year + 1, 3, 31)->endOfDay();

            // Count existing dispatches for the current financial year
            $count = Dispatch::whereBetween('dispatch_date', [$yearStart, $yearEnd])->count();

            // Indexing starts from 1, so add 1
            $index = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

            // Final Dispatch Number
            $dispatchNumber = "DO_{$financialYear}_{$index}";

            return response()->json(['dispatch_number' => $dispatchNumber]);
        } catch (\Exception $e) {
            \Log::error('Error generating dispatch number: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate dispatch number'], 500);
        }
    }

    public function getLineItems($id)
    {
        $dispatch = Dispatch::with('dispatchItems.order')->find($id);

        if (!$dispatch) {
            return response()->json([], 404);
        }

        $items = $dispatch->dispatchItems->map(function ($item) {
            return [
                'item_name' => $item->order->order_number
                    ?? $item->order->id
                    ?? 'N/A',
                'dispatch_qty' => $item->dispatch_qty,
                'size' => $item->size->size,
                'length' => $item->length,
                'price_per_mt' => $item->final_price_per_mt,
                'total_price' => $item->total_amount,
            ];
        });
        return response()->json($items);
    }

    public function approve($id)
    {
        $dispatch = Dispatch::with('dispatchItems')->findOrFail($id);

        // Check required fields before approval
        $requiredFields = [
            'bill_to',
            'bill_number',
            'dispatch_date',
            'transporter_name',
            'vehicle_no',
            'driver_name',
            'driver_mobile_no',
            'e_way_bill_no',
            'bilty_no'
        ];

        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (empty($dispatch->$field)) {
                $missingFields[] = ucwords(str_replace('_', ' ', $field));
            }
        }

        if (!empty($missingFields)) {
            $message = 'The following fields are required before approval: ' . implode(', ', $missingFields) . '. Please update the dispatch and try again.';
            return redirect()->route('dispatch.index')->with('error', $message);
        }

        foreach ($dispatch->dispatchItems as $item) {
            $order = $item->order; // Assuming you have relationship: dispatchItem -> order

            if ($order && $order->status === 'closed with condition') {
                return redirect()->route('dispatch.index')->with(
                    'error',
                    "Cannot approve dispatch. Order #{$order->order_number} is already closed with condition."
                );
            }
        }

        // If already approved, prevent double approval
        if ($dispatch->status === 'Approved') {
            return redirect()->route('dispatch.index')->with('error', 'This dispatch is already approved and cannot be approved again.');
        }

        DB::beginTransaction();

        try {
            // Check threshold at order level before processing
            $order_proposed = [];
            foreach ($dispatch->dispatchItems as $item) {
                $order_id = $item->order_id;
                if (!isset($order_proposed[$order_id])) {
                    $order_proposed[$order_id] = 0;
                }
                $order_proposed[$order_id] += $item->dispatch_qty;
            }

            foreach ($order_proposed as $order_id => $proposed) {
                $total_remaining = OrderAllocation::where('order_id', $order_id)->sum('remaining_qty');
                if ($proposed > $total_remaining + 5) {
                    throw new \Exception('Total dispatch quantity exceeds remaining quantity + 5 MT threshold for order #' . $order_id . '. Remaining: ' . $total_remaining . ' MT');
                }
            }

            // Process allocations
            $uniqueOrders = [];
            $dispatchedQtyPerOrder = [];
            $dealerUpdates = []; // dealer_id => total qty to add to allowed_order_limit
            $distributorUpdates = []; // distributor_id => total qty to add to allowed_order_limit
            $distributorIndividualUpdates = []; // distributor_id => total qty to add to individual_allowed_order_limit

            foreach ($dispatch->dispatchItems as $item) {
                $allocation = OrderAllocation::findOrFail($item->allocation_id);

                // Fetch dealer or distributor name for error messages
                $entityName = '';
                if ($allocation->allocated_to_type === 'dealer') {
                    $dealer = Dealer::find($allocation->allocated_to_id);
                    $entityName = $dealer ? $dealer->name : 'Dealer ID ' . $allocation->allocated_to_id;
                } elseif ($allocation->allocated_to_type === 'distributor') {
                    $distributor = Distributor::find($allocation->allocated_to_id);
                    $entityName = $distributor ? $distributor->name : 'Distributor ID ' . $allocation->allocated_to_id;
                }

                // Calculate effective qty for limit updates (cap at remaining to avoid fluctuating by threshold)
                $effective_qty = min($item->dispatch_qty, $allocation->remaining_qty);

                $allocation->dispatched_qty += $item->dispatch_qty;
                $allocation->remaining_qty -= $item->dispatch_qty;

                if ($allocation->dispatched_qty >= $allocation->qty) {
                    $allocation->status = 'completed';
                } elseif ($allocation->dispatched_qty > 0 && $allocation->dispatched_qty < $allocation->qty) {
                    $allocation->status = 'partially dispatched';
                }

                $allocation->save();

                // Collect unique order IDs
                $uniqueOrders[$item->order_id] = true;

                // Arsh changes for already dispatched qty tracking

                // Sync dispatch_item already_disp with allocation dispatched total (keeps single source of truth)
                try {
                    // Option A: set already_disp to allocation's dispatched_qty
                    $item->already_disp = $allocation->dispatched_qty;
                    $item->save();

                    // --- Alternative options (pick one and comment the others) ---
                    // Option B: increment per-dispatch (uncomment if you prefer increment):
                    // $item->already_disp = ($item->already_disp ?? 0) + $item->dispatch_qty;
                    // $item->save();

                    // Option C: bulk/increment (avoids save() on model, good for many rows)
                    // DispatchItem::where('id', $item->id)->increment('already_disp', $item->dispatch_qty);
                } catch (\Exception $e) {
                    \Log::error("Failed to update dispatch_item already_disp for item id {$item->id}: " . $e->getMessage());
                    // If you want to fail the whole transaction on error, rethrow:
                    // throw $e;
                }

                // Arsh changes end for already dispatched qty tracking

                // Sum dispatched qty per order for this dispatch
                if (!isset($dispatchedQtyPerOrder[$item->order_id])) {
                    $dispatchedQtyPerOrder[$item->order_id] = 0;
                }
                $dispatchedQtyPerOrder[$item->order_id] += $item->dispatch_qty;

                // Handle order limit updates based on allocation (using effective_qty)
                $order = Order::findOrFail($item->order_id);

                if ($order->type === 'distributor') {
                    $distributor_id = $order->placed_by_distributor_id;

                    if ($allocation->allocated_to_type === 'distributor') {
                        // For distributor/self allocations: increase individual and allowed for distributor
                        if (!isset($distributorIndividualUpdates[$distributor_id])) {
                            $distributorIndividualUpdates[$distributor_id] = 0;
                        }
                        $distributorIndividualUpdates[$distributor_id] += $effective_qty;

                        if (!isset($distributorUpdates[$distributor_id])) {
                            $distributorUpdates[$distributor_id] = 0;
                        }
                        $distributorUpdates[$distributor_id] += $effective_qty;
                    } elseif ($allocation->allocated_to_type === 'dealer') {
                        // For dealer allocations: increase allowed for dealer and distributor
                        $dealer_id = $allocation->allocated_to_id;

                        if (!isset($dealerUpdates[$dealer_id])) {
                            $dealerUpdates[$dealer_id] = 0;
                        }
                        $dealerUpdates[$dealer_id] += $effective_qty;

                        if (!isset($distributorUpdates[$distributor_id])) {
                            $distributorUpdates[$distributor_id] = 0;
                        }
                        $distributorUpdates[$distributor_id] += $effective_qty;
                    }
                } elseif ($order->type === 'dealer') {
                    // For dealer orders: increase allowed for dealer, and if assigned distributor exists, for distributor too
                    $dealer_id = $order->placed_by_dealer_id;

                    if (!isset($dealerUpdates[$dealer_id])) {
                        $dealerUpdates[$dealer_id] = 0;
                    }
                    $dealerUpdates[$dealer_id] += $effective_qty;

                    $dealer = Dealer::findOrFail($dealer_id);
                    $assigned_distributor_id = $dealer->assigned_distributor_id; // Corrected to assigned_distributor_id

                    if ($assigned_distributor_id) {
                        $distributor = Distributor::find($assigned_distributor_id);
                        if (!$distributor) {
                            \Log::warning("Assigned distributor ID {$assigned_distributor_id} for dealer ID {$dealer_id} not found.");
                            continue;
                        }

                        if (!isset($distributorUpdates[$assigned_distributor_id])) {
                            $distributorUpdates[$assigned_distributor_id] = 0;
                        }
                        $distributorUpdates[$assigned_distributor_id] += $effective_qty;
                        \Log::info("Updating distributor ID {$assigned_distributor_id} with qty {$effective_qty} for dealer order ID {$order->id}");
                    } else {
                        \Log::warning("No assigned distributor for dealer ID {$dealer_id} in order ID {$order->id}");
                    }
                }
            }

            // Update order statuses based on allocations
            foreach (array_keys($uniqueOrders) as $orderId) {
                $order = Order::findOrFail($orderId);

                // Check if all allocations are completed
                $allCompleted = $order->allocations->every(function ($alloc) {
                    return $alloc->status === 'completed';
                });

                if ($allCompleted) {
                    $order->status = 'completed';
                } else {
                    $totalDispatchedQty = $order->allocations->sum('dispatched_qty');
                    $totalOrderQty = $order->allocations->sum('qty');
                    if ($totalDispatchedQty > 0 && $totalDispatchedQty < $totalOrderQty) {
                        $order->status = 'partial dispatch';
                    }
                }

                $order->save();
            }

            // Apply aggregated updates to dealers
            foreach ($dealerUpdates as $dealerId => $qty) {
                $dealer = Dealer::findOrFail($dealerId);
                $dealer->allowed_order_limit += $qty;
                $dealer->save();
                \Log::info("Updated dealer ID {$dealerId} allowed_order_limit by {$qty}");
            }

            // Apply aggregated updates to distributors
            foreach ($distributorUpdates as $distributorId => $qty) {
                $distributor = Distributor::findOrFail($distributorId);
                $distributor->allowed_order_limit += $qty;

                $individualQty = $distributorIndividualUpdates[$distributorId] ?? 0;
                $distributor->individual_allowed_order_limit += $individualQty;

                $distributor->save();
                \Log::info("Updated distributor ID {$distributorId} allowed_order_limit by {$qty}, individual_allowed_order_limit by {$individualQty}");
            }

            // Update dispatch items' status to 'approved'
            DispatchItem::where('dispatch_id', $dispatch->id)->update(['status' => 'approved']);

            // Approve the dispatch
            $dispatch->status = 'Approved';
            $dispatch->save();

            DB::commit();

        if ($dispatch->type === 'dealer' && $dispatch->dealer_id) {
            $placer = \App\Models\Dealer::find($dispatch->dealer_id);
            $appUser = $placer ? AppUserManagement::where('code', $placer->code)
                ->where('type', 'dealer')->first() : null;
        } elseif ($dispatch->type === 'distributor' && $dispatch->distributor_id) {
            $placer = \App\Models\Distributor::find($dispatch->distributor_id);
            $appUser = $placer ? AppUserManagement::where('code', $placer->code)
                ->where('type', 'distributor')->first() : null;
        }

        $webUser = null;
        if ($placer) {
            $webUser = \App\Models\User::where('name', 'LIKE', "%{$placer->name}%")
                ->orWhere('email', $placer->email)
                ->first();
        }

        $notifiables = collect();
        if ($appUser) $notifiables->push($appUser);
        if ($webUser) $notifiables->push($webUser);

        if ($notifiables->isNotEmpty()) {
            Notification::send($notifiables, new DispatchApproved($dispatch));
        } else {
            \Log::warning("No notifiable user found for dispatch #{$dispatch->dispatch_number}");
        }

            return redirect()->route('dispatch.index')->with('success', 'Dispatch approved successfully. Allocations, orders, and order limits updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Dispatch approval failed: " . $e->getMessage());
            return redirect()->route('dispatch.index')->with('error', 'Dispatch approval failed: ' . $e->getMessage());
        }
    }

    public function getAllocations($orderId)
    {
        try {
            $order = Order::with('allocations.allocatable')->findOrFail($orderId);
            $allocations = $order->allocations
                ->where('remaining_qty', '>', 0)
                ->map(function ($allocation) {
                    $allocatable = $allocation->allocatable;
                    return [
                        'id' => $allocation->id,
                        'token_amount' => $allocation->token_amount,
                        'remaining_qty' => $allocation->remaining_qty,
                        'payment_terms' => $allocation->payment_terms,
                        'type' => $allocation->allocated_to_type,
                        'allocated_to_id' => $allocation->allocated_to_id,
                        'dealer_name' => $allocation->allocated_to_type === 'dealer' ? ($allocatable->name ?? 'N/A') : '',
                        'dealer_code' => $allocation->allocated_to_type === 'dealer' ? ($allocatable->code ?? 'N/A') : '',
                        'distributor_name' => $allocation->allocated_to_type === 'distributor' ? ($allocatable->name ?? 'N/A') : '',
                        'distributor_code' => $allocation->allocated_to_type === 'distributor' ? ($allocatable->code ?? 'N/A') : '',
                    ];
                })
                ->values(); // Reset keys to ensure an array

            return response()->json($allocations, 200);
        } catch (\Exception $e) {
            \Log::error('getAllocations Error: ' . $e->getMessage() . ' | Stack: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Failed to fetch allocations: ' . $e->getMessage()], 500);
        }
    }

    public function item()
    {
        $items = Item::select('id', 'item_name')->get();
        return response()->json($items);
    }

    public function allocations($allocationId)
    {
        $allocation = OrderAllocation::findOrFail($allocationId);

        return response()->json([
            'id' => $allocation->id,
            'dispatched_qty' => $allocation->dispatched_qty ?? 0,
            'agreed_basic_price' => $allocation->agreed_basic_price ?? 0, // This should now pull the actual value
            'payment_term' => $allocation->payment_terms ?? '',
            'loading_charge' => $allocation->loading_charge ?? 265,
            'insurance_charge' => $allocation->insurance_charge ?? 40,
            'gst_rate' => $allocation->gst_rate ?? 18,
            'token_amount' => $allocation->token_amount ?? 0.00,
            // Add any other fields needed, e.g., 'qty' => $allocation->qty
        ]);
    }

    public function getPartyList(Request $request)
    {
        $type = $request->query('type');

        if ($type === 'distributor') {
            $parties = Distributor::select('distributors.id', 'distributors.name')
                ->join('orders', 'distributors.id', '=', 'orders.placed_by_distributor_id')
                ->whereIn('orders.status', ['approved', 'partial dispatch'])
                ->distinct()
                ->get();
        } else {
            $parties = Dealer::select('dealers.id', 'dealers.name')
                ->join('orders', 'dealers.id', '=', 'orders.placed_by_dealer_id')
                ->whereIn('orders.status', ['approved', 'partial dispatch'])
                ->distinct()
                ->get();
        }

        return response()->json($parties);
    }

    public function getOrderList(Request $request)
    {
        $type = $request->query('type');
        $partyId = $request->query('party_id');

        if (!$type || !$partyId) {
            return response()->json([], 400); // Bad Request
        }

        $query = Order::select('id', 'order_number', 'created_at')
            ->where('type', $type)
            ->whereIn('status', ['approved', 'partial dispatch'])
            ->with(['allocations' => function ($q) {
                $q->select('order_id', 'qty', 'remaining_qty');
            }])
            ->withSum(['allocations as total_remaining_qty' => function ($q) {
                $q->select(DB::raw('SUM(CASE WHEN remaining_qty < 0 THEN 0 ELSE remaining_qty END)'));
            }], 'remaining_qty');

        if ($type === 'dealer') {
            $query->where('placed_by_dealer_id', $partyId);
        } elseif ($type === 'distributor') {
            $query->where('placed_by_distributor_id', $partyId);
        }

        $orders = $query->get();

        return response()->json($orders);
    }

    public function getDispatchItems($id)
    {
        $dispatch = Dispatch::with(['dispatchItems.order', 'dispatchItems.size', 'dispatchItems.item'])->findOrFail($id);
        return response()->json($dispatch->dispatchItems);
    }

    public function checkBillNumber(Request $request)
    {
        $billNumber = $request->query('bill_number');
        $dispatchId = $request->query('dispatch_id'); // may be null/empty/missing

        // Only bill_number is required
        if (!$billNumber) {
            return response()->json(['exists' => false]);
        }

        $query = Dispatch::where('bill_number', $billNumber);

        // Only exclude current dispatch when we actually have a valid ID (edit mode)
        if ($dispatchId && is_numeric($dispatchId) && $dispatchId > 0) {
            $query->where('id', '!=', $dispatchId);
        }

        $exists = $query->exists();

        return response()->json(['exists' => $exists]);
    }

    // Changes by md raza start
    // public function downloadDispatchPDF($id)
    // {
    //     try {
    //         // This line fetches the dispatch data and stores it in the $dispatch variable
    //         $dispatch = Dispatch::with([
    //             'dealer',
    //             'distributor',
    //             'warehouse',
    //             'attachments',
    //             'dispatchItems.order',
    //             'dispatchItems.size',
    //         ])->findOrFail($id);

    //         // You can calculate other variables as needed
    //         $totalQty = $dispatch->dispatchItems->sum('dispatch_qty');
    //         $grandTotal = $dispatch->dispatchItems->sum('total_amount');
    //         $amountInWords = NumberHelper::amountInWords($grandTotal);

    //         $data = [
    //             'dispatch'      => $dispatch,
    //             'totalQty'      => $totalQty,
    //             'amountInWords' => $amountInWords,
    //         ];

    //         // Load the view and pass the $data array to it.
    //         $pdf = Pdf::loadView('pdf.dispatch', $data);

    //         // ... (rest of the code)

    //         return $pdf->stream('Dispatch-' . $dispatch->dispatch_number . '.pdf');
    //     } catch (\Exception $e) {
    //         \Log::error('Dispatch PDF generation failed: ' . $e->getMessage());
    //         return redirect()->back()->with('error', 'Could not generate PDF.');
    //     }
    // }


    public function downloadDispatchPDF($id)
    {
        try {
            // --- Image Preparation (Added Section) ---
            $logoPath = public_path('assets/img/logo.png');
            $sealPath = public_path('assets/img/singhal -Stamp.png');

            $logoBase64 = '';
            if (File::exists($logoPath)) {
                $logoType = File::mimeType($logoPath);
                $logoData = File::get($logoPath);
                $logoBase64 = 'data:' . $logoType . ';base64,' . base64_encode($logoData);
            }

            $sealBase64 = '';
            if (File::exists($sealPath)) {
                $sealType = File::mimeType($sealPath);
                $sealData = File::get($sealPath);
                $sealBase64 = 'data:' . $sealType . ';base64,' . base64_encode($sealData);
            }

            // --- Fetch Dispatch Data ---
            $dispatch = Dispatch::with([
                'dealer',
                'distributor',
                'warehouse',
                'attachments',
                'dispatchItems.order',
                'dispatchItems.size',
            ])->findOrFail($id);

            // --- Calculations ---
            $totalQty = $dispatch->dispatchItems->sum('dispatch_qty');
            $grandTotal = $dispatch->dispatchItems->sum('total_amount');
            $amountInWords = NumberHelper::amountInWords($grandTotal);

            $company_settings = CompanySetting::firstOrCreate([]);


            // --- Data for the View (Updated) ---
            $data = [
                'dispatch'      => $dispatch,
                'totalQty'      => $totalQty,
                'amountInWords' => $amountInWords,
                'logoBase64'    => $logoBase64, // Added logo
                'sealBase64'    => $sealBase64, // Added seal
                'company_settings' => $company_settings,
            ];

            // Load the view and pass the data to it.
            $pdf = Pdf::loadView('pdf.dispatch', $data);

            // Stream the final PDF
            return $pdf->stream('Dispatch-' . $dispatch->dispatch_number . '.pdf');
        } catch (\Exception $e) {
            \Log::error('Dispatch PDF generation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Could not generate PDF.');
        }
    }
    // Changes by md raza end
}
