<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderAllocation;
use App\Models\OrderAttachment;
use App\Models\Dealer;
use App\Models\Distributor;
use App\Models\ItemBasicPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use App\Http\Resources\OrderManagementResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\OrderResource;
use App\Models\DistributorTeam;
use App\Models\LoadingCharge;
use App\Models\InsuranceCharge;
use App\Models\GstRate;
use Exception;

class OrderManagementController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index(Request $request)
    {
        // --- Step 1: Get the authenticated user ---
        $appUser = $request->user();

        // Agar user logged in nahi hai toh 401 error do
        if (!$appUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $userType = $appUser->type;
        $userCode = $appUser->code;

        // --- Step 2: Start the query ---
        $query = Order::query();

        // ===================================================================
        // User ke role ke hisaab se orders filter karo
        // ===================================================================
        if ($userType === 'dealer') {
            // 'placedByDealer' relationship check karega
            $query->whereHas('placedByDealer', function ($q) use ($userCode) {
                $q->where('code', $userCode); // Dealer model par 'code' check karo
            });
        } elseif ($userType === 'distributor') {
            // 'placedByDistributor' relationship check karega
            $query->whereHas('placedByDistributor', function ($q) use ($userCode) {
                $q->where('code', $userCode); // Distributor model par 'code' check karo
            });
        } else {
            // Agar user ka type na 'dealer' hai na 'distributor' (ya null hai),
            // toh koi order na dikhe.
            $query->whereRaw('1 = 0'); // Koi result return nahi karega
        }
        // ===================================================================

        // --- Step 3: Relationships ko load karo (Eager Loading) ---
        $query->with(['placedByDealer', 'placedByDistributor', 'allocations']);

        // --- Step 4: Saare Optional Filters apply karo ---

        // 1. Filter by Status (Optional)
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // 2. Filter by Search (Optional) - Yeh 'order_number' par search karega
        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->input('search') . '%');
        }

        // 3. Filter by Date Range (Optional)
        if ($request->filled('start_date')) {
            $query->whereDate('order_date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('order_date', '<=', $request->input('end_date'));
        }

        // --- Step 5: Default sorting ---
        // (Latest wala first)
        $query->orderBy('order_date', 'desc')->orderBy('id', 'desc');

        // ===================================================================
        // --- Step 6: Pagination Logic (Aapke developer ke hisaab se) ---
        // ===================================================================

        // 'per_page' optional hai. Default 20 rakha hai.
        // Agar request ?per_page=10 aayega, toh 10 use hoga.
        // Agar ?per_page= nahi aayega, toh 20 use hoga.
        $perPage = $request->input('per_page', 10);

        // 'page' parameter Laravel paginate() function khud handle kar lega.
        // Agar ?page=2 aayega, toh page 2 dega.
        // Agar ?page= nahi aayega, toh default page 1 dega.
        $paginator = $query->paginate($perPage);

        // ===================================================================
        // --- Step 6.5: NAYA STEP - GST Fetch karein aur Allocations mein add karein ---
        // ===================================================================

        // 1. Database se GST rate ek baar fetch kar lo
        // (Yeh aapke pichhle code se liya hai)
        $gstRate = GstRate::first()->rate ?? 18;

        // 2. Paginator se 'Order' items ka array nikalo
        $orders = $paginator->items();

        // 3. Har order ke har allocation mein GST rate ko inject kar do
        foreach ($orders as $order) {
            foreach ($order->allocations as $allocation) {
                // Hum allocation object mein ek nayi property 'gst_rate' add kar rahe hain
                $allocation->gst_rate = $gstRate;
            }
        }

        // ===================================================================
        // --- Step 7: Custom JSON Response (Aapke developer ke format mein) ---
        // ===================================================================
        return response()->json(
            [
                'status' => true,
                // 'data' => $paginator->items(),
                'data' => $orders,
                'current_page' => $paginator->currentPage(),
                'per_page' => (int) $paginator->perPage(), // (int) se number ban jaayega
                'total' => $paginator->total(),
                'total_pages' => $paginator->lastPage(),
            ],
            200,
        );
    }

    public function destroy(Request $request, Order $order)
    {
        try {
            // ✅ 1. Check if order is NOT pending
            if ($order->status !== 'pending') {
                return response()->json([
                    'status' => false,
                    'message' => 'Only pending orders can be deleted.'
                ], 403); // 403 = Forbidden
            }

            // ✅ 2. Safe delete (soft delete if model has SoftDeletes)
            $order->delete();

            return response()->json([
                'status' => true,
                'message' => 'Order deleted successfully.'
            ], 200);
        } catch (Exception $e) {

            Log::error('Order deletion failed for order ID ' . $order->id . ': ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while trying to delete the order.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // public function store(Request $request)
    // {
    //     try {
    //         // ===================================================================
    //         // !! NAYA LOGIC YAHAN ADD KAREIN !!
    //         // --- 0. GET AUTHENTICATED USER & PREPARE REQUEST ---
    //         // ===================================================================

    //         // Step 0.1: Get the authenticated app user
    //         $appUser = $request->user();
    //         if (!$appUser) {
    //             return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
    //         }

    //         $userType = $appUser->type;
    //         $userCode = $appUser->code;
    //         $placer = null; // This will hold the Dealer or Distributor model

    //         // Step 0.2: Find the matching Dealer or Distributor profile
    //         if ($userType === 'dealer') {
    //             $placer = Dealer::where('code', $userCode)->first();
    //         } elseif ($userType === 'distributor') {
    //             $placer = Distributor::where('code', $userCode)->first();
    //         }

    //         // Step 0.3: Handle if profile not found
    //         if (!$placer) {
    //             return response()->json(['status' => false, 'message' => 'Authenticated user profile (Dealer/Distributor) not found.'], 404);
    //         }

    //         // Step 0.4: Inject user's data into the request object
    //         // Saare existing logic ab in values ka istemaal karenge
    //         $request->merge([
    //             'type' => $userType,
    //             'dealer_id' => $userType === 'dealer' ? $placer->id : null,
    //             'distributor_id' => $userType === 'distributor' ? $placer->id : null,
    //             'created_by' => $placer->name, // Automatically set created_by
    //         ]);

    //         // --- 1. VALIDATION ---
    //         $validated = $request->validate([
    //             'type' => 'required|in:dealer,distributor',
    //             'order_number' => 'nullable|string|unique:orders,order_number',
    //             'order_date' => 'required|date',

    //             // !! CHANGE YAHAN HAI !!
    //             // 'nullable' add kiya gaya hai
    //             'dealer_id' => 'nullable|required_if:type,dealer|exists:dealers,id',
    //             'distributor_id' => 'nullable|required_if:type,distributor|exists:distributors,id',

    //             'payment_term' => 'required|string',
    //             'loading_charge' => 'nullable|numeric',
    //             'insurance_charge' => 'nullable|numeric',
    //             'created_by' => 'required|string',
    //             'remarks' => 'nullable|string',
    //             'terms_conditions' => 'nullable|string',
    //             'allocations' => 'required|array|min:1',
    //             // ... baaki rules waise hi rahenge ...
    //             'allocations.*.for_type' => 'required|in:self,dealer',
    //             'allocations.*.qty' => 'required|numeric|min:0.1',
    //             'allocations.*.agreed_basic_price' => 'required|numeric|min:0',
    //             'allocations.*.basic_price' => 'required|numeric|min:0',
    //             'allocations.*.token_amount' => 'nullable|numeric|min:0',
    //             'allocations.*.dealer_id' => 'nullable|required_if:allocations.*.for_type,dealer|exists:dealers,id',
    //             'allocations.*.remarks' => 'nullable|string',
    //             'attachments' => 'nullable|array',
    //             'attachments.*' => 'nullable|file|max:2048',
    //             'atch_remarks' => 'nullable|array',
    //             'atch_remarks.*' => 'nullable|string',
    //         ]);

    //         // --- 2. GENERATE ORDER NUMBER IF NOT PROVIDED ---
    //         if (empty($validated['order_number'])) {
    //             $carbonDate = Carbon::parse($validated['order_date']);
    //             $validated['order_number'] = Order::generateOrderNumber($carbonDate);
    //         }

    //         // --- 3. MANUAL BUSINESS LOGIC VALIDATION ---

    //         // NAYA CHECK ADD KIYA GAYA HAI: Verify that dealers in allocations belong to the ordering distributor
    //         if ($validated['type'] === 'distributor') {
    //             foreach ($validated['allocations'] as $index => $allocation) {
    //                 // This check only runs if the allocation line is for a specific dealer
    //                 if ($allocation['for_type'] === 'dealer') {
    //                     $dealerId = $allocation['dealer_id'];
    //                     $distributorId = $validated['distributor_id'];

    //                     // Find the dealer from the database
    //                     $dealer = Dealer::find($dealerId);

    //                     // If the dealer doesn't exist or their distributor_id does not match, throw an error.
    //                     if (!$dealer || $dealer->distributor_id != $distributorId) {
    //                         throw ValidationException::withMessages([
    //                             "allocations.{$index}.dealer_id" => 'The selected dealer is not associated with this distributor.',
    //                         ]);
    //                     }
    //                 }
    //             }
    //         }

    //         // Check 3.2: NAYA CHECK - Verify Allowed Order Limit
    //         foreach ($validated['allocations'] as $index => $line) {
    //             $qty = (float) $line['qty'];
    //             $entity = null;
    //             $limit = 0;

    //             // Step 1: Pata lagao ki order kiske liye hai (entity kaun hai)
    //             if ($line['for_type'] === 'self') {
    //                 if ($request->type === 'distributor') {
    //                     $entity = Distributor::find($request->distributor_id);
    //                     // Distributor ke 'self' order ke liye 'individual_allowed_order_limit' use hoga
    //                     $limit = (float) $entity->allowed_order_limit;
    //                 } else {
    //                     // type is 'dealer'
    //                     $entity = Dealer::find($request->dealer_id);
    //                     $limit = (float) $entity->allowed_order_limit;
    //                 }
    //             } else {
    //                 // for_type is 'dealer'
    //                 $entity = Dealer::find($line['dealer_id']);
    //                 $limit = (float) $entity->allowed_order_limit;
    //             }

    //             // Step 2: Check karo
    //             if (!$entity) {
    //                 throw ValidationException::withMessages(["allocations.{$index}.dealer_id" => 'The selected party was not found.']);
    //             }

    //             if ($qty > $limit) {
    //                 throw ValidationException::withMessages([
    //                     "allocations.{$index}.qty" => "Order quantity ({$qty} MT) exceeds the allowed limit ({$limit} MT) for {$entity->name}.",
    //                 ]);
    //             }
    //         }

    //         // (You can add your order limit validation here as well)

    //         DB::beginTransaction();

    //         // --- 4. PREPARE AND CREATE ORDER ---
    //         $orderData = [
    //             'order_number' => $validated['order_number'],
    //             'order_date' => $validated['order_date'],
    //             'type' => $validated['type'],
    //             'payment_term' => $validated['payment_term'],
    //             'loading_charge' => $validated['loading_charge'] ?? 0,
    //             'insurance_charge' => $validated['insurance_charge'] ?? 0,
    //             'created_by' => $validated['created_by'],
    //             'status' => 'pending',
    //             'remarks' => $validated['remarks'] ?? null,
    //             'terms_conditions' => $validated['terms_conditions'] ?? null,
    //         ];

    //         // This logic correctly sets the placer's ID
    //         if ($validated['type'] === 'dealer') {
    //             $orderData['placed_by_dealer_id'] = $validated['dealer_id'];
    //             $orderData['placed_by_distributor_id'] = null;
    //         } else {
    //             // type is 'distributor'
    //             $orderData['placed_by_distributor_id'] = $validated['distributor_id'];
    //             $orderData['placed_by_dealer_id'] = null;
    //         }

    //         $order = Order::create($orderData);

    //         // --- 5. CREATE ALLOCATIONS ---
    //         foreach ($validated['allocations'] as $line) {
    //             $order->allocations()->create([
    //                 'allocated_to_type' => $line['for_type'] === 'self' ? $request->type : 'dealer',
    //                 'allocated_to_id' => $line['for_type'] === 'self' ? ($request->type === 'dealer' ? $request->dealer_id : $request->distributor_id) : $line['dealer_id'],
    //                 'qty' => $line['qty'],
    //                 'remarks' => $line['remarks'] ?? null,
    //                 'basic_price' => $line['basic_price'],
    //                 'agreed_basic_price' => $line['agreed_basic_price'],
    //                 'payment_terms' => $order->payment_term,
    //                 'token_amount' => $line['token_amount'] ?? null,
    //                 'dispatched_qty' => 0,
    //                 'remaining_qty' => $line['qty'],
    //                 'status' => 'pending',
    //             ]);
    //         }

    //         // --- 6. HANDLE ATTACHMENTS ---
    //         if ($request->hasFile('attachments')) {
    //             foreach ($request->file('attachments') as $index => $file) {
    //                 if ($file && $file->isValid()) {
    //                     // Store the file directly in the public disk under 'order_attachments'
    //                     $path = $file->store('order_attachments', 'public');

    //                     // Create a record in the database for the attachment
    //                     $order->attachments()->create([
    //                         'attachment' => $path,
    //                         'remarks' => $request->atch_remarks[$index] ?? null,
    //                     ]);
    //                 }
    //             }
    //         }

    //         DB::commit();

    //         return response()->json(
    //             [
    //                 'status' => true,
    //                 'message' => 'Order created successfully!',
    //                 'data' => $order->load(['allocations', 'attachments']),
    //             ],
    //             201,
    //         );
    //     } catch (ValidationException $e) {
    //         DB::rollBack();
    //         return response()->json(['status' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('API Order store error: ' . $e->getMessage());
    //         return response()->json(['status' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500);
    //     }
    // }

    // public function store(Request $request)
    // {
    //     try {
    //         // --- 0. GET AUTHENTICATED USER & PREPARE REQUEST ---
    //         $appUser = $request->user();
    //         if (!$appUser) {
    //             return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
    //         }

    //         $userType = $appUser->type;
    //         $userCode = $appUser->code;
    //         $placer = null;

    //         if ($userType === 'dealer') {
    //             $placer = Dealer::where('code', $userCode)->first();
    //         } elseif ($userType === 'distributor') {
    //             $placer = Distributor::where('code', $userCode)->first();
    //         }

    //         if (!$placer) {
    //             return response()->json(['status' => false, 'message' => 'Authenticated user profile (Dealer/Distributor) not found.'], 404);
    //         }

    //         $request->merge([
    //             'type' => $userType,
    //             'dealer_id' => $userType === 'dealer' ? $placer->id : null,
    //             'distributor_id' => $userType === 'distributor' ? $placer->id : null,
    //             'created_by' => $placer->name,
    //         ]);

    //         // --- 1. VALIDATION ---
    //         $validated = $request->validate([
    //             'type' => 'required|in:dealer,distributor',
    //             'order_number' => 'nullable|string|unique:orders,order_number',
    //             'order_date' => 'required|date',
    //             'dealer_id' => 'nullable|required_if:type,dealer|exists:dealers,id',
    //             'distributor_id' => 'nullable|required_if:type,distributor|exists:distributors,id',
    //             // 'payment_term' => 'nullable|string',
    //             'loading_charge' => 'nullable|numeric',
    //             'insurance_charge' => 'nullable|numeric',
    //             'created_by' => 'required|string',
    //             'remarks' => 'nullable|string',
    //             'terms_conditions' => 'nullable|string',
    //             'allocations' => 'required|array|min:1',
    //             'allocations.*.for_type' => 'required|in:self,dealer',
    //             'allocations.*.qty' => 'required|numeric|min:0.1',
    //             'allocations.*.agreed_basic_price' => 'required|numeric|min:0',
    //             'allocations.*.basic_price' => 'required|numeric|min:0',
    //             'allocations.*.token_amount' => 'nullable|numeric|min:0',
    //             'allocations.*.payment_term' => 'required|string',
    //             'allocations.*.dealer_id' => 'nullable|required_if:allocations.*.for_type,dealer|exists:dealers,id',
    //             'allocations.*.remarks' => 'nullable|string',
    //             'attachments' => 'nullable|array',
    //             'attachments.*' => 'nullable|file|max:2048',
    //             'atch_remarks' => 'nullable|array',
    //             'atch_remarks.*' => 'nullable|string',
    //         ]);

    //         // --- 2. GENERATE ORDER NUMBER IF NOT PROVIDED ---
    //         if (empty($validated['order_number'])) {
    //             $carbonDate = Carbon::parse($validated['order_date']);
    //             $validated['order_number'] = Order::generateOrderNumber($carbonDate);
    //         }

    //         // ===================================================================
    //         // --- 3. MANUAL BUSINESS LOGIC VALIDATION ---
    //         // ===================================================================

    //         // CASE 1: Agar order 'DEALER' daal raha hai
    //         if ($validated['type'] === 'dealer') {
    //             foreach ($validated['allocations'] as $index => $allocation) {
    //                 // Ek dealer sirf 'self' order daal sakta hai
    //                 if ($allocation['for_type'] !== 'self') {
    //                     throw ValidationException::withMessages([
    //                         "allocations.{$index}.for_type" => 'As a dealer, you can only place orders for yourself (for_type must be "self").',
    //                     ]);
    //                 }
    //             }
    //         }
    //         // CASE 2: Agar order 'DISTRIBUTOR' daal raha hai
    //         elseif ($validated['type'] === 'distributor') {

    //             // Pehle hi check karlo ki distributor ka team hai ya nahi
    //             $distributorId = $validated['distributor_id'];
    //             $team = DistributorTeam::where('distributor_id', $distributorId)->first();

    //             foreach ($validated['allocations'] as $index => $allocation) {

    //                 // Yeh check sirf tabhi run hoga jab allocation line 'dealer' ke liye hai
    //                 if ($allocation['for_type'] === 'dealer') {

    //                     $dealerId = $allocation['dealer_id'];

    //                     // 1. Check karo ki distributor ka team hai bhi ya nahi
    //                     if (!$team) {
    //                         throw ValidationException::withMessages([
    //                             "allocations.{$index}.dealer_id" => 'You do not have a team configured to place orders for dealers.',
    //                         ]);
    //                     }

    //                     // 2. Agar team hai, toh check karo ki yeh dealer uss team mein hai ya nahi
    //                     $isDealerInTeam = $team->dealers()->where('dealers.id', $dealerId)->exists();

    //                     if (!$isDealerInTeam) {
    //                         throw ValidationException::withMessages([
    //                             "allocations.{$index}.dealer_id" => "The selected dealer (ID: {$dealerId}) is not a member of your team.",
    //                         ]);
    //                     }
    //                 }
    //                 // Agar 'for_type' === 'self', toh kuch check nahi karna hai (team validation),
    //                 // order limit check aage hoga.
    //             }
    //         }

    //         // Check 3.2: Verify Allowed Order Limit (Yeh dono cases ke liye chalega)
    //         foreach ($validated['allocations'] as $index => $line) {
    //             $qty = (float) $line['qty'];
    //             $entity = null;
    //             $limit = 0;

    //             // Step 1: Pata lagao ki order kiske liye hai (entity kaun hai)
    //             if ($line['for_type'] === 'self') {
    //                 if ($request->type === 'distributor') {
    //                     $entity = Distributor::find($request->distributor_id);
    //                     $limit = (float) $entity->allowed_order_limit;
    //                 } else { // type is 'dealer'
    //                     $entity = Dealer::find($request->dealer_id);
    //                     $limit = (float) $entity->allowed_order_limit;
    //                 }
    //             } else { // for_type is 'dealer'
    //                 $entity = Dealer::find($line['dealer_id']);
    //                 $limit = (float) $entity->allowed_order_limit;
    //             }

    //             if (!$entity) {
    //                 throw ValidationException::withMessages(["allocations.{$index}.dealer_id" => 'The selected party was not found.']);
    //             }

    //             if ($qty > $limit) {
    //                 throw ValidationException::withMessages([
    //                     "allocations.{$index}.qty" => "Order quantity ({$qty} MT) exceeds the allowed limit ({$limit} MT) for {$entity->name}.",
    //                 ]);
    //             }
    //         }
    //         // --- Manual Validation End ---


    //         // --- DB Transaction Start ---
    //         DB::beginTransaction();

    //         // --- 4. PREPARE AND CREATE ORDER ---
    //         $orderData = [
    //             'order_number' => $validated['order_number'],
    //             'order_date' => $validated['order_date'],
    //             'type' => $validated['type'],
    //             // 'payment_term' => $validated['payment_term'],
    //             'loading_charge' => $validated['loading_charge'] ?? 0,
    //             'insurance_charge' => $validated['insurance_charge'] ?? 0,
    //             'created_by' => $validated['created_by'],
    //             'status' => 'pending',
    //             'remarks' => $validated['remarks'] ?? null,
    //             'terms_conditions' => $validated['terms_conditions'] ?? null,
    //         ];

    //         if ($validated['type'] === 'dealer') {
    //             $orderData['placed_by_dealer_id'] = $validated['dealer_id'];
    //             $orderData['placed_by_distributor_id'] = null;
    //         } else {
    //             $orderData['placed_by_distributor_id'] = $validated['distributor_id'];
    //             $orderData['placed_by_dealer_id'] = null;
    //         }

    //         $order = Order::create($orderData);

    //         // --- 5. CREATE ALLOCATIONS ---
    //         foreach ($validated['allocations'] as $line) {
    //             $order->allocations()->create([
    //                 // Logic ab 'self' aur 'dealer' dono ko handle karta hai
    //                 'allocated_to_type' => $line['for_type'] === 'self' ? $request->type : 'dealer',
    //                 'allocated_to_id' => $line['for_type'] === 'self' ? ($request->type === 'dealer' ? $request->dealer_id : $request->distributor_id) : $line['dealer_id'],
    //                 'qty' => $line['qty'],
    //                 'remarks' => $line['remarks'] ?? null,
    //                 'basic_price' => $line['basic_price'],
    //                 'agreed_basic_price' => $line['agreed_basic_price'],
    //                 // 'payment_terms' => $order->payment_term,
    //                 'payment_terms' => $line['payment_term'],
    //                 'token_amount' => $line['token_amount'] ?? null,
    //                 'dispatched_qty' => 0,
    //                 'remaining_qty' => $line['qty'],
    //                 'status' => 'pending',
    //             ]);
    //         }

    //         // --- 6. HANDLE ATTACHMENTS ---
    //         if ($request->hasFile('attachments')) {
    //             foreach ($request->file('attachments') as $index => $file) {
    //                 if ($file && $file->isValid()) {
    //                     $path = $file->store('order_attachments', 'public');
    //                     $order->attachments()->create([
    //                         'attachment' => $path,
    //                         'remarks' => $request->atch_remarks[$index] ?? null,
    //                     ]);
    //                 }
    //             }
    //         }

    //         DB::commit();

    //         return response()->json(
    //             [
    //                 'status' => true,
    //                 'message' => 'Order created successfully!',
    //                 'data' => $order->load(['allocations', 'attachments']),
    //             ],
    //             201
    //         );
    //     } catch (ValidationException $e) {
    //         DB::rollBack();
    //         return response()->json(['status' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('API Order store error: ' . $e->getMessage());
    //         return response()->json(['status' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500);
    //     }
    // }


    public function store(Request $request)
    {
        try {
            // --- 0. GET AUTHENTICATED USER & PREPARE REQUEST ---
            $appUser = $request->user();
            if (!$appUser) {
                return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
            }

            $userType = $appUser->type;
            $userCode = $appUser->code;
            $placer = null;

            if ($userType === 'dealer') {
                $placer = Dealer::where('code', $userCode)->first();
            } elseif ($userType === 'distributor') {
                $placer = Distributor::where('code', $userCode)->first();
            }

            if (!$placer) {
                return response()->json(['status' => false, 'message' => 'Authenticated user profile (Dealer/Distributor) not found.'], 404);
            }

            $request->merge([
                'type' => $userType,
                'dealer_id' => $userType === 'dealer' ? $placer->id : null,
                'distributor_id' => $userType === 'distributor' ? $placer->id : null,
                'created_by' => $placer->name,
            ]);

            // ===================================================================
            // --- 1. VALIDATION (Charges hata diye gaye hain) ---
            // ===================================================================
            $validated = $request->validate([
                'type' => 'required|in:dealer,distributor',
                'order_number' => 'nullable|string|unique:orders,order_number',
                'order_date' => 'required|date|after_or_equal:today',
                'dealer_id' => 'nullable|required_if:type,dealer|exists:dealers,id',
                'distributor_id' => 'nullable|required_if:type,distributor|exists:distributors,id',

                // !! YEH LINES HATA DI GAYI HAIN !!
                // 'loading_charge' => 'nullable|numeric',
                // 'insurance_charge' => 'nullable|numeric',

                'created_by' => 'required|string',
                'remarks' => 'nullable|string',
                'terms_conditions' => 'nullable|string',
                'allocations' => 'required|array|min:1',
                'allocations.*.for_type' => 'required|in:self,dealer',
                'allocations.*.qty' => 'required|numeric|min:0.1',
                'allocations.*.agreed_basic_price' => 'required|numeric|min:0',
                'allocations.*.basic_price' => 'required|numeric|min:0',
                'allocations.*.token_amount' => 'nullable|numeric|min:0',
                'allocations.*.payment_term' => 'required|string',
                'allocations.*.dealer_id' => 'nullable|required_if:allocations.*.for_type,dealer|exists:dealers,id',
                'allocations.*.remarks' => 'nullable|string',
                'attachments' => 'nullable|array',
                'attachments.*' => 'nullable|file|max:2048',
                'atch_remarks' => 'nullable|array',
                'atch_remarks.*' => 'nullable|string',
            ]);

            // --- 2. GENERATE ORDER NUMBER IF NOT PROVIDED ---
            if (empty($validated['order_number'])) {
                $carbonDate = Carbon::parse($validated['order_date']);
                $validated['order_number'] = Order::generateOrderNumber($carbonDate);
            }

            // --- 3. MANUAL BUSINESS LOGIC VALIDATION ---
            // (Ismein koi change nahi)
            if ($validated['type'] === 'dealer') {
                if (count($validated['allocations']) > 1) {
                    throw ValidationException::withMessages([
                        'allocations' => 'As a dealer, you can only add one allocation (for yourself) per order.',
                    ]);
                }
                foreach ($validated['allocations'] as $index => $allocation) {
                    if ($allocation['for_type'] !== 'self') {
                        throw ValidationException::withMessages([
                            "allocations.{$index}.for_type" => 'As a dealer, you can only place orders for yourself (for_type must be "self").',
                        ]);
                    }
                }
            } elseif ($validated['type'] === 'distributor') {
                $distributorId = $validated['distributor_id'];
                $team = DistributorTeam::where('distributor_id', $distributorId)->first();
                foreach ($validated['allocations'] as $index => $allocation) {
                    if ($allocation['for_type'] === 'dealer') {
                        $dealerId = $allocation['dealer_id'];
                        if (!$team) {
                            throw ValidationException::withMessages([
                                "allocations.{$index}.dealer_id" => 'You do not have a team configured to place orders for dealers.',
                            ]);
                        }
                        $isDealerInTeam = $team->dealers()->where('dealers.id', $dealerId)->exists();
                        if (!$isDealerInTeam) {
                            throw ValidationException::withMessages([
                                "allocations.{$index}.dealer_id" => "The selected dealer (ID: {$dealerId}) is not a member of your team.",
                            ]);
                        }
                    }
                }
            }
            // (Order Limit check - Ismein koi change nahi)
            foreach ($validated['allocations'] as $index => $line) {
                $qty = (float) $line['qty'];
                $entity = null;
                $limit = 0;
                if ($line['for_type'] === 'self') {
                    if ($request->type === 'distributor') {
                        $entity = Distributor::find($request->distributor_id);
                        $limit = (float) $entity->allowed_order_limit;
                    } else {
                        $entity = Dealer::find($request->dealer_id);
                        $limit = (float) $entity->allowed_order_limit;
                    }
                } else {
                    $entity = Dealer::find($line['dealer_id']);
                    $limit = (float) $entity->allowed_order_limit;
                }
                if (!$entity) {
                    throw ValidationException::withMessages(["allocations.{$index}.dealer_id" => 'The selected party was not found.']);
                }
                if ($qty > $limit) {
                    throw ValidationException::withMessages([
                        "allocations.{$index}.qty" => "Order quantity ({$qty} MT) exceeds the allowed limit ({$limit} MT) for {$entity->name}.",
                    ]);
                }
            }
            // --- Manual Validation End ---


            // ===================================================================
            // --- 3.5. NAYA CODE: Database se Charges Fetch Karein ---
            // ===================================================================
            $loadingCharge = LoadingCharge::first()->amount ?? 265;
            $insuranceCharge = InsuranceCharge::first()->amount ?? 40;


            // --- DB Transaction Start ---
            DB::beginTransaction();

            // ===================================================================
            // --- 4. PREPARE AND CREATE ORDER (Charges update kiye gaye hain) ---
            // ===================================================================
            $orderData = [
                'order_number' => $validated['order_number'],
                'order_date' => $validated['order_date'],
                'type' => $validated['type'],

                // !! YEH LINES UPDATE HO GAYI HAIN !!
                // Ab yeh DB se fetch ki gayi values (ya fallback) use kar raha hai
                'loading_charge' => $loadingCharge,
                'insurance_charge' => $insuranceCharge,

                'created_by' => $validated['created_by'],
                'status' => 'pending',
                'remarks' => $validated['remarks'] ?? null,
                'terms_conditions' => $validated['terms_conditions'] ?? null,
            ];

            if ($validated['type'] === 'dealer') {
                $orderData['placed_by_dealer_id'] = $validated['dealer_id'];
                $orderData['placed_by_distributor_id'] = null;
            } else {
                $orderData['placed_by_distributor_id'] = $validated['distributor_id'];
                $orderData['placed_by_dealer_id'] = null;
            }

            $order = Order::create($orderData);

            // --- 5. CREATE ALLOCATIONS ---
            // (Ismein koi change nahi)
            foreach ($validated['allocations'] as $line) {
                $order->allocations()->create([
                    'allocated_to_type' => $line['for_type'] === 'self' ? $request->type : 'dealer',
                    'allocated_to_id' => $line['for_type'] === 'self' ? ($request->type === 'dealer' ? $request->dealer_id : $request->distributor_id) : $line['dealer_id'],
                    'qty' => $line['qty'],
                    'remarks' => $line['remarks'] ?? null,
                    'basic_price' => $line['basic_price'],
                    'agreed_basic_price' => $line['agreed_basic_price'],
                    'payment_terms' => $line['payment_term'],
                    'token_amount' => $line['token_amount'] ?? null,
                    'dispatched_qty' => 0,
                    'remaining_qty' => $line['qty'],
                    'status' => 'pending',
                ]);
            }

            // --- 6. HANDLE ATTACHMENTS ---
            // (Ismein koi change nahi)
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $index => $file) {
                    if ($file && $file->isValid()) {
                        $path = $file->store('order_attachments', 'public');
                        $order->attachments()->create([
                            'attachment' => $path,
                            'remarks' => $request->atch_remarks[$index] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            $superAdminRole = \Spatie\Permission\Models\Role::where('name', 'Super Admin')->first();

            if ($superAdminRole) {
                $superAdmins = \App\Models\User::role($superAdminRole)->get();

                if ($superAdmins->isNotEmpty()) {
                    \Illuminate\Support\Facades\Notification::send(
                        $superAdmins,
                        new \App\Notifications\NewOrderPlaced($order, $placer)
                    );
                }
            } else {
                \Log::warning('Super Admin role not found while sending order notification.');
            }

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Order created successfully!',
                    'data' => $order->load(['allocations', 'attachments']),
                ],
                201
            );
        } catch (ValidationException $e) {
            DB::rollBack();

            // 1. Saare errors ko flat list banayein
            $allErrors = collect($e->errors())->flatten()->all();

            // 2. List ko naye format mein badlein
            $formattedErrors = collect($allErrors)->map(function ($message) {
                return ['reason' => $message];
            })->all();

            // 3. Naya response bhejein
            return response()->json([
                'status' => false,
                'message' => 'validations failed',
                'errors' => $formattedErrors
            ], 422);
            // --- BADLAAV KHATAM ---

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API Order store error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Order $order)
    {
        // STEP 1: SABSE ZAROORI CHECK
        if ($order->status !== 'pending') {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'This order cannot be edited because its status is not pending.',
                ],
                403,
            ); // 403 Forbidden
        }

        try {
            // --- 2. VALIDATION ---
            $validated = $request->validate([
                'type' => 'required|in:dealer,distributor',
                'order_date' => 'required|date',
                'dealer_id' => 'required_if:type,dealer|exists:dealers,id',
                'distributor_id' => 'required_if:type,distributor|exists:distributors,id',
                'order_number' => ['nullable', 'string', Rule::unique('orders')->ignore($order->id)],
                'payment_term' => 'required|string',
                'loading_charge' => 'nullable|numeric',
                'insurance_charge' => 'nullable|numeric',
                'created_by' => 'required|string',
                'remarks' => 'nullable|string',
                'terms_conditions' => 'nullable|string',
                'allocations' => 'required|array|min:1',
                'allocations.*.for_type' => 'required|in:self,dealer',
                'allocations.*.qty' => 'required|numeric|min:0.1',
                'allocations.*.agreed_basic_price' => 'required|numeric|min:0',
                'allocations.*.basic_price' => 'required|numeric|min:0',
                'allocations.*.token_amount' => 'nullable|numeric|min:0',
                'allocations.*.dealer_id' => 'nullable|required_if:allocations.*.for_type,dealer|exists:dealers,id',
                'allocations.*.remarks' => 'nullable|string',
            ]);

            // --- 3. MANUAL BUSINESS LOGIC VALIDATION (Copied from store method) ---

            // Check 3.1: Distributor-Dealer Association
            if ($validated['type'] === 'distributor') {
                foreach ($validated['allocations'] as $index => $allocation) {
                    if ($allocation['for_type'] === 'dealer') {
                        $dealer = Dealer::find($allocation['dealer_id']);
                        if (!$dealer || $dealer->distributor_id != $validated['distributor_id']) {
                            throw ValidationException::withMessages(["allocations.{$index}.dealer_id" => 'The selected dealer is not associated with this distributor.']);
                        }
                    }
                }
            }

            // Check 3.2: Allowed Order Limit
            foreach ($validated['allocations'] as $index => $line) {
                $qty = (float) $line['qty'];
                $entity = null;
                $limit = 0;
                if ($line['for_type'] === 'self') {
                    $entity = $request->type === 'distributor' ? Distributor::find($request->distributor_id) : Dealer::find($request->dealer_id);
                    $limit = $request->type === 'distributor' ? (float) $entity->individual_allowed_order_limit : (float) $entity->allowed_order_limit;
                } else {
                    $entity = Dealer::find($line['dealer_id']);
                    $limit = (float) $entity->allowed_order_limit;
                }

                if ($qty > $limit) {
                    throw ValidationException::withMessages(["allocations.{$index}.qty" => "Order quantity ({$qty} MT) exceeds the allowed limit ({$limit} MT) for {$entity->name}."]);
                }
            }

            DB::beginTransaction();

            // --- 4. UPDATE THE MAIN ORDER ---
            $orderData = $request->only(['order_date', 'type', 'payment_term', 'loading_charge', 'insurance_charge', 'created_by', 'remarks', 'terms_conditions']);
            if ($validated['type'] === 'dealer') {
                $orderData['placed_by_dealer_id'] = $validated['dealer_id'];
                $orderData['placed_by_distributor_id'] = null;
            } else {
                $orderData['placed_by_distributor_id'] = $validated['distributor_id'];
                $orderData['placed_by_dealer_id'] = null;
            }
            $order->update($orderData);

            // --- 5. SYNC ALLOCATIONS (Delete old, create new) ---
            $order->allocations()->delete();
            foreach ($validated['allocations'] as $line) {
                $order->allocations()->create([
                    'allocated_to_type' => $line['for_type'] === 'self' ? $request->type : 'dealer',
                    'allocated_to_id' => $line['for_type'] === 'self' ? ($request->type === 'dealer' ? $request->dealer_id : $request->distributor_id) : $line['dealer_id'],
                    'qty' => $line['qty'],
                    'remarks' => $line['remarks'] ?? null,
                    'basic_price' => $line['basic_price'],
                    'agreed_basic_price' => $line['agreed_basic_price'],
                    'payment_terms' => $order->payment_term,
                    'token_amount' => $line['token_amount'] ?? null,
                    'dispatched_qty' => 0,
                    'remaining_qty' => $line['qty'],
                    'status' => 'pending',
                ]);
            }

            DB::commit();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Order updated successfully!',
                    'data' => $order->fresh()->load(['allocations', 'attachments']),
                ],
                200,
            );
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API Order update error: ' . $e->getMessage() . ' on line ' . $e->getLine());
            return response()->json(['status' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generate Order Number based on Date
     */
    public function generateOrderNumberFromDate(Request $request)
    {
        $date = $request->input('order_date');

        if (!$date) {
            return response()->json(['error' => 'Date is required'], 400);
        }

        try {
            $carbonDate = Carbon::parse($date);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        $orderNumber = $this->generateOrderNumber($date);

        return response()->json(['order_number' => $orderNumber]);
    }

    /**
     * Private method to generate order number
     */
    private function generateOrderNumber($date)
    {
        $carbonDate = Carbon::parse($date);
        $dateFormatted = $carbonDate->format('Ymd');

        $lastOrder = Order::whereDate('order_date', $carbonDate)->orderBy('id', 'desc')->first();

        if ($lastOrder) {
            // Extract counter from last order_number
            $lastCounter = (int) substr($lastOrder->order_number, -4);
            $counter = $lastCounter + 1;
        } else {
            $counter = 1;
        }

        $counterPadded = str_pad($counter, 4, '0', STR_PAD_LEFT);

        return 'ODR_' . $dateFormatted . '_' . $counterPadded;
    }

    public function show(Order $order): OrderManagementResource
    {
        $order->load(['allocations', 'attachments', 'dealer', 'distributor']);
        return new OrderManagementResource($order);
    }

    // public function getOrderLimit(Request $request)
    // {
    //     try {
    //         // --- 1. Validate the incoming request (The new way) ---
    //         $validatedData = $request->validate([
    //             'type' => 'required|in:dealer,distributor',
    //             'id'   => 'required|integer|min:1',
    //         ]);

    //         $type = $validatedData['type'];
    //         $id = $validatedData['id'];
    //         $limitData = null;

    //         // --- 2. Fetch data based on the type ---
    //         if ($type === 'dealer') {
    //             $dealer = Dealer::find($id);

    //             if (!$dealer) {
    //                 return response()->json(['status' => false, 'message' => 'Dealer not found.'], 404);
    //             }

    //             $limitData = [
    //                 'name' => $dealer->name,
    //                 'code' => $dealer->code,
    //                 'order_limit' => $dealer->order_limit,
    //                 'allowed_order_limit' => $dealer->allowed_order_limit,
    //             ];
    //         } elseif ($type === 'distributor') {
    //             $distributor = Distributor::find($id);

    //             if (!$distributor) {
    //                 return response()->json(['status' => false, 'message' => 'Distributor not found.'], 404);
    //             }

    //             $limitData = [
    //                 'name' => $distributor->name,
    //                 'code' => $distributor->code,
    //                 'order_limit' => $distributor->order_limit,
    //                 'allowed_order_limit' => $distributor->allowed_order_limit,
    //                 'individual_allowed_order_limit' => $distributor->individual_allowed_order_limit,
    //             ];
    //         }

    //         // --- 3. Return the successful response ---
    //         return response()->json([
    //             'status' => true,
    //             'data' => $limitData
    //         ], 200);
    //     } catch (ValidationException $e) {
    //         // This catch block is optional but good practice.
    //         // Laravel handles the response automatically, but you can log it here if you want.
    //         // \Log::error('Validation failed for getOrderLimit: ' . $e->getMessage());
    //         // Re-throw the exception to let Laravel handle the JSON response.
    //         throw $e;
    //     }
    // }

    // public function getMyLimits(Request $request)
    // {
    //     // Get the currently authenticated app user
    //     $appUser = $request->user();

    //     // The user's type ('dealer' or 'distributor') and code are in the token's user object
    //     $userType = $appUser->type;
    //     $userCode = $appUser->code;

    //     $limits = [];

    //     // --- LOGIC CHANGE: We now search using TYPE and CODE ---

    //     // Case 1: If the logged-in user's type is 'dealer'
    //     if ($userType === 'dealer') { // Replace 'dealer' with the exact value from your app_user_management table
    //         $dealer = Dealer::where('code', $userCode)->first();
    //         if ($dealer) {
    //             $limits['dealer'] = [
    //                 'id' => $dealer->id,
    //                 'name' => $dealer->name,
    //                 'order_limit' => $dealer->order_limit,
    //                 'allowed_order_limit' => $dealer->allowed_order_limit,
    //             ];
    //         }
    //     }
    //     // Case 2: If the logged-in user's type is 'distributor'
    //     elseif ($userType === 'distributor') { // Replace 'distributor' with the exact value
    //         $distributor = Distributor::where('code', $userCode)->first();
    //         if ($distributor) {
    //             $limits['distributor'] = [
    //                 'id' => $distributor->id,
    //                 'name' => $distributor->name,
    //                 'order_limit' => $distributor->order_limit,
    //                 'allowed_order_limit' => $distributor->allowed_order_limit,
    //                 'individual_allowed_order_limit' => $distributor->individual_allowed_order_limit,
    //             ];
    //         }
    //     }

    //     // If no matching profile was found based on code and type
    //     if (empty($limits)) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'No matching dealer or distributor profile found for this user.'
    //         ], 404);
    //     }

    //     // Return the found limits successfully
    //     return response()->json([
    //         'status' => true,
    //         'data' => $limits
    //     ], 200);
    // }

    public function getMyLimits(Request $request)
    {
        // Get the currently authenticated app user
        $appUser = $request->user();

        // The user's type ('dealer' or 'distributor') and code are in the token's user object
        $userType = $appUser->type;
        $userCode = $appUser->code;

        $limits = []; // Initialize as empty array

        // --- LOGIC CHANGE: We now search using TYPE and CODE ---

        // Case 1: If the logged-in user's type is 'dealer'
        if ($userType === 'dealer') {
            // Replace 'dealer' with the exact value from your app_user_management table
            $dealer = Dealer::where('code', $userCode)->first();
            if ($dealer) {
                // !! CHANGE HERE !!
                // Assign directly to $limits, NOT $limits['dealer']
                $limits = [
                    'id' => $dealer->id,
                    'name' => $dealer->name,
                    'order_limit' => $dealer->order_limit,
                    'allowed_order_limit' => $dealer->allowed_order_limit,
                ];
            }
        }
        // Case 2: If the logged-in user's type is 'distributor'
        elseif ($userType === 'distributor') {
            // Replace 'distributor' with the exact value
            $distributor = Distributor::where('code', $userCode)->first();
            if ($distributor) {
                // !! CHANGE HERE !!
                // Assign directly to $limits, NOT $limits['distributor']
                $limits = [
                    'id' => $distributor->id,
                    'name' => $distributor->name,
                    'order_limit' => $distributor->order_limit,
                    'allowed_order_limit' => $distributor->allowed_order_limit,
                    'individual_allowed_order_limit' => $distributor->individual_allowed_order_limit,
                ];
            }
        }

        // If no matching profile was found based on code and type
        // This check will still work, as $limits will be []
        if (empty($limits)) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'No matching dealer or distributor profile found for this user.',
                ],
                404,
            );
        }

        // Return the found limits successfully
        // This will now have the flat structure your client wants
        return response()->json(
            [
                'status' => true,
                'data' => $limits,
            ],
            200,
        );
    }

    // public function getMyOrders(Request $request)
    // {
    //     // Step 1: Logged-in app user ko get karo
    //     $appUser = $request->user();
    //     $userType = $appUser->type;
    //     $userCode = $appUser->code;

    //     // Step 2: Query builder shuru karo
    //     $query = Order::query();

    //     // Relationships ko pehle hi load karlo behtar performance ke liye
    //     // $query->with(['placedByDealer', 'placedByDistributor', 'allocations']);
    //     $query->with(['placedByDealer', 'placedByDistributor', 'allocations', 'attachments']);

    //     // --- LOGIC START ---

    //     // Case A: Agar logged-in user 'dealer' hai
    //     if ($userType === 'dealer') {
    //         // 'dealer' ko aapke app_user_management table ke value se match karein
    //         // Dealer ko uske code se dhoondo
    //         $dealer = Dealer::where('code', $userCode)->first();

    //         if ($dealer) {
    //             // Sirf woh orders fetch karo jo is dealer ne place kiye hain
    //             $query->where('placed_by_dealer_id', $dealer->id);
    //         } else {
    //             // Agar dealer profile nahi mila, toh an empty result return karo
    //             return response()->json(['data' => []]);
    //         }
    //     }
    //     // Case B: Agar logged-in user 'distributor' hai
    //     elseif ($userType === 'distributor') {
    //         // 'distributor' ko value se match karein
    //         // Distributor ko uske code se dhoondo
    //         $distributor = Distributor::where('code', $userCode)->first();

    //         if ($distributor) {
    //             // Step B.1: Is distributor ke under aane waale saare dealers ki ID nikaalo
    //             $teamDealerIds = Dealer::where('distributor_id', $distributor->id)->pluck('id');

    //             // Step B.2: Query mein conditions lagao
    //             $query->where(function ($q) use ($distributor, $teamDealerIds) {
    //                 // Condition 1: Woh orders jo distributor ne khud place kiye hain
    //                 $q->where('placed_by_distributor_id', $distributor->id);

    //                 // Condition 2: YA, woh orders jo uski team ke dealers ne place kiye hain
    //                 $q->orWhereIn('placed_by_dealer_id', $teamDealerIds);
    //             });
    //         } else {
    //             // Agar distributor profile nahi mila, toh empty result return karo
    //             return response()->json(['data' => []]);
    //         }
    //     }

    //     // --- LOGIC END ---

    //     // Filters (Bonus feature, jaisa pehle banaya tha)
    //     if ($request->filled('status')) {
    //         $query->where('status', $request->input('status'));
    //     }
    //     if ($request->filled('start_date')) {
    //         $query->whereDate('order_date', '>=', $request->input('start_date'));
    //     }
    //     if ($request->filled('end_date')) {
    //         $query->whereDate('order_date', '<=', $request->input('end_date'));
    //     }

    //     // Hamesha latest order upar rakho
    //     $query->latest('order_date');

    //     // Results ko paginate karo
    //     $orders = $query->paginate(15);

    //     // return response()->json($orders);
    //     return OrderResource::collection($orders);
    // }


    // public function getMyOrders(Request $request)
    // {
    //     // Step 1: Logged-in app user ko get karo
    //     $appUser = $request->user();
    //     $userType = $appUser->type;
    //     $userCode = $appUser->code;

    //     // Step 2: Query builder shuru karo
    //     $query = Order::query();

    //     // Relationships ko pehle hi load karlo behtar performance ke liye
    //     $query->with([
    //         'placedByDealer',
    //         'placedByDistributor',
    //         'allocations',
    //         'attachments',
    //         // [NEW] Har allocation ke saath jude dealer ko load karo
    //         // (Maan kar chal rahe hain ki relationship ka naam 'dealer' hai)
    //         'allocations.dealer'
    //     ]);

    //     // --- LOGIC START ---

    //     // Case A: Agar logged-in user 'dealer' hai
    //     if ($userType === 'dealer') {
    //         // 'dealer' ko aapke app_user_management table ke value se match karein
    //         // Dealer ko uske code se dhoondo
    //         $dealer = Dealer::where('code', $userCode)->first();

    //         if ($dealer) {
    //             // Sirf woh orders fetch karo jo is dealer ne place kiye hain
    //             $query->where('placed_by_dealer_id', $dealer->id);
    //         } else {
    //             // Agar dealer profile nahi mila, toh an empty result return karo
    //             return response()->json(['data' => []]);
    //         }
    //     }
    //     // Case B: Agar logged-in user 'distributor' hai
    //     elseif ($userType === 'distributor') {
    //         // 'distributor' ko value se match karein
    //         // Distributor ko uske code se dhoondo
    //         $distributor = Distributor::where('code', $userCode)->first();

    //         if ($distributor) {
    //             // Step B.1: Is distributor ke under aane waale saare dealers ki ID nikaalo
    //             $teamDealerIds = Dealer::where('distributor_id', $distributor->id)->pluck('id');

    //             // Step B.2: Query mein conditions lagao
    //             $query->where(function ($q) use ($distributor, $teamDealerIds) {
    //                 // Condition 1: Woh orders jo distributor ne khud place kiye hain
    //                 $q->where('placed_by_distributor_id', $distributor->id);

    //                 // Condition 2: YA, woh orders jo uski team ke dealers ne place kiye hain
    //                 $q->orWhereIn('placed_by_dealer_id', $teamDealerIds);
    //             });
    //         } else {
    //             // Agar distributor profile nahi mila, toh empty result return karo
    //             return response()->json(['data' => []]);
    //         }
    //     }

    //     // --- LOGIC END ---

    //     // Filters (Bonus feature, jaisa pehle banaya tha)
    //     if ($request->filled('status')) {
    //         $query->where('status', $request->input('status'));
    //     }
    //     if ($request->filled('start_date')) {
    //         $query->whereDate('order_date', '>=', $request->input('start_date'));
    //     }
    //     if ($request->filled('end_date')) {
    //         $query->whereDate('order_date', '<=', $request->input('end_date'));
    //     }

    //     // Hamesha latest order upar rakho
    //     $query->latest('order_date');

    //     // --- [NEW] GST Logic (Aapke index method se liya gaya) ---

    //     // 1. Database se GST rate ek baar fetch kar lo
    //     $gstRate = GstRate::first()->rate ?? 18; // Default 18% agar nahi mila

    //     // Results ko paginate karo
    //     $orders = $query->paginate(15);

    //     // 2. Har order ke har allocation mein GST rate ko inject kar do
    //     // (Yeh Resource mein calculation ke liye zaroori hai)
    //     foreach ($orders->items() as $order) {
    //         foreach ($order->allocations as $allocation) {
    //             $allocation->gst_rate = $gstRate;
    //         }
    //     }

    //     // --- End GST Logic ---

    //     // return response()->json($orders);
    //     return OrderResource::collection($orders);
    // }

    // public function getMyOrders(Request $request)
    // {
    //     // Step 1: Logged-in app user ko get karo
    //     $appUser = $request->user();

    //     // [SUGGESTION] Add check for unauthenticated user
    //     if (!$appUser) {
    //         return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
    //     }

    //     $userType = $appUser->type;
    //     $userCode = $appUser->code;

    //     // Step 2: Query builder shuru karo
    //     $query = Order::query();

    //     // Relationships ko pehle hi load karlo behtar performance ke liye
    //     $query->with([
    //         'placedByDealer',
    //         'placedByDistributor',
    //         'allocations',
    //         'attachments',
    //         'allocations.dealer'
    //     ]);

    //     // --- LOGIC START ---

    //     // Case A: Agar logged-in user 'dealer' hai
    //     if ($userType === 'dealer') {
    //         $dealer = Dealer::where('code', $userCode)->first();
    //         if ($dealer) {
    //             $query->where('placed_by_dealer_id', $dealer->id);
    //         } else {
    //             return response()->json(['status' => true, 'data' => []]); // Return empty list
    //         }
    //     }
    //     // Case B: Agar logged-in user 'distributor' hai
    //     elseif ($userType === 'distributor') {
    //         $distributor = Distributor::where('code', $userCode)->first();
    //         if ($distributor) {
    //             $teamDealerIds = Dealer::where('distributor_id', $distributor->id)->pluck('id');
    //             $query->where(function ($q) use ($distributor, $teamDealerIds) {
    //                 $q->where('placed_by_distributor_id', $distributor->id);
    //                 $q->orWhereIn('placed_by_dealer_id', $teamDealerIds);
    //             });
    //         } else {
    //             return response()->json(['status' => true, 'data' => []]); // Return empty list
    //         }
    //     } else {
    //          // Agar user ka type match nahi hota, toh empty result do
    //          return response()->json(['status' => true, 'data' => []]);
    //     }

    //     // --- LOGIC END ---

    //     // Filters
    //     if ($request->filled('status')) {
    //         $query->where('status', $request->input('status'));
    //     }
    //     if ($request->filled('start_date')) {
    //         $query->whereDate('order_date', '>=', $request->input('start_date'));
    //     }
    //     if ($request->filled('end_date')) {
    //         $query->whereDate('order_date', '<=', $request->input('end_date'));
    //     }

    //     // Hamesha latest order upar rakho
    //     $query->latest('order_date');

    //     // --- GST Logic ---
    //     $gstRate = GstRate::first()->rate ?? 18;

    //     // --- [NEW] Custom Pagination Logic (index method jaisa) ---

    //     // 'per_page' optional hai. Default 15 rakha hai.
    //     $perPage = $request->input('per_page', 15);

    //     // Results ko paginate karo
    //     $paginator = $query->paginate($perPage);

    //     // Paginator se 'Order' items ka array nikalo
    //     $orders = $paginator->items();

    //     // Har order ke har allocation mein GST rate ko inject kar do
    //     foreach ($orders as $order) {
    //         foreach ($order->allocations as $allocation) {
    //             $allocation->gst_rate = $gstRate;
    //         }
    //     }
    //     // --- End GST/Pagination Logic ---

    //     // [NEW] Custom JSON Response (index method jaisa)
    //     // Hum data ko OrderResource se process kar rahe hain
    //     return response()->json(
    //         [
    //             'status' => true,
    //             'data' => OrderResource::collection($orders), // <-- Data ko Resource se format kiya
    //             'current_page' => $paginator->currentPage(),
    //             'per_page' => (int) $paginator->perPage(),
    //             'total' => $paginator->total(),
    //             'total_pages' => $paginator->lastPage(),
    //         ],
    //         200,
    //     );
    // }


    // public function getMyOrders(Request $request)
    // {
    //     // Step 1: Logged-in app user ko get karo
    //     $appUser = $request->user();

    //     if (!$appUser) {
    //         return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
    //     }

    //     $userType = $appUser->type;
    //     $userCode = $appUser->code;

    //     // Step 2: Query builder shuru karo
    //     $query = Order::query();

    //     // Relationships ko pehle hi load karlo
    //     $query->with([
    //         'placedByDealer',
    //         'placedByDistributor',
    //         'allocations',
    //         'attachments',
    //         // 'allocations.dealer'
    //         'allocations.allocatedTo'
    //     ]);

    //     // --- LOGIC START ---
    //     // (Yeh user-based logic bilkul waisa hi hai, ismein koi change nahi)
    //     if ($userType === 'dealer') {
    //         $dealer = Dealer::where('code', $userCode)->first();
    //         if ($dealer) {
    //             $query->where('placed_by_dealer_id', $dealer->id);
    //         } else {
    //             return response()->json(['status' => true, 'data' => []]);
    //         }
    //     } elseif ($userType === 'distributor') {
    //         $distributor = Distributor::where('code', $userCode)->first();
    //         if ($distributor) {
    //             $teamDealerIds = Dealer::where('distributor_id', $distributor->id)->pluck('id');
    //             $query->where(function ($q) use ($distributor, $teamDealerIds) {
    //                 $q->where('placed_by_distributor_id', $distributor->id);
    //                 $q->orWhereIn('placed_by_dealer_id', $teamDealerIds);
    //             });
    //         } else {
    //             return response()->json(['status' => true, 'data' => []]);
    //         }
    //     } else {
    //         return response()->json(['status' => true, 'data' => []]);
    //     }
    //     // --- LOGIC END ---

    //     // --- [START] Filters ---

    //     // 1. Status Filter (Pehle se tha)
    //     if ($request->filled('status')) {
    //         $query->where('status', $request->input('status'));
    //     }

    //     // 2. Date Range Filters (Pehle se tha)
    //     if ($request->filled('start_date')) {
    //         $query->whereDate('order_date', '>=', $request->input('start_date'));
    //     }
    //     if ($request->filled('end_date')) {
    //         $query->whereDate('order_date', '<=', $request->input('end_date'));
    //     }

    //     // 3. [NEW] Search Filter (Aapke index method se liya gaya)
    //     // Yeh 'order_number' par search karega
    //     if ($request->filled('search')) {
    //         $query->where('order_number', 'like', '%' . $request->input('search') . '%');
    //     }

    //     // --- [END] Filters ---

    //     // Hamesha latest order upar rakho
    //     // [UPDATED] Secondary sort add kiya (index method jaisa)
    //     $query->orderBy('order_date', 'desc')->orderBy('id', 'desc');

    //     // --- GST Logic ---
    //     $gstRate = GstRate::first()->rate ?? 18;

    //     // --- Custom Pagination Logic ---

    //     $perPage = $request->input('per_page', 15);
    //     $paginator = $query->paginate($perPage);

    //     // Paginator se 'Order' items ka array nikalo
    //     $orders = $paginator->items();

    //     // Har order ke har allocation mein GST rate ko inject kar do
    //     foreach ($orders as $order) {
    //         foreach ($order->allocations as $allocation) {
    //             $allocation->gst_rate = $gstRate;
    //         }
    //     }
    //     // --- End GST/Pagination Logic ---

    //     // Custom JSON Response
    //     return response()->json(
    //         [
    //             'status' => true,
    //             'data' => OrderResource::collection($orders),
    //             'current_page' => $paginator->currentPage(),
    //             'per_page' => (int) $paginator->perPage(),
    //             'total' => $paginator->total(),
    //             'total_pages' => $paginator->lastPage(),
    //         ],
    //         200,
    //     );
    // }
    public function getMyOrders(Request $request)
    {
        // Step 1: Logged-in app user ko get karo
        $appUser = $request->user();

        if (!$appUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $userType = $appUser->type;
        $userCode = $appUser->code;

        // Step 2: Query builder shuru karo
        $query = Order::query();

        // Relationships ko pehle hi load karlo
        $query->with([
            'placedByDealer',
            'placedByDistributor',
            'allocations',
            'attachments',
            // 'allocations.dealer'
            'allocations.allocatedTo'
        ]);

        // --- LOGIC START ---
        // (Yeh user-based logic bilkul waisa hi hai, ismein koi change nahi)
        if ($userType === 'dealer') {
            $dealer = Dealer::where('code', $userCode)->first();
            if ($dealer) {
                $query->where('placed_by_dealer_id', $dealer->id);
            } else {
                return response()->json(['status' => true, 'data' => []]);
            }
        } elseif ($userType === 'distributor') {
            $distributor = Distributor::where('code', $userCode)->first();
            if ($distributor) {
                $teamDealerIds = Dealer::where('distributor_id', $distributor->id)->pluck('id');
                $query->where(function ($q) use ($distributor, $teamDealerIds) {
                    $q->where('placed_by_distributor_id', $distributor->id);
                    $q->orWhereIn('placed_by_dealer_id', $teamDealerIds);
                });
            } else {
                return response()->json(['status' => true, 'data' => []]);
            }
        } else {
            return response()->json(['status' => true, 'data' => []]);
        }
        // --- LOGIC END ---

        // --- [START] Filters ---

        $query->where('status', '!=', 'deleted');

        // 1. Status Filter (Pehle se tha)
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // 2. Date Range Filters (Pehle se tha)
        if ($request->filled('start_date')) {
            $query->whereDate('order_date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('order_date', '<=', $request->input('end_date'));
        }

        // 3. [NEW] Search Filter (Aapke index method se liya gaya)
        // Yeh 'order_number' par search karega
        // if ($request->filled('search')) {
        //     $query->where('order_number', 'like', '%' . $request->input('search') . '%');
        // }

        // Ab yeh 'order_number' aur 'status' dono par search karega
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('order_number', 'like', $searchTerm)
                    ->orWhere('status', 'like', $searchTerm);
            });
        }

        // --- [END] Filters ---

        // Hamesha latest order upar rakho
        // [UPDATED] Secondary sort add kiya (index method jaisa)
        $query->orderBy('order_date', 'desc')->orderBy('id', 'desc');

        // --- GST Logic ---
        $gstRate = GstRate::first()->rate ?? 18;

        // --- Custom Pagination Logic ---

        $perPage = $request->input('per_page', 15);
        // $paginator = $query->paginate($perPage);

        $paginator = $query->paginate($perPage, ['*'], 'current_page');

        // --- YAHAN FIX KIYA GAYA HAI ---
        // $paginator->items() ki jagah $paginator->getCollection() ka istemaal karein
        $orderCollection = $paginator->getCollection();

        // Har order ke har allocation mein GST rate ko inject kar do
        // Ab $orderCollection (Collection) par loop karein
        $orderCollection->each(function ($order) use ($gstRate) {
            foreach ($order->allocations as $allocation) {
                $allocation->gst_rate = $gstRate;
            }
        });
        // --- End GST/Pagination Logic ---

        // Custom JSON Response
        return response()->json(
            [
                'status' => true,
                // --- YAHAN FIX KIYA GAYA HAI ---
                'data' => OrderResource::collection($orderCollection), // $orders (array) ki jagah $orderCollection (Collection)
                'current_page' => $paginator->currentPage(),
                'per_page' => (int) $paginator->perPage(),
                'total' => $paginator->total(),
                'total_pages' => $paginator->lastPage(),
            ],
            200,
        );
    }
}
