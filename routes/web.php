<?php

    use App\Http\Controllers\LoadingPointMasterController;
    use App\Http\Controllers\ProfileController;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\RoleController;
    use App\Http\Controllers\UserController;
    use App\Http\Controllers\CompanySettingController;
    use App\Http\Controllers\HomeController;
    use App\Http\Controllers\LoginController;
    use App\Http\Controllers\GstSettingController;
    use App\Http\Controllers\ItemBasicPriceController;
    use App\Http\Controllers\ItemSizesController;
    use App\Http\Controllers\ItemBundleController;
    use App\Http\Controllers\DistributorController;
    use App\Http\Controllers\DealerController;
    use App\Http\Controllers\DistributorTeamController;
    use App\Http\Controllers\OrderManagementController;
    use App\Http\Controllers\WarehouseController;
    use App\Http\Controllers\DispatchController;
    use App\Http\Controllers\AppUserManagementController;
    use App\Services\BrevoService;

    Route::get('/test-brevo', function () {

        $toEmail = "yashams.singhal@gmail.com";
        $subject = "Test Email from Brevo API";
        $htmlContent = "<h1>Hello</h1><p>This is a Brevo API test.</p>";

        try {
            $config = SendinBlue\Client\Configuration::getDefaultConfiguration()
                ->setApiKey('api-key', env('BREVO_API_KEY'));

            $apiInstance = new SendinBlue\Client\Api\TransactionalEmailsApi(
                new GuzzleHttp\Client(),
                $config
            );

            $sendSmtpEmail = new SendinBlue\Client\Model\SendSmtpEmail([
                'subject' => $subject,
                'sender' => ['email' => env('MAIL_FROM_ADDRESS')],
                'to' => [['email' => $toEmail]],
                'htmlContent' => $htmlContent,
            ]);

            $response = $apiInstance->sendTransacEmail($sendSmtpEmail);

            return [
                'status' => 'success',
                'messageId' => $response->getMessageId() ?? null,
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    });




    // Md Raza Changes starts from here
    use App\Http\Controllers\ReportController;
    // Md Raza Changes ends here


    // Route::get('/', function () {
    //     return view('welcome');
    // });

    Route::get('/', function () {
        return view('auth.login');
    })->name('login');


    Route::get('/logs', [HomeController::class, 'logs']);


    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::get('/edit-profile', [ProfileController::class, 'index'])->name('profile_edit');
        Route::post('/profile-update/{id}', [ProfileController::class, 'pro_update'])->name('profile_update');
        Route::post('/password-update/{id}', [ProfileController::class, 'password_reset'])->name('pass_update');

        Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');


        // ............................................user............................................
        Route::get('user', [UserController::class, 'index'])->name('users.index');
        Route::get('/user-create', [UserController::class, 'create'])->name('users.create');
        Route::post('user-store', [UserController::class, 'store'])->name('users.store');
        Route::get('user-show-{id}', [UserController::class, 'show'])->name('users.show');
        Route::get('user-edit-{id}', [UserController::class, 'edit'])->name('users.edit');
        Route::put('user-update-{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/user-delete/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::delete('/user-resoter/{id}', [UserController::class, 'resoter'])->name('users.resoter');


        // ............................................role............................................
        Route::get('role', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/role-create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('role-store', [RoleController::class, 'store'])->name('roles.store');
        Route::get('role-show-{id}', [RoleController::class, 'show'])->name('roles.show');
        Route::get('role-edit-{id}', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('role-update-{id}', [RoleController::class, 'update'])->name('roles.update');
        Route::get('/role-delete/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');

        Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])
            ->name('notifications.read');
        Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllRead'])
            ->name('notifications.markAllRead');

        // Optional: list all notifications page
        Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])
         ->name('notifications.index');
        Route::get('/api/notifications/unread/count', function () {
            return response()->json([
                'count' => auth()->user()->unreadNotifications->count()
            ]);
        });

        // Order Management Routes
        Route::get('order-management', [OrderManagementController::class, 'index'])->name('order_management');
        Route::get('order-management/create', [OrderManagementController::class, 'create'])->name('order_management.create');
        Route::get('order-management/show/{id}', [OrderManagementController::class, 'show'])->name('order_management.show');
        Route::get('order-management/edit/{id}', [OrderManagementController::class, 'edit'])->name('order_management.edit');
        Route::put('order-management/update/{id}', [OrderManagementController::class, 'update'])->name('order_management.update');
        Route::post('order-management/store', [OrderManagementController::class, 'store'])->name('order_management.store');
        Route::put('/order-management/approve/{id}', [OrderManagementController::class, 'approve'])->name('orders.approve');
        Route::post('/orders/generate-order-number', [OrderManagementController::class, 'generateOrderNumberFromDate'])->name('orders.generateOrderNumber');
        Route::get('orders/reject/{id}', [OrderManagementController::class, 'reject'])->name('orders.reject');
        Route::delete('orders/destroy/{id}', [OrderManagementController::class, 'delete'])->name('orders.delete');
        Route::get('/order-management/allocations/{id}', [OrderManagementController::class, 'getAllocations'])->name('order_management.allocations');
        Route::put('/order-management/change-status/{id}', [App\Http\Controllers\OrderManagementController::class, 'changeStatus'])->name('order_management.change_status')->middleware('permission:Order-ChangeStatus');

        // Item Master Routes

        // Items
        Route::get('items/index', [ItemSizesController::class, 'items'])->name('items.index');

        // Item Sizes
        Route::get('item-sizes', [ItemSizesController::class, 'index'])->name('itemSizes.index');
        Route::get('item-sizes/inactive', [ItemSizesController::class, 'inactiveItemSizes'])->name('itemSizes.inactiveSizes');
        Route::post('item-sizes/add', [ItemSizesController::class, 'add'])->name('itemSizes.add');
        Route::put('item-sizes/update/{id}', [ItemSizesController::class, 'update'])->name('itemSizes.update');
        Route::get('item-sizes/show/{id}', [ItemSizesController::class, 'show'])->name('itemSizes.show');
        Route::patch('/item-sizes/inactive/{id}', [ItemSizesController::class, 'markInactive'])->name('itemSizes.inactive');
        Route::patch('item-sizes/active/{id}', [ItemSizesController::class, 'activeItemSizes'])->name('itemSizes.activate');
        Route::get('item-sizes/approval-requests', [ItemSizesController::class, 'approvalRequests'])->name('itemSizes.approvalRequests');
        Route::patch('/item-size/approve/{id}', [ItemSizesController::class, 'approve'])->name('itemSizes.approve');
        Route::patch('/item-size/reject/{id}', [ItemSizesController::class, 'reject'])->name('itemSizes.reject');

        // Item Basic Prices
        Route::get('item-basic-prices', [ItemBasicPriceController::class, 'index'])->name('itemBasicPrice.index');
        Route::post('item-basic-prices/add', [ItemBasicPriceController::class, 'store'])->name('itemBasicPrices.add');
        Route::put('item-basic-prices/update/{id}', [ItemBasicPriceController::class, 'update'])->name('itemBasicPrices.update');
        Route::get('item-basic-prices/export', [ItemBasicPriceController::class, 'export'])->name('itemBasicPrice.export');
        Route::post('item-basic-prices/import', [ItemBasicPriceController::class, 'import'])->name('itemBasicPrice.import');
        Route::get('item-basic-prices/approval-requests', [ItemBasicPriceController::class, 'approvalRequests'])->name('itemBasicPrice.approvalRequests');
        Route::patch('/item-basic-price/approve/{id}', [ItemBasicPriceController::class, 'approve'])->name('itemBasicPrice.approve');
        Route::patch('/item-basic-price/reject/{id}', [ItemBasicPriceController::class, 'reject'])->name('itemBasicPrice.reject');
        Route::get('item-basic-prices/show/{id}', [ItemBasicPriceController::class, 'show'])->name('itemBasicPrices.show');
        Route::patch('/item-basic-price/approve-all', [ItemBasicPriceController::class, 'approveAll'])->name('itemBasicPrice.approveAll');
        Route::patch('/item-basic-price/reject-all', [ItemBasicPriceController::class, 'rejectAll'])->name('itemBasicPrice.rejectAll');
        Route::get('/item-basic-price/rejected',[ItemBasicPriceController::class, 'rejected'])->name('itemBasicPrice.rejected');

        // Item Bundle
        Route::get('item-bundle', [ItemBundleController::class, 'index'])->name('itemBundle.index');
        Route::post('item-bundle/add', [ItemBundleController::class, 'store'])->name('itemBundle.add');
        Route::patch('item-bundle/update/{id}', [ItemBundleController::class, 'update'])->name('itemBundle.update');

        // App User Management Routes
        Route::get('app-user-management',[AppUserManagementController::class,'index'])->name('appUserManagement');
        Route::get('/app-users/{id}', [AppUserManagementController::class, 'show'])->name('app-users.show');
        Route::get('/app-users/{id}/edit', [AppUserManagementController::class, 'edit'])->name('app-users.edit');
        Route::put('/app-users/{id}', [AppUserManagementController::class, 'update'])->name('app-users.update');
        Route::get('/app-users/cities/{stateId}', [AppUserManagementController::class, 'cities']);

        // Dealers And Distributors

        // Distributors
        Route::get('distributors', [DistributorController::class, 'index'])->name('distributors.index');
        Route::get('distributors/create', [DistributorController::class, 'create'])->name('distributors.create');
        Route::get('distributors/show/{id}', [DistributorController::class, 'show'])->name('distributors.show');
        Route::post('distributors', [DistributorController::class, 'store'])->name('distributors.store');
        Route::get('distributors/edit/{id}', [DistributorController::class, 'edit'])->name('distributors.edit');
        Route::put('distributors/update/{id}', [DistributorController::class, 'update'])->name('distributors.update');
        Route::get('/get-cities/{state_id}', [DistributorController::class, 'cities']);
        Route::post('/distributors/order-limit-request', [DistributorController::class, 'requestMyOrderLimit'])->name('distributors.orderLimitRequest');
        Route::get('/distributors/order-limit-request/requests', [DistributorController::class, 'olRequests'])->name('distributors.olRequests');
        Route::patch('/distributors/order-limit-request/approve/{id}', [DistributorController::class, 'olRequestApprove'])->name('distributors.olRequestApprove');
        Route::patch('/distributors/order-limit-request/reject/{id}', [DistributorController::class, 'olRequestReject'])->name('distributors.olRequestReject');
        Route::get('/distributor/check-inactivation/{id}', [DistributorController::class, 'checkInactivation']);
        Route::post('/distributor/inactivate', [DistributorController::class, 'inactivate'])->name('distributor.inactivate');
        Route::post('/distributor/activate', [DistributorController::class, 'activate'])->name('distributor.activate');

        // Dealers
        Route::get('dealers', [DealerController::class, 'index'])->name('dealers.index');
        Route::get('dealers/create', [DealerController::class, 'create'])->name('dealers.create');
        Route::get('dealers/show/{id}', [DealerController::class, 'show'])->name('dealers.show');
        Route::post('dealers', [DealerController::class, 'store'])->name('dealers.store');
        Route::get('dealers/edit/{id}', [DealerController::class, 'edit'])->name('dealers.edit');
        Route::put('dealers/update/{id}', [DealerController::class, 'update'])->name('dealers.update');
        Route::get('dealers/approval-requests', [DealerController::class, 'approvalRequests'])->name('dealers.approvalRequests');
        Route::patch('/dealers/approve/{id}', [DealerController::class, 'approve'])->name('dealers.approve');
        Route::patch('/dealers/reject/{id}', [DealerController::class, 'reject'])->name('dealers.reject');
        Route::post('/dealers/order-limit-request', [DealerController::class, 'storeOrderLimitRequest'])->name('dealers.orderLimitRequest');
        Route::get('/dealers/order-limit-request/requests', [DealerController::class, 'olRequests'])->name('dealers.olRequests');
        Route::patch('/dealers/order-limit-request/approve/{id}', [DealerController::class, 'olRequestApprove'])->name('dealers.olRequestApprove');
        Route::patch('/dealers/order-limit-request/reject/{id}', [DealerController::class, 'olRequestReject'])->name('dealers.olRequestReject');
        Route::get('/dealer/check-inactivation/{id}', [DealerController::class, 'checkInactivation']);
        Route::post('/dealer/inactivate', [DealerController::class, 'inactivate'])->name('dealer.inactivate');
        Route::post('/dealer/activate', [DealerController::class, 'activate'])->name('dealer.activate');



        // Distributor Team
        Route::get('distributor-team', [DistributorTeamController::class, 'index'])->name('distributor_team.index');
        Route::get('distributor-team/create', [DistributorTeamController::class, 'create'])->name('distributor_team.create');
        Route::post('distributor-team/store', [DistributorTeamController::class, 'store'])->name('distributor_team.store');
        Route::get('/distributor-team/{id}/dealers', [DistributorTeamController::class, 'getDealersInModal'])->name('distributor_team.dealers');
        Route::get('/get-dealers-by-distributor/{id}', [DistributorTeamController::class, 'getDealersByDistributor'])->name('get.dealers.by.distributor');
        Route::get('/get-distributor/{id}', [DistributorTeamController::class, 'getDistributor']);
        Route::post('/get-dealers', [DistributorTeamController::class, 'getDealers']);
        Route::get('distributor-team/show/{id}', [DistributorTeamController::class, 'show'])->name('distributor_team.show');
        Route::get('distributor-team/edit/{id}', [DistributorTeamController::class, 'edit'])->name('distributor_team.edit');
        Route::put('distributor-team/update/{id}', [DistributorTeamController::class, 'update'])->name('distributor_team.update');
        Route::post('/distributor-team/{teamId}/suspend', [DistributorTeamController::class, 'suspend'])->name('distributor_team.suspend');

        // Dispatch Routes
        Route::get('dispatch', [DispatchController::class, 'index'])->name('dispatch.index');
        Route::get('dispatch/create', [DispatchController::class, 'create'])->name('dispatch.create');
        Route::post('dispatch/store', [DispatchController::class, 'store'])->name('dispatch.store');
        // Route::get('/api/party-list', [DispatchController::class, 'getParties']);
        // Route::get('/api/order-list', [DispatchController::class, 'getOrders']);
        Route::get('/generate-dispatch-number', [DispatchController::class, 'generateDispatchNumber']);
        Route::get('/api/dispatch/{id}/items', [DispatchController::class, 'getLineItems']);
        Route::get('dispatch/show/{id}', [DispatchController::class, 'show'])->name('dispatch.show');
        Route::patch('/dispatch/{id}/approve', [DispatchController::class, 'approve'])->name('dispatch.approve');
        Route::get('/dispatch/{id}/edit', [DispatchController::class, 'edit'])->name('dispatch.edit');
        Route::put('/dispatch/{id}/update', [DispatchController::class, 'update'])->name('dispatch.update');
        Route::get('/orders/{orderId}/allocations', [DispatchController::class, 'getAllocations'])->name('api.allocations.index');
        Route::get('/api/items', [DispatchController::class, 'item']);
        Route::get('/api/allocations/{allocationId}', [DispatchController::class, 'allocations']);
        Route::get('/api/party-list', [DispatchController::class, 'getPartyList']);
        Route::get('/api/order-list', [DispatchController::class, 'getOrderList']);
        Route::get('/api/dispatch/{id}/items', [DispatchController::class, 'getDispatchItems']);
        Route::get('/check-bill-number', [DispatchController::class, 'checkBillNumber'])->name('dispatch.checkBillNumber');
        // Delete Dispatch (only for pending)
        Route::delete('/dispatch/{dispatch}', [DispatchController::class, 'destroy'])
            ->name('dispatch.destroy');

        // WareHouse Routes
        Route::get('/settings/warehouse', [WareHouseController::class, 'index'])->name('warehouse.index');
        Route::get('/settings/warehouse/create', [WareHouseController::class, 'create'])->name('warehouse.create');
        Route::post('/settings/warehouse', [WareHouseController::class, 'store'])->name('warehouse.store');
        Route::get('/settings/warehouse/{id}', [WarehouseController::class, 'show'])->name('warehouse.show');
        Route::get('/settings/warehouse/{id}/edit', [WarehouseController::class, 'edit'])->name('warehouse.edit');
        Route::put('/settings/warehouse/{id}', [WarehouseController::class, 'update'])->name('warehouse.update');

        // Loading Point Masters Routes
        Route::get('/settings/loading-point-master', [LoadingPointMasterController::class, 'index'])->name('loadingPointMaster.index');
        Route::get('/settings/loading-point-master/create', [LoadingPointMasterController::class, 'create'])->name('loadingPointMaster.create');
        Route::post('/settings/loading-point-master', [LoadingPointMasterController::class, 'store'])->name('loadingPointMaster.store');

        /*
   |--------------------------------------------------------------------------
   | Customer Management Routes
   |--------------------------------------------------------------------------
   */
        // Route For GstSetting Controller
        Route::resource('gst-setting', GstSettingController::class);
        Route::post('/get_gst_details', [GstSettingController::class, 'get_gst_details'])->name('get_gst_details');
        Route::post('gst-setting-update', [GstSettingController::class, 'update'])->name('gstsetting.update');
        Route::delete('gst-setting-activate/{id}', [GstSettingController::class, 'activate'])->name('gstsetting.activate');


        // ............................................Company Setting............................................
        Route::get('company', [CompanySettingController::class, 'index'])->name('company');
        Route::post('company-update', [CompanySettingController::class, 'update'])->name('setting.company_update');

        // ............................................Company Setting............................................
        Route::get('items', [CompanySettingController::class, 'index'])->name('item');


        Route::post('company-update', [CompanySettingController::class, 'update'])->name('setting.company_update');



        // Md Raza Changes starts from here

        // Route::get('/order-report', [ReportController::class, 'index'])->name('order.report');

        // Orders Report
        Route::get('/order-report', [ReportController::class, 'order_report'])->name('order.report');
        Route::post('/order-report', [ReportController::class, 'get_order_report'])->name('get_order.report');
        Route::get('/order-report/{id}', [ReportController::class, 'order_report'])->name('order_filter.report');

        // Dispatch Report
        Route::get('/dispatch-report', [ReportController::class, 'dispatch_report'])->name('dispatch.report');
        Route::post('/dispatch-report', [ReportController::class, 'get_dispatch_report'])->name('get_dispatch.report');
        Route::get('/dispatch-report/{id}', [ReportController::class, 'dispatch_report'])->name('dispatch_filter.report');
        // Dispatch Report line items
        Route::get('/reports/dispatch/{id}/items', [ReportController::class, 'get_dispatch_items'])->name('get_dispatch_items');

        // Item Price Report
        Route::get('/item-price-report', [ReportController::class, 'item_price_report'])->name('item_price.report');
        Route::post('/item-price-report', [ReportController::class, 'get_item_price_report'])->name('get_item_price.report');
        Route::get('/item-price-report/{id}', [ReportController::class, 'item_price_report'])->name('item_price_filter.report');
        Route::get('/reports/item-price/{id}/history', [ReportController::class, 'get_item_price_history'])->name('get_item_price_history');

        // Distributor Team Report
        Route::get('/distributor-team-report', [ReportController::class, 'distributor_team_report'])->name('distributor_team.report');
        Route::post('/distributor-team-report', [ReportController::class, 'get_distributor_team_report'])->name('get_distributor_team.report');
        Route::get('/distributor-team-report/{id}', [ReportController::class, 'distributor_team_report'])->name('distributor_team_filter.report');

        // Show no of distributors in the modal by ajax
        Route::get('/reports/distributor-teams/{id}/dealers', [ReportController::class, 'get_distributor_team_dealers'])->name('get_distributor_team_dealers');

        // Dealers Report
        Route::get('/dealers-report', [ReportController::class, 'dealers_report'])->name('dealers.report');
        Route::post('/dealers-report', [ReportController::class, 'get_dealers_report'])->name('get_dealers.report');
        Route::get('/dealers-report/{id}', [ReportController::class, 'dealers_report'])->name('dealers_filter.report');

        // Show Dealers order limit history modal by ajax
        Route::get('/reports/dealers/{id}/order-limit-history', [ReportController::class, 'get_dealer_order_limit_history'])->name('get_dealer_order_limit_history');

        // Show Dealers contact person modal by ajax
        Route::get('/reports/dealers/{id}/contact-persons', [ReportController::class, 'get_dealer_contact_persons'])->name('get_dealer_contact_persons');

        // Distributors Report
        Route::get('/distributors-report', [ReportController::class, 'distributors_report'])->name('distributors.report');
        Route::post('/distributors-report', [ReportController::class, 'get_distributors_report'])->name('get_distributors.report');
        Route::get('/distributors-report/{id}', [ReportController::class, 'distributors_report'])->name('distributors_filter.report');

        // Show Distributora order limit history modal by ajax
        Route::get('/reports/distributors/{id}/order-limit-history', [ReportController::class, 'get_distributor_order_limit_history'])->name('get_distributor_order_limit_history');

        // Show Distributors contact person modal by ajax
        Route::get('/reports/distributors/{id}/contact-persons', [ReportController::class, 'get_distributor_contact_persons'])->name('get_distributor_contact_persons');

        // Item Size Report
        Route::get('/item-sizes-report', [ReportController::class, 'item_sizes_report'])->name('item_sizes.report');
        Route::post('/item-sizes-report', [ReportController::class, 'get_item_sizes_report'])->name('get_item_sizes.report');
        Route::get('/item-sizes-report/{id}', [ReportController::class, 'item_sizes_report'])->name('item_sizes_filter.report');

        Route::get('/reports/item-sizes/{id}/history', [ReportController::class, 'get_item_size_history'])->name('get_item_size_history');

        // Order PDF Download
        Route::get('/order/pdf/download/{id}', [OrderManagementController::class, 'downloadOrderPDF'])
            ->name('order.pdf.download');
        // Dispatch PDF Download
        Route::get('/dispatch/pdf/download/{id}', [DispatchController::class, 'downloadDispatchPDF'])
            ->name('dispatch.pdf.download');

        // Md Raza Changes ends here

        // Top dealer/distriutors report

        Route::post('/reports/top-performers', [ReportController::class, 'getTopPerformers'])->name('get_top_performers.report');
        Route::get('/reports/top-performers', [ReportController::class, 'index'])->name('top_performers.index');
        Route::get('/reports/distributors/{distributor}/contact-persons', [ReportController::class, 'getContactPersons'])->name('distributors.contact_persons');

        Route::get('/reports/top-performers/dealer-orders/{dealerId}', [ReportController::class, 'getDealerOrders'])->name('get_dealer_orders');
        Route::get('/reports/top-performers/details/{type}/{id}', [ReportController::class, 'getPerformerDetails'])->name('reports.getPerformerDetails');

        Route::get('/reports/top-performers/distributor-orders/{distributorId}', [ReportController::class, 'getDistributorOrders'])->name('get_distributor_orders');
        Route::get('/reports/top-performers/distributor-contacts/{distributorId}', [ReportController::class, 'getContactPersons'])->name('get_distributor_contacts');

        Route::get('/reports/top-performers/distributor-teams/{distributorId}', [ReportController::class, 'getDistributorTeams'])->name('get_distributor_teams');

    });


    require __DIR__ . '/auth.php';
