<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ItemListController;
use App\Http\Controllers\Api\ItemSizesController;
use App\Http\Controllers\Api\ItemBasicPriceController;

// Md Raza Changes starts from here

use App\Http\Controllers\Api\OrderManagementController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\DealerController;
use App\Http\Controllers\Api\DistributorController;
use App\Http\Controllers\Api\DistributorTeamController;
use App\Http\Controllers\Api\DispatchController as DispatchController;

// Md Raza Changes ends here

// for register user
// Route::prefix('user')->group(function () {
Route::prefix('app-user')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/password/update', [AuthController::class, 'password_update']);
});

Route::prefix('reset-password')->group(function () {
    // Generate and send OTP
    Route::post('/otp', [AuthController::class, 'sendOtp'])
        ->name('password.otp.generate')
        ->middleware('throttle:3,1'); // 3 attempts per minute

    // Verify OTP
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])
        ->name('password.otp.verify')
        ->middleware('throttle:5,1'); // 5 attempts per minute

    // Reset password with OTP
    Route::post('/reset', [AuthController::class, 'resetWithOtp'])
        ->name('password.reset')
        ->middleware('throttle:3,1'); // 3 attempts per minute
});

Route::middleware('auth:sanctum')->group(function () {
    // for update user
    Route::prefix('user')->group(function () {
        Route::get('/details', [AuthController::class, 'list']);
        Route::post('/profile', [AuthController::class, 'upload_profile']);
        Route::post('/update', [AuthController::class, 'update']);
        Route::post('/delete', [AuthController::class, 'destroy']);
        // change app users password
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        // my profile app users
        Route::get('/my-profile', [AuthController::class, 'getMyProfile']);
        Route::post('/request-order-limit', [AuthController::class, 'requestMyOrderLimit'])->name('profile.requestOrderLimit');

        Route::get('/state', [AuthController::class, 'state']);
        Route::post('/city', [AuthController::class, 'city']);
    });

    Route::get('/notifications', [App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/all/itmes', [ItemListController::class, 'all_item_list']);
    Route::get('/get-all-item-list', [ItemListController::class, 'item_list']);
    Route::get('/get-all-rolling-list', [ItemListController::class, 'rolling_list']);

    Route::prefix('item-sizes')
        ->controller(ItemSizesController::class)
        ->group(function () {
            Route::get('/', 'index'); // GET    /api/item-sizes
            Route::get('/inactive', 'inactiveItemSizes'); // GET    /api/item-sizes/inactive
            Route::post('/', 'store'); // POST   /api/item-sizes
            Route::put('/{id}', 'update'); // PUT    /api/item-sizes/{id}
            Route::get('/{id}', 'show'); // GET    /api/item-sizes/{id}
            Route::patch('/{id}/deactivate', 'markInactive'); // PATCH  /api/item-sizes/{id}/deactivate
            Route::patch('/{id}/activate', 'activateItemSize'); // PATCH  /api/item-sizes/{id}/activate
        });

    // ✅ Add Item Basic Prices API here
    Route::get('item-basic-prices', [ItemBasicPriceController::class, 'index']);
    // });

    // Md Raza Changes starts from here

    // Item Basic Price View
    // Route::get('item-basic-prices', [ItemBasicPriceController::class, 'index']);
    // Item Basic Price Store
    // Route::post('item-basic-prices', [ItemBasicPriceController::class, 'store']);
    // Item Basic Price Update - not used currently
    // Route::put('item-basic-prices/{itemBasicPrice}', [ItemBasicPriceController::class, 'update']);
    // Item Basic Price List
    // Route::apiResource('items', ItemController::class)->only(['index', 'show']);

    // 1. Get a list of all APPROVED prices
    Route::get('item-basic-prices', [ItemBasicPriceController::class, 'index']);
    // 2. Get a single APPROVED price and its history
    Route::get('item-basic-prices/{itemBasicPrice}', [ItemBasicPriceController::class, 'show']);
    // 3. Submit a NEW price request (sets status to 'Pending')
    //    This is used for both creating the first price and requesting an update.
    Route::post('item-basic-prices', [ItemBasicPriceController::class, 'store']);
    // --- Approval Workflow Routes ---
    // 4. Get a list of all PENDING price requests for an admin/approver
    Route::get('item-basic-prices-approval-requests', [ItemBasicPriceController::class, 'approvalRequests']);
    // 5. Approve a specific pending request
    Route::post('item-basic-prices/{itemBasicPrice}/approve', [ItemBasicPriceController::class, 'approve']);
    // 6. Reject a specific pending request
    Route::post('item-basic-prices/{itemBasicPrice}/reject', [ItemBasicPriceController::class, 'reject']);

    // Order Management Routes
    Route::apiResource('orders', OrderManagementController::class);
    Route::post('orders/generate-number', [OrderManagementController::class, 'generateOrderNumberFromDate']);
    Route::get('orders/prepare', [OrderManagementController::class, 'prepareOrderData']);
    // Get Dealers and Distributors order limits
    // Route::get('order-limit', [OrderManagementController::class, 'getOrderLimit']);
    Route::get('/my-limits', [OrderManagementController::class, 'getMyLimits']);
    // see my orders
    Route::get('/my-orders', [OrderManagementController::class, 'getMyOrders']);

    // Dealer Routes
    Route::apiResource('dealers', DealerController::class);
    // Dealer Request Order Limit Change
    // Route::post('/dealers/request-order-limit', [DealerController::class, 'requestOrderLimit'])->name('dealers.requestOrderLimit');
    Route::post('/dealers/request-order-limit', [DealerController::class, 'requestMyOrderLimit'])->name('dealers.requestOrderLimit');
    // Dealer Inactivate
    Route::put('/dealers/{dealer}/deactivate', [DealerController::class, 'deactivate'])->name('dealers.deactivate');
    // Logged-in distributor dwara naya dealer create karne ke liye route
    Route::post('/team/add-dealer', [DealerController::class, 'storeByDistributor']);

    // Distributor Routes
    // Distributor List
    Route::get('/distributors', [DistributorController::class, 'index'])->name('distributors.index');
    // Distributor Store
    Route::post('/distributors', [DistributorController::class, 'store'])->name('distributors.store');
    // Distributor Update
    Route::put('/distributors/{distributor}', [DistributorController::class, 'update'])->name('distributors.update');
    // Distributor Inactivate
    Route::put('/distributors/{distributor}/deactivate', [DistributorController::class, 'deactivate'])->name('distributors.deactivate');
    // Distributor Request Order Limit Change
    // Route::post('/distributors/request-order-limit', [DistributorController::class, 'requestOrderLimit']);
    Route::post('/distributors/request-order-limit', [DistributorController::class, 'requestMyOrderLimit']);
    Route::get('/distributors/my-team', [DistributorController::class, 'getMyTeam']);

    Route::get('/distributors/{distributor}/available-dealers', [DistributorTeamController::class, 'getAvailableDealers']);

    Route::post('/distributor-teams', [DistributorTeamController::class, 'store']);

    Route::put('/distributor-teams/{team}', [DistributorTeamController::class, 'update']);

    Route::get('/dispatches', [DispatchController::class, 'index']);
    Route::get('/dispatches/{dispatch}', [DispatchController::class, 'show']);
    // Md Raza Changes ends here

    // Step 1: Get initial data (warehouses, sizes, user's orders, charges)
    Route::get('/dispatches/prepare', [DispatchController::class, 'prepare']);

    // Step 2: Get allocations for selected orders
    Route::post('/dispatches/pending-orders', [DispatchController::class, 'getPendingOrders']);

    // Step 3: Create dispatch
    Route::post('/dispatches', [DispatchController::class, 'store']);

    // Get all dispatches for logged-in user
    Route::get('/dispatches', [DispatchController::class, 'index']);

    // Get single dispatch details
    Route::get('/dispatches/{id}', [DispatchController::class, 'show']);

    // Get allocation details by ID
    Route::get('/allocations/{allocationId}/details', [DispatchController::class, 'getAllocationDetails']);

    // Check if bill number is unique
    Route::get('/dispatches/check-bill-number', [DispatchController::class, 'checkBillNumber']);

    // Generate dispatch number based on date
    Route::post('/dispatches/generate-number', [DispatchController::class, 'generateDispatchNumber']);

    // Get states list
    Route::get('/states', [DispatchController::class, 'getStates']);

    // Get cities by state
    Route::get('/states/{stateId}/cities', [DispatchController::class, 'getCities']);
});
