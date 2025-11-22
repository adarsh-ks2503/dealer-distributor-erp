<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dealer;
use App\Models\Distributor;
use App\Models\Order;
use App\Models\ItemBasicPrice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\OrderAttachment;
use Carbon\Carbon;
use App\Models\OrderAllocation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Notifications\OrderApproved;
use App\Notifications\NewOrderPlaced;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Models\AppUserManagement;
use App\Notifications\OrderStatusChanged;

// Changes by md raza start
use App\Helpers\NumberHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use App\Models\CompanySetting;
use Illuminate\Support\Facades\Log;
// Changes by md raza end

class OrderManagementController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:Order-Index', ['only' => ['index']]);
        $this->middleware('permission:Order-Create', ['only' => ['create', 'store']]);
        $this->middleware('permission:Order-Edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:Order-Delete', ['only' => ['destroy']]);
        $this->middleware('permission:Order-View', ['only' => ['show']]);
        $this->middleware('permission:Order-Approve', ['only' => ['approve']]);
    }
    public function index()
    {
        $dealers = Dealer::select('id', 'name')->where('status', 'Active')->orderBy('name')->get();
        $distributors = Distributor::select('id', 'name')->where('status', 'Active')->orderBy('name')->get();
        $orders = Order::with('distributor', 'dealer', 'allocations')->orderBy('created_at','DESC')->get();
        return view('orders.index', compact('dealers', 'distributors', 'orders'));
    }

    public function create(Request $request)
    {
        $orders = [];
        $type = $request->query('type');
        $partyId = $request->query('party');
        $orderNumber = Order::generateOrderNumber();

        $party = null;
        $assignedDealers = [];

        if ($type === 'dealer') {
            $party = Dealer::find($partyId);
        } elseif ($type === 'distributor') {
            $party = Distributor::find($partyId);
            $assignedDealers = Dealer::where('distributor_id', $party->id)->get();
        } else {
            return redirect()->route('order_management')->with('error', 'Invalid order type!');
        }

        if (!$party) {
            return redirect()->route('order_management')->with('error', 'Party not found!');
        }

        $state_id = $party->state_id;

        // TRY to get basic price
        $basicPrice = ItemBasicPrice::where('region', $state_id)
            ->where('status', 'Approved')
            ->first();

        // IF NOT FOUND → SHOW ERROR
        if (!$basicPrice) {
            return redirect()->route('order_management')
                ->with('error', "No approved basic price found for the state: {$party->state->state}. Please contact admin.");
        }

        return view('orders.create', compact('orders','party', 'type', 'orderNumber', 'basicPrice', 'assignedDealers'));
    }

    public function store(Request $request)
    {
        Log::info('Request Data', $request->all());

        $customMessages = [
            'type.required' => 'Please select the order type (Dealer or Distributor).',
            'type.in' => 'The order type must be either Dealer or Distributor.',
            'order_number.required' => 'The order number is required.',
            'order_number.unique' => 'The order number has already been used.',
            'order_date.required' => 'The order date is required.',
            'order_date.date' => 'The order date must be a valid date.',
            'loading_charge.numeric' => 'The loading charge must be a valid number.',
            'insurance_charge.numeric' => 'The insurance charge must be a valid number.',
            'created_by.required' => 'The created by field is required.',
            'created_by.string' => 'The created by field must be a valid string.',
            'remarks.string' => 'The remarks must be a valid string.',
            'terms_conditions.string' => 'The terms and conditions must be a valid string.',
            'orders.required' => 'At least one line item is required for the order.',
            'orders.array' => 'The line items must be provided in a valid format.',
            'orders.min' => 'At least one line item is required for the order.',
            'orders.*.for_type.required' => 'Please specify whether the line item is for self or dealer.',
            'orders.*.for_type.in' => 'The line item type must be either Self or Dealer.',
            'orders.*.order_qty.required' => 'The order quantity is required.',
            'orders.*.order_qty.numeric' => 'The order quantity must be a valid number.',
            'orders.*.order_qty.min' => 'The order quantity must be at least 0.1 MT.',
            'orders.*.agreed_basic_price.required' => 'The agreed basic price is required.',
            'orders.*.agreed_basic_price.numeric' => 'The agreed basic price must be a valid number.',
            'orders.*.agreed_basic_price.min' => 'The agreed basic price cannot be negative.',
            'orders.*.basic_price.required' => 'The basic price is required.',
            'orders.*.basic_price.numeric' => 'The basic price must be a valid number.',
            'orders.*.basic_price.min' => 'The basic price cannot be negative.',
            'orders.*.token_amount.numeric' => 'The token amount must be a valid number.',
            'orders.*.token_amount.min' => 'The token amount cannot be negative.',
            'orders.*.payment_term.required' => 'The payment term is required.',
            'orders.*.payment_term.in' => 'The payment term must be one of: Advance, Next Day, 15 Days Later, 30 Days Later.',
            'orders.*.dealer_id.required_if' => 'The dealer ID is required when the line item is for a dealer.',
            'orders.*.dealer_id.numeric' => 'The dealer ID must be a valid number.',
            'orders.*.dealer_id.exists' => 'The selected dealer does not exist.',
            'attachments.*.file' => 'Each attachment must be a valid file.',
            'attachments.*.max' => 'Each attachment must not exceed 2MB in size.',
            'atch_remarks.*.string' => 'Attachment remarks must be a valid string.',
        ];

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:dealer,distributor',
            'order_number' => 'required|unique:orders,order_number',
            'order_date' => 'required|date',
            'loading_charge' => 'nullable|numeric',
            'insurance_charge' => 'nullable|numeric',
            'created_by' => 'required|string',
            'remarks' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'orders' => 'required|array|min:1',
            'orders.*.for_type' => 'required|in:self,dealer',
            'orders.*.order_qty' => 'required|numeric|min:0.1',
            'orders.*.agreed_basic_price' => 'required|numeric|min:0',
            'orders.*.basic_price' => 'required|numeric|min:0',
            'orders.*.token_amount' => 'nullable|numeric|min:0',
            'orders.*.payment_term' => 'required|in:Advance,Next Day,15 Days Later,30 Days Later',
            'orders.*.dealer_id' => 'required_if:orders.*.for_type,dealer|numeric|exists:dealers,id',
            'attachments.*' => 'nullable|file|max:2048',
            'atch_remarks.*' => 'nullable|string',
            'team_id' => 'nullable|exists:distributor_teams,id',
        ], $customMessages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $orders = $request->orders;

        if (!is_array($orders) || count($orders) === 0) {
            return response()->json(['errors' => ['orders' => ['At least one line item is required for the order.']]], 422);
        }

        DB::beginTransaction();

        try {
            // Validate quantities against limits
            foreach ($orders as $index => $line) {
                $forType = $line['for_type'] ?? null;
                $qty = $line['order_qty'] ?? 0;

                if ($forType === 'self') {
                    $partyId = $request->input($request->type . '_id');
                    $party = ($request->type === 'dealer') ? Dealer::find($partyId) : Distributor::find($partyId);
                    if (!$party) {
                        return response()->json(['errors' => ["orders.$index" => ['The specified party does not exist.']]], 422);
                    }
                    $allowedLimit = $request->type === 'dealer' ? $party->allowed_order_limit : $party->individual_allowed_order_limit;
                    if ($qty > $allowedLimit) {
                        return response()->json([
                            'errors' => ["orders.$index.order_qty" => "The ordered quantity exceeds the allowed limit of {$allowedLimit} MT for {$party->name} ({$party->code})."]
                        ], 422);
                    }
                } elseif ($forType === 'dealer') {
                    $dealerId = $line['dealer_id'] ?? null;
                    if (!$dealerId) {
                        return response()->json(['errors' => ["orders.$index.dealer_id" => 'A dealer ID is required for this line item.']], 422);
                    }
                    $dealer = Dealer::find($dealerId);
                    if (!$dealer) {
                        return response()->json(['errors' => ["orders.$index.dealer" => 'The specified dealer does not exist.']], 422);
                    }
                    if ($qty > $dealer->allowed_order_limit) {
                        return response()->json([
                            'errors' => ["orders.$index.order_qty" => "The ordered quantity for dealer {$dealer->name} ({$dealer->code}) exceeds the allowed limit of {$dealer->allowed_order_limit} MT."]
                        ], 422);
                    }
                } else {
                    return response()->json(['errors' => ["orders.$index.for_type" => 'The line item type must be either Self or Dealer (Row ' . ($index + 1) . ').']], 422);
                }
            }

            // Create Order
            $order = Order::create([
                'order_number' => $request->order_number,
                'order_date' => $request->order_date,
                'type' => $request->type,
                'loading_charge' => $request->loading_charge ?? 0,
                'insurance_charge' => $request->insurance_charge ?? 0,
                'token_amount' => null,
                'created_by' => $request->created_by,
                'status' => 'pending',
                'remarks' => $request->type === 'dealer' ? ($orders[0]['remarks'] ?? null) : ($request->remarks ?? null),
                'placed_by_dealer_id' => $request->type === 'dealer' ? ($request->dealer_id ?? null) : null,
                'placed_by_distributor_id' => $request->type === 'distributor' ? $request->distributor_id : null,
                'terms_conditions' => $request->terms_conditions,
                'team_id' => $request->type === 'distributor' ? $request->team_id : null,
            ]);

            // Create Allocations
            foreach ($orders as $line) {
                $forType = $line['for_type'];
                $allocationData = [
                    'order_id' => $order->id,
                    'allocated_to_type' => $forType === 'self' ? $request->type : 'dealer',
                    'allocated_to_id' => $forType === 'self'
                        ? ($request->type === 'dealer' ? $request->dealer_id : $request->distributor_id)
                        : $line['dealer_id'],
                    'qty' => $line['order_qty'],
                    'remarks' => $line['remarks'],
                    'basic_price' => $line['basic_price'],
                    'agreed_basic_price' => $line['agreed_basic_price'],
                    'payment_terms' => $line['payment_term'],
                    'token_amount' => $line['token_amount'] ?? null,
                    'dispatched_qty' => 0,
                    'remaining_qty' => $line['order_qty'],
                    'status' => 'pending',
                ];

                OrderAllocation::create($allocationData);
            }

            // Handle Attachments
            $attachmentsToProcess = [];
            if ($request->has('temp_attachments')) {
                $attachmentsToProcess = array_map(function ($path) use ($request) {
                    return ['path' => $path, 'remarks' => $request->atch_remarks[array_search($path, $request->temp_attachments)] ?? null];
                }, $request->temp_attachments);
            }
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $index => $file) {
                    if ($file && $file->isValid()) {
                        $originalName = $file->getClientOriginalName();
                        $cleanName = time() . '_' . preg_replace('/\s+/', '_', strtolower($originalName));
                        $path = $file->storeAs('order_attachments', $cleanName, 'public');
                        $attachmentsToProcess[] = ['path' => $path, 'remarks' => $request->atch_remarks[$index] ?? null];
                    }
                }
            }

            foreach ($attachmentsToProcess as $attachment) {
                OrderAttachment::create([
                    'order_id' => $order->id,
                    'attachment' => $attachment['path'],
                    'remarks' => $attachment['remarks'],
                ]);
            }

            // Save Terms & Conditions
            if ($request->filled('terms_conditions')) {
                $order->update(['terms_conditions' => $request->terms_conditions]);
            }

            DB::commit();

            try {
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

                    if ($placer) {
                        \Illuminate\Support\Facades\Notification::send(
                            $superAdmins,
                            new \App\Notifications\NewOrderPlaced($order, $placer)
                        );
                    } else {
                        \Log::warning('No valid placer found for order notification.', [
                            'order_id' => $order->id,
                            'type' => $request->type,
                        ]);
                    }
                }
            } else {
                \Log::warning('Super Admin role not found when sending new order notification.');
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send new order notification: ' . $e->getMessage());
        }

            // Clear temp attachments
            session()->forget('temp_attachments');

            // SUCCESS: Show SweetAlert then redirect
            return response()->json([
                'success' => true,
                'message' => 'Order created successfully!',
                'redirect_url' => route('order_management')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $order = Order::with(['dealer', 'distributor', 'allocations', 'attachments'])->findOrFail($id);
        // dd($order);
        return view('orders.show', compact('order'));
    }

    public function getAllocations($id)
    {
        $order = Order::with(['allocations'])->findOrFail($id);
        $allocations = $order->allocations->map(function ($alloc) {
            $allocated_to_name = $alloc->allocated_to_type === 'dealer'
                ? (\App\Models\Dealer::find($alloc->allocated_to_id)->name ?? 'N/A')
                : (\App\Models\Distributor::find($alloc->allocated_to_id)->name ?? 'N/A');
            return [
                'allocated_to_type' => $alloc->allocated_to_type,
                'allocated_to_name' => $allocated_to_name,
                'qty' => $alloc->qty,
                'basic_price' => $alloc->basic_price,
                'agreed_basic_price' => $alloc->agreed_basic_price,
                'token_amount' => $alloc->token_amount,
                'payment_terms' => $alloc->payment_terms,
                'dispatched_qty' => $alloc->dispatched_qty,
                'remaining_qty' => $alloc->remaining_qty,
                'remarks' => $alloc->remarks,
            ];
        });

        return response()->json(['allocations' => $allocations]);
    }

    public function edit($id)
    {
        $order = Order::with(['allocations', 'attachments'])->findOrFail($id);
        $type = $order->type;

        if ($type === 'dealer') {
            $party = Dealer::find($order->placed_by_dealer_id);
            $assignedDealers = [];
        } else {
            $party = Distributor::find($order->placed_by_distributor_id);
            $assignedDealers = Dealer::where('distributor_id', $party->id)->get();
        }

        if (!$party) {
            return redirect()->route('order_management')->with('error', 'Party not found!');
        }

        $state_id = $party->state_id;
        $basicPrice = ItemBasicPrice::where('region', $state_id)
            ->where('status', 'Approved')
            ->first();

        if (!$basicPrice) {
            return redirect()->route('order_management')
                ->with('error', "No approved basic price found for the state: {$party->state->state}.");
        }

        $orderNumber = $order->order_number;

        return view('orders.edit', compact('order', 'basicPrice', 'type', 'party', 'assignedDealers', 'orderNumber'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $order = Order::findOrFail($id);
            $orders = $request->orders;

            // Update Order
            $order->update([
                'order_number' => $request->order_number,
                'order_date' => $request->order_date,
                'type' => $request->type,
                'loading_charge' => $request->loading_charge ?? 0,
                'insurance_charge' => $request->insurance_charge ?? 0,
                'created_by' => $request->created_by,
                'status' => 'pending',
                'remarks' => $request->type === 'dealer' ? ($orders[0]['remarks'] ?? null) : ($request->remarks ?? null),
                'placed_by_dealer_id' => $request->type === 'dealer' ? ($orders[0]['dealer_id'] ?? null) : null,
                'placed_by_distributor_id' => $request->type === 'distributor' ? $request->distributor_id : null,
                'terms_conditions' => $request->terms_conditions,
            ]);

            // Delete existing allocations and create new ones
            $order->allocations()->delete();
            foreach ($orders as $line) {
                $forType = $line['for_type'];
                $allocationData = [
                    'order_id' => $order->id,
                    'allocated_to_type' => $forType === 'self' ? $request->type : 'dealer',
                    'allocated_to_id' => $forType === 'self'
                        ? ($request->type === 'dealer' ? $request->dealer_id : $request->distributor_id)
                        : $line['dealer_id'],
                    'qty' => $line['order_qty'],
                    'remarks' => $line['remarks'],
                    'basic_price' => $line['basic_price'],
                    'agreed_basic_price' => $line['agreed_basic_price'],
                    'payment_terms' => $line['payment_term'],
                    'token_amount' => $line['token_amount'] ?? null,
                    'dispatched_qty' => 0,
                    'remaining_qty' => $line['order_qty'],
                    'status' => 'pending',
                ];

                OrderAllocation::create($allocationData);
            }

            // Handle Attachments
            // Delete marked attachments
            if ($request->has('delete_attachments')) {
                OrderAttachment::whereIn('id', $request->delete_attachments)->delete();
            }

            // Update existing attachments
            if ($request->has('existing_attachments')) {
                foreach ($request->existing_attachments as $index => $attachmentData) {
                    $attachment = OrderAttachment::find($attachmentData['id']);
                    if ($attachment) {
                        $attachment->remarks = $attachmentData['remarks'];
                        if ($request->hasFile("existing_attachments.{$index}.new_file")) {
                            $file = $request->file("existing_attachments.{$index}.new_file");
                            $originalName = $file->getClientOriginalName();
                            $cleanName = time() . '_' . preg_replace('/\s+/', '_', strtolower($originalName));
                            $path = $file->storeAs('order_attachments', $cleanName, 'public');
                            $attachment->attachment = $path;
                        }
                        $attachment->save();
                    }
                }
            }

            // Add new attachments
            if ($request->has('attachments')) {
                foreach ($request->attachments as $index => $attachmentData) {
                    if (isset($attachmentData['new_file']) && $attachmentData['new_file']) {
                        $file = $attachmentData['new_file'];
                        $originalName = $file->getClientOriginalName();
                        $cleanName = time() . '_' . preg_replace('/\s+/', '_', strtolower($originalName));
                        $path = $file->storeAs('order_attachments', $cleanName, 'public');
                        $order->attachments()->create([
                            'attachment' => $path,
                            'remarks' => $attachmentData['remarks'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'redirect_url' => route('order_management'),
                'message' => 'Order updated successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    public function generateOrderNumberFromDate(Request $request)
    {
        $date = $request->input('order_date');

        if (!$date) {
            return response()->json(['error' => 'Date is required'], 400);
        }

        try {
            $carbonDate = Carbon::parse($date);

            // Determine financial year (starts April 1st)
            $year = $carbonDate->month >= 4 ? $carbonDate->year : $carbonDate->year - 1;
            $nextYear = substr($year + 1, -2); // Get last two digits of next year
            $financialYear = "{$year}-{$nextYear}";

            // Define financial year start and end dates
            $yearStart = Carbon::create($year, 4, 1)->startOfDay();
            $yearEnd = Carbon::create($year + 1, 3, 31)->endOfDay();

            // Find the last order in the current financial year
            $lastOrder = Order::whereBetween('order_date', [$yearStart, $yearEnd])
                ->orderBy('id', 'desc')
                ->first();

            if ($lastOrder) {
                // Extract counter from last order_number
                $lastCounter = (int) substr($lastOrder->order_number, -4);
                $counter = $lastCounter + 1;
            } else {
                $counter = 1;
            }

            $counterPadded = str_pad($counter, 4, '0', STR_PAD_LEFT);

            $orderNumber = "ODR_{$financialYear}_{$counterPadded}";

            return response()->json(['order_number' => $orderNumber]);
        } catch (\Exception $e) {
            \Log::error('Error generating order number: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid date format or failed to generate order number'], 400);
        }
    }

    public function approve($id)
    {
        $order = Order::with('allocations')->findOrFail($id);

        // 1. Prevent double approval
        if ($order->status === 'approved') {
            return back()->with('error', 'Order is already approved.');
        }

        // 2. Validate allocations
        if ($order->allocations->isEmpty()) {
            return back()->with('error', 'No allocations found for this order.');
        }

        // 3. Calculate total order quantity for distributor's allowed_order_limit check
        $totalOrderQty = $order->allocations->sum('qty');

        // 4. Check allowed order limits for each allocation
        $errors = [];
        if ($order->type === 'dealer') {
            // For dealer orders, check the dealer's allowed_order_limit
            $dealer = Dealer::find($order->placed_by_dealer_id);
            if (!$dealer) {
                $errors[] = "Dealer with ID {$order->placed_by_dealer_id} not found.";
            } elseif ($dealer->allowed_order_limit < $totalOrderQty) {
                $errors[] = "Dealer '{$dealer->name}' (ID: {$dealer->id}) has insufficient allowed order limit: "
                    . "{$dealer->allowed_order_limit} MT available, but {$totalOrderQty} MT requested.";
            }
        } else {
            // For distributor orders
            $distributor = Distributor::find($order->placed_by_distributor_id);
            if (!$distributor) {
                $errors[] = "Distributor with ID {$order->placed_by_distributor_id} not found.";
            } elseif ($distributor->allowed_order_limit < $totalOrderQty) {
                $errors[] = "Distributor '{$distributor->name}' (ID: {$distributor->id}) has insufficient total allowed order limit: "
                    . "{$distributor->allowed_order_limit} MT available, but {$totalOrderQty} MT requested.";
            }

            // Check individual limits for each allocation only if distributor exists
            if ($distributor) {
                foreach ($order->allocations as $allocation) {
                    if ($allocation->allocated_to_type === 'self' || $allocation->allocated_to_type === 'distributor') {
                        if ($distributor->individual_allowed_order_limit < $allocation->qty) {
                            $errors[] = "Distributor '{$distributor->name}' (ID: {$distributor->id}) has insufficient individual allowed order limit: "
                                . "{$distributor->individual_allowed_order_limit} MT available, but {$allocation->qty} MT requested for self allocation.";
                        }
                    } elseif ($allocation->allocated_to_type === 'dealer') {
                        $dealer = Dealer::find($allocation->allocated_to_id);
                        if (!$dealer) {
                            $errors[] = "Dealer with ID {$allocation->allocated_to_id} not found.";
                        } elseif ($dealer->allowed_order_limit < $allocation->qty) {
                            $errors[] = "Dealer '{$dealer->name}' (ID: {$dealer->id}) has insufficient allowed order limit: "
                                . "{$dealer->allowed_order_limit} MT available, but {$allocation->qty} MT requested.";
                        }
                    } else {
                        $errors[] = "Invalid allocation type '{$allocation->allocated_to_type}' for allocation ID {$allocation->id}.";
                    }
                }
            }
        }

        // 5. If there are errors, return them
        if (!empty($errors)) {
            return back()->with('error', implode(' ', $errors));
        }

        DB::beginTransaction();

        try {
            // 6. Deduct order_qty from allowed limits
            if ($order->type === 'dealer') {
                // Update dealer's allowed_order_limit
                $dealer = Dealer::find($order->placed_by_dealer_id);
                $dealer->allowed_order_limit -= $totalOrderQty;
                $dealer->save();

                $assignedDistributorId = $dealer->distributor_id;
                if ($assignedDistributorId) {
                    $assignedDistributor = Distributor::find($assignedDistributorId);
                    $assignedDistributor->allowed_order_limit -= $totalOrderQty;
                    $assignedDistributor->save();
                }
            } else {
                // Update distributor's allowed_order_limit (total) and individual limits
                $distributor = Distributor::find($order->placed_by_distributor_id);
                if ($distributor) {
                    $distributor->allowed_order_limit -= $totalOrderQty;
                    $distributor->save();

                    foreach ($order->allocations as $allocation) {
                        if ($allocation->allocated_to_type === 'self' || $allocation->allocated_to_type === 'distributor') {
                            $distributor->individual_allowed_order_limit -= $allocation->qty;
                            $distributor->save();
                        } elseif ($allocation->allocated_to_type === 'dealer') {
                            $dealer = Dealer::find($allocation->allocated_to_id);
                            $dealer->allowed_order_limit -= $allocation->qty;
                            $dealer->save();
                        }
                    }
                }
            }

            // 7. Update allocation statuses to approved
            foreach ($order->allocations as $allocation) {
                $allocation->status = 'approved';
                $allocation->save();
            }

            // 8. Approve the order
            $order->status = 'approved';
            $order->approval_time = now();
            $order->save();

            DB::commit();

            try {
                // Find the placer in AppUserManagement (either dealer or distributor)
                if ($order->type === 'dealer') {
                    $placer = \App\Models\Dealer::find($order->placed_by_dealer_id);
                    $appUser = AppUserManagement::where('code', $placer->code)
                        ->where('type', 'dealer')
                        ->first();
                } else {
                    $placer = \App\Models\Distributor::find($order->placed_by_distributor_id);
                    $appUser = AppUserManagement::where('code', $placer->code)
                        ->where('type', 'distributor')
                        ->first();
                }

                // Optional: find a linked web user if needed (like your dealer approval example)
                $webUser = null;
                if ($placer) {
                    $webUser = \App\Models\User::where('name', 'LIKE', "%{$placer->name}%")
                        ->orWhere('email', $placer->email)
                        ->first();
                }

                $notifiables = collect();

                if ($appUser) {
                    $notifiables->push($appUser);
                }

                if ($webUser) {
                    $notifiables->push($webUser);
                }

                if ($notifiables->isNotEmpty()) {
                    Notification::send($notifiables, new OrderApproved($order));
                } else {
                    \Log::warning("No notifiable user found for order #{$order->order_number}");
                }

            } catch (\Exception $e) {
                \Log::error("Failed to send order approval notification: " . $e->getMessage());
            }

            return redirect()->back()->with('success', 'Order approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Approval failed: ' . $e->getMessage());
        }
    }

    public function items()
    {
        return $this->hasMany(OrderAllocation::class);
    }

    public function reject($id)
    {
        $order = Order::findOrFail($id);

        // Check if the order status is pending
        if ($order->status !== 'pending') {
            return redirect()->route('order_management')->with('error', 'Only pending orders can be deleted.');
        }

        // Soft delete the order by updating status to 'deleted'
        $order->status = 'rejected';
        $order->deleted_by = Auth::user()->name . " " . Auth::user()->last_name;
        $order->save();

        $this->notifyPlacer($order, 'rejected');

        return redirect()->route('order_management')->with('success', 'Order rejected successfully.');
    }

    public function delete($id){
        $order = Order::findOrFail($id);

        $order->delete();

        $this->notifyPlacer($order, 'deleted');
        return redirect()->route('order_management')->with('success', 'Order deleted successfully.');
    }

    public function changeStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:closed with condition',
            'status_change_remarks' => 'nullable|string|max:1000',
        ]);

        $order = Order::with('allocations')->findOrFail($id);

        // Prevent changing status if already closed
        if ($order->status === 'closed with condition') {
            return redirect()->back()->with('error', 'This order is already closed with condition.');
        }

        DB::beginTransaction();

        try {
            // Initialize updates for dealers and distributors
            $dealerUpdates = []; // dealer_id => total qty to add to allowed_order_limit
            $distributorUpdates = []; // distributor_id => total qty to add to allowed_order_limit
            $distributorIndividualUpdates = []; // distributor_id => total qty to add to individual_allowed_order_limit

            foreach ($order->allocations as $allocation) {
                // Use remaining_qty to avoid over-crediting
                $effective_qty = $allocation->remaining_qty;

                // Update allocation status
                $allocation->status = 'closed with condition';
                $allocation->remaining_qty = 0;
                $allocation->dispatched_qty = $allocation->qty;
                $allocation->save();

                // Handle order limit updates based on allocation
                if ($order->type === 'distributor') {
                    $distributor_id = $order->placed_by_distributor_id;

                    if ($allocation->allocated_to_type === 'distributor') {
                        // Self allocation: increase individual and allowed for distributor
                        $distributorIndividualUpdates[$distributor_id] = ($distributorIndividualUpdates[$distributor_id] ?? 0) + $effective_qty;
                        $distributorUpdates[$distributor_id] = ($distributorUpdates[$distributor_id] ?? 0) + $effective_qty;
                    } elseif ($allocation->allocated_to_type === 'dealer') {
                        // Dealer allocation: increase dealer + distributor allowed
                        $dealer_id = $allocation->allocated_to_id;
                        $dealerUpdates[$dealer_id] = ($dealerUpdates[$dealer_id] ?? 0) + $effective_qty;
                        $distributorUpdates[$distributor_id] = ($distributorUpdates[$distributor_id] ?? 0) + $effective_qty;
                    }
                } elseif ($order->type === 'dealer') {
                    // Dealer order: increase dealer allowed
                    $dealer_id = $order->placed_by_dealer_id;
                    $dealerUpdates[$dealer_id] = ($dealerUpdates[$dealer_id] ?? 0) + $effective_qty;

                    // If dealer has assigned distributor, increase their limit too
                    $dealer = Dealer::find($dealer_id);
                    if ($dealer && $dealer->assigned_distributor_id) {
                        $dist_id = $dealer->assigned_distributor_id;
                        $distributorUpdates[$dist_id] = ($distributorUpdates[$dist_id] ?? 0) + $effective_qty;
                        Log::info("Updating distributor ID {$dist_id} with qty {$effective_qty} for dealer order ID {$order->id}");
                    } else {
                        Log::warning("No assigned distributor for dealer ID {$dealer_id} in order ID {$order->id}");
                    }
                }
            }

            // Apply aggregated updates to dealers
            foreach ($dealerUpdates as $dealerId => $qty) {
                $dealer = Dealer::findOrFail($dealerId);
                $dealer->allowed_order_limit += $qty;
                $dealer->save();
                Log::info("Updated dealer ID {$dealerId} allowed_order_limit by {$qty}");
            }

            // Apply aggregated updates to distributors
            foreach ($distributorUpdates as $distributorId => $qty) {
                $distributor = Distributor::findOrFail($distributorId);
                $distributor->allowed_order_limit += $qty;

                $individualQty = $distributorIndividualUpdates[$distributorId] ?? 0;
                $distributor->individual_allowed_order_limit += $individualQty;

                $distributor->save();
                Log::info("Updated distributor ID {$distributorId} allowed_order_limit by {$qty}, individual_allowed_order_limit by {$individualQty}");
            }

            // === UPDATE ORDER WITH TRACKING ===
            $user = Auth::user();
            $fullName = trim(($user->name ?? '') . ' ' . ($user->last_name ?? ''));

            $order->status = $request->status;
            $order->status_changed_by = $fullName;           // First Last
            $order->status_changed_at = now();
            $order->status_change_remarks = $request->status_change_remarks;
            $order->save();

            DB::commit();

            $this->notifyPlacer($order, 'closed with condition', $request->status_change_remarks);

            return redirect()->route('order_management')
                ->with('success', 'Order status changed to "Closed with Condition" successfully. Limits updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Order status change failed for ID {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to change status: ' . $e->getMessage());
        }
    }

    public function downloadOrderPDF($id)
    {
        // Eager load all relationships at once and find the order.
        // findOrFail will automatically throw an error if not found, so no need for a manual check.
        $order = Order::with(['distributor', 'dealer', 'allocations'])->findOrFail($id);
        $company_settings = CompanySetting::firstOrCreate([]);

        // --- Image Preparation ---
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

        // --- Calculation ---
        $subtotal = $order->allocations->sum(function ($allocation) {
            return $allocation->qty * $allocation->agreed_basic_price;
        });
        $grandTotal = $order->allocations->sum(function ($allocation) use ($order) {
            // Har allocation ke agreed price mein order ke charges jodo
            $pricePerUnit = $allocation->agreed_basic_price + $order->loading_charge + $order->insurance_charge;

            // Phir quantity se multiply karke line total nikalo
            return $allocation->qty * $pricePerUnit;
        });

        // Amount in words ab naye grand total se banega
        $amountInWords = NumberHelper::amountInWords($grandTotal);

        // --- Data for the View ---
        // Combine ALL data into a single array. This is the main fix.
        $data = [
            'order' => $order,
            'logoBase64' => $logoBase64,
            'sealBase64' => $sealBase64,
            'amountInWords' => $amountInWords,
            'company_settings' => $company_settings,
            'grandTotal' => $grandTotal,
        ];

        // --- PDF Generation ---
        // Pass the single $data array to the view.
        $pdf = Pdf::loadView('pdf.order', $data)
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isRemoteEnabled' => true,
            ])
            ->setPaper('a4', 'portrait');

        return $pdf->stream('order-' . $order->order_number . '.pdf');
    }
    // Changes by md raza end

    public function notifyPlacer($order, $status, $remarks = null)
    {
        if ($order->type === 'dealer') {
            $placer = Dealer::find($order->placed_by_dealer_id);
            $appUser = $placer ? AppUserManagement::where('code', $placer->code)
                ->where('type', 'dealer')->first() : null;
        } else {
            $placer = Distributor::find($order->placed_by_distributor_id);
            $appUser = $placer ? AppUserManagement::where('code', $placer->code)
                ->where('type', 'distributor')->first() : null;
        }

        $webUser = null;
        if ($placer) {
            $webUser = User::where('name', 'LIKE', "%{$placer->name}%")
                ->orWhere('email', $placer->email)
                ->first();
        }

        $notifiables = collect();
        if ($appUser) $notifiables->push($appUser);
        if ($webUser) $notifiables->push($webUser);

        if ($notifiables->isNotEmpty()) {
            Notification::send($notifiables, new OrderStatusChanged($order, $status, $remarks));
        } else {
            Log::warning("No notifiable user found for order #{$order->order_number}");
        }
    }

}
