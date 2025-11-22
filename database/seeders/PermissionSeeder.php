<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dashboard
        Permission::firstOrCreate(['name' => 'Dashboard']);

        // Order
        Permission::firstOrCreate(['name' => 'Order-Index']);
        Permission::firstOrCreate(['name' => 'Order-Create']);
        Permission::firstOrCreate(['name' => 'Order-Edit']);
        Permission::firstOrCreate(['name' => 'Order-View']);
        Permission::firstOrCreate(['name' => 'Order-Delete']);
        Permission::firstOrCreate(['name' => 'Order-Approve']);
        Permission::firstOrCreate(['name' => 'Order-DownloadPdf']);
        Permission::firstOrCreate(['name' => 'Order-ChangeStatus']);
        Permission::firstOrCreate(['name' => 'Order-Dispatch']);

        // Dispatch
        Permission::firstOrCreate(['name' => 'Dispatch-Index']);
        Permission::firstOrCreate(['name' => 'Dispatch-Create']);
        Permission::firstOrCreate(['name' => 'Dispatch-View']);
        Permission::firstOrCreate(['name' => 'Dispatch-ExportPdf']);
        Permission::firstOrCreate(['name' => 'Dispatch-Approve']);

        // App User Management
        Permission::firstOrCreate(['name' => 'AppUserMgmt-Index']);
        Permission::firstOrCreate(['name' => 'AppUserMgmt-Edit']);
        Permission::firstOrCreate(['name' => 'AppUserMgmt-View']);
        // Permission::firstOrCreate(['name' => 'AppUserMgmt-Create']);
        // Permission::firstOrCreate(['name' => 'AppUserMgmt-Delete']);
        // Permission::firstOrCreate(['name' => 'AppUserMgmt-InActive']);
        // Permission::firstOrCreate(['name' => 'AppUserMgmt-Active']);

        // Item Name
        // Permission::firstOrCreate(['name' => 'ItemName-Module']);
        // Permission::firstOrCreate(['name' => 'ItemName-Index']);
        
        // Item Basic Price
        Permission::firstOrCreate(['name' => 'ItemBasicPrice-Index']);
        Permission::firstOrCreate(['name' => 'ItemBasicPrice-Create']);
        Permission::firstOrCreate(['name' => 'ItemBasicPrice-Edit']);
        Permission::firstOrCreate(['name' => 'ItemBasicPrice-View']);
        Permission::firstOrCreate(['name' => 'ItemBasicPrice-Approve']);
        Permission::firstOrCreate(['name' => 'ItemBasicPrice-Delete']);
        Permission::firstOrCreate(['name' => 'ItemBasicPrice-Import/Export']);
        
        
        Permission::firstOrCreate(['name' => 'ItemBundle-Index']);
        Permission::firstOrCreate(['name' => 'ItemBundle-Create']);
        Permission::firstOrCreate(['name' => 'ItemBundle-Active']);
        Permission::firstOrCreate(['name' => 'ItemBundle-View']);
        Permission::firstOrCreate(['name' => 'ItemBundle-Edit']);
        Permission::firstOrCreate(['name' => 'ItemBundle-Inactive']);

        // Permission::firstOrCreate(['name' => 'ItemBasicPrice-ApproveAll']);

        // Item Size
        Permission::firstOrCreate(['name' => 'ItemSize-Index']);
        Permission::firstOrCreate(['name' => 'ItemSize-Create']);
        Permission::firstOrCreate(['name' => 'ItemSize-Edit']);
        Permission::firstOrCreate(['name' => 'ItemSize-View']);
        Permission::firstOrCreate(['name' => 'ItemSize-InActive']);
        Permission::firstOrCreate(['name' => 'ItemSize-Active']);
        Permission::firstOrCreate(['name' => 'ItemSize-Approve']);
        // Permission::firstOrCreate(['name' => 'ItemSize-Import/Export']);

        // Item Category
        // Permission::firstOrCreate(['name' => 'ItemCategory-Index']);
        // Permission::firstOrCreate(['name' => 'ItemCategory-Create']);
        // Permission::firstOrCreate(['name' => 'ItemCategory-Edit']);
        // Permission::firstOrCreate(['name' => 'ItemCategory-View']);
        // Permission::firstOrCreate(['name' => 'ItemCategory-InActive']);
        // Permission::firstOrCreate(['name' => 'ItemCategory-Active']);

        // Reports
        // Permission::firstOrCreate(['name' => 'Report-Module']);
        // Permission::firstOrCreate(['name' => 'Reports-Index']);
        Permission::firstOrCreate(['name' => 'OrderReport-Index']);
        Permission::firstOrCreate(['name' => 'DispatchReport-Index']);
        Permission::firstOrCreate(['name' => 'ItemPriceReport-Index']);
        // Permission::firstOrCreate(['name' => 'ItemSizeReport-Index']);
        Permission::firstOrCreate(['name' => 'ItemSizeReport-Index']);
        Permission::firstOrCreate(['name' => 'DistributorTeamReport-Index']);
        Permission::firstOrCreate(['name' => 'DistributorReport-Index']);
        Permission::firstOrCreate(['name' => 'DealerReport-Index']);
        Permission::firstOrCreate(['name' => 'TopPerformerReport-Index']);

        // User Management
        Permission::firstOrCreate(['name' => 'UserManagement-Index']);
        Permission::firstOrCreate(['name' => 'UserManagement-Create']);
        Permission::firstOrCreate(['name' => 'UserManagement-Edit']);
        Permission::firstOrCreate(['name' => 'UserManagement-View']);
        Permission::firstOrCreate(['name' => 'UserManagement-InActive']);
        Permission::firstOrCreate(['name' => 'UserManagement-Active']);

        // Access Management
        Permission::firstOrCreate(['name' => 'AccessManagement-Index']);
        Permission::firstOrCreate(['name' => 'AccessManagement-Create']);
        Permission::firstOrCreate(['name' => 'AccessManagement-Edit']);
        Permission::firstOrCreate(['name' => 'AccessManagement-View']);
        Permission::firstOrCreate(['name' => 'AccessManagement-Delete']);

        // Company & Email
        Permission::firstOrCreate(['name' => 'Company-Index']);
        Permission::firstOrCreate(['name' => 'Company-Update']);
        // Permission::firstOrCreate(['name' => 'Email-Index']);
        // Permission::firstOrCreate(['name' => 'Email-Edit']);

        // GST
        // Permission::firstOrCreate(['name' => 'GST-Index']);
        // Permission::firstOrCreate(['name' => 'GST-Create']);
        // Permission::firstOrCreate(['name' => 'GST-Edit']);
        // Permission::firstOrCreate(['name' => 'GST-View']);
        // Permission::firstOrCreate(['name' => 'GST-InActive']);
        // Permission::firstOrCreate(['name' => 'GST-Active']);

        // Dealer
        // Permission::firstOrCreate(['name' => 'DealerDistributor-Module']);
        Permission::firstOrCreate(['name' => 'Dealers-Index']);
        Permission::firstOrCreate(['name' => 'Dealers-Create']);
        Permission::firstOrCreate(['name' => 'Dealers-Edit']);
        Permission::firstOrCreate(['name' => 'Dealers-View']);
        Permission::firstOrCreate(['name' => 'Dealers-InActive']);
        Permission::firstOrCreate(['name' => 'Dealers-Active']);
        Permission::firstOrCreate(['name' => 'Dealers-OrderLimitChange']);
        Permission::firstOrCreate(['name' => 'Dealers-OrderLimitRequests']);
        Permission::firstOrCreate(['name' => 'Dealers-Approve']);

        // Distrubutors
        Permission::firstOrCreate(['name' => 'Distributors-Index']);
        Permission::firstOrCreate(['name' => 'Distributors-Create']);
        Permission::firstOrCreate(['name' => 'Distributors-Edit']);
        Permission::firstOrCreate(['name' => 'Distributors-View']);
        Permission::firstOrCreate(['name' => 'Distributors-InActive']);
        Permission::firstOrCreate(['name' => 'Distributors-Active']);
        Permission::firstOrCreate(['name' => 'Distributors-OrderLimitChange']);
        Permission::firstOrCreate(['name' => 'Distributors-OrderLimitRequests']);

        // Distrubutor Team
        Permission::firstOrCreate(['name' => 'DistributorsTeam-Index']);
        Permission::firstOrCreate(['name' => 'DistributorsTeam-Create']);
        Permission::firstOrCreate(['name' => 'DistributorsTeam-Edit']);
        Permission::firstOrCreate(['name' => 'DistributorsTeam-View']);
        Permission::firstOrCreate(['name' => 'DistributorsTeam-Suspend']);

        // Dispatch
        // Permission::firstOrCreate(['name' => 'Dispatch-Module']);
        Permission::firstOrCreate(['name' => 'Dispatch-Index']);
        Permission::firstOrCreate(['name' => 'Dispatch-Create']);
        Permission::firstOrCreate(['name' => 'Dispatch-Edit']);
        Permission::firstOrCreate(['name' => 'Dispatch-View']);
        Permission::firstOrCreate(['name' => 'Dispatch-InActive']);
        Permission::firstOrCreate(['name' => 'Dispatch-Active']);
        
        
        Permission::firstOrCreate(['name' => 'Warehouse-Index']);
        Permission::firstOrCreate(['name' => 'Warehouse-Create']);
        Permission::firstOrCreate(['name' => 'Warehouse-Edit']);
        // Permission::firstOrCreate(['name' => 'Warehouse-Delete']);
        Permission::firstOrCreate(['name' => 'Warehouse-View']);


        // Permission::firstOrCreate(['name' => 'LoadingPoint-Index']);
        // Permission::firstOrCreate(['name' => 'LoadingPoint-Create']);
        // Permission::firstOrCreate(['name' => 'LoadingPoint-View']);
        // Permission::firstOrCreate(['name' => 'LoadingPoint-Edit']);
        // Permission::firstOrCreate(['name' => 'LoadingPoint-Delete']);



        $admin = Role::where('name', 'Admin')->first();
        $operator = Role::where('name', 'Operator')->first();

        $superAdmin = Role::where('name', 'Super Admin')->first();
        $permissions = Permission::all();

        $superAdmin->syncPermissions($permissions);


        // $salesman = Role::where('name', 'Salesman')->first();
        // $salesman->syncPermissions([
        //     'Dashboard',
        //     'Order-Index',
        //     'Order-Create',
        //     'Order-Edit',
        //     'Order-View',
        //     'Order-DownloadPdf',
        //     // 'DealerDistributor-Module',
        //     'Dealers-Index',
        //     'Dealers-View',
        //     'Distributors-Index',
        //     'Distributors-View',
        //     'DistributorsTeam-Index',
        //     'DistributorsTeam-View',
        //     // 'Report-Module',
        //     'OrderReport-Index',
        //     'DealerReport-Index',
        //     'DistributorReport-Index',
        // ]);

        // D. Accountant permissions
        // $accountant = Role::where('name', 'Accountant')->first();
        // $accountant->syncPermissions([
        //     'Dashboard',
        //     'Order-Index',
        //     'Order-View',
        //     'Order-DownloadPdf',
        //     // 'ItemName-Module',
        //     'ItemBasicPrice-Index',
        //     'ItemBasicPrice-Create',
        //     'ItemBasicPrice-Edit',
        //     'ItemBasicPrice-View',
        //     'ItemBasicPrice-Approve',
        //     // 'Report-Module',
        //     'OrderReport-Index',
        //     'ItemPriceReport-Index',
        // ]);

        // // E. Dispatch Incharge permissions
        // $dispatchIncharge = Role::where('name', 'Dispatch Incharge')->first();
        // $dispatchIncharge->syncPermissions([
        //     'Dashboard',
        //     'Order-Index',
        //     'Order-View',
        //     'Order-Dispatch',
        //     'Dispatch-Index',
        //     'Dispatch-Create',
        //     'Dispatch-View',
        //     'Dispatch-ExportPdf',
        //     'Dispatch-Approve',
        //     // 'Report-Module',
        //     'DispatchReport-Index',
        // ]);

        // F. Distributor permissions (for app usage)
        // $distributor = Role::where('name', 'Distributor')->first();
        // $distributor->syncPermissions([
        //     'Dashboard',
        //     'Order-Index',
        //     'Order-Create',
        //     'Order-View',
        //     'Dealers-Index',
        //     'Dealers-View',
        //     'Distributors-View',
        //     'Distributors-Edit',
        //     'Distributors-OrderLimitChange',
        //     // 'Report-Module',
        //     'OrderReport-Index',
        //     'DealerReport-Index',
        // ]);

        // G. Dealer permissions (for app usage)
        // $dealer = Role::where('name', 'Dealer')->first();
        // $dealer->syncPermissions([
        //     'Dashboard',
        //     'Order-Index',
        //     'Order-Create',
        //     'Order-View',
        //     'Dealers-View',
        //     'Dealers-Edit',
        //     'Dealers-OrderLimitChange',
        //     // 'Report-Module',
        //     'OrderReport-Index',
        // ]);


        $admin->givePermissionTo([

            'Dashboard',

            'Order-Index',
            'Order-Create',
            'Order-Edit',
            'Order-View',
            // 'Order-Delete',
            'Order-Approve',
            'Order-DownloadPdf',
            'Order-ChangeStatus',
            // 'Order-Dispatch',


            // Dealers
            'Dealers-Index',
            // 'Dispatch-Module',
            // 'Dealers-Create',
            // 'Dealers-Edit',
            // 'Dealers-View',
            // 'Dealers-InActive',
            // 'Dealers-Active',

            // Distributors
            // 'DealerDistributor-Module',
            'Distributors-Index',
            'Distributors-Create',
            'Distributors-Edit',
            // 'Distributors-View',
            // 'Distributors-InActive',
            // 'Distributors-Active',

            // Distributors Team
            'DistributorsTeam-Index',
            'DistributorsTeam-Create',
            'DistributorsTeam-Edit',
            // 'DistributorsTeam-View',
            // 'DistributorsTeam-InActive',
            // 'DistributorsTeam-Active',

            // 'Dispatch-Index',
            // 'Dispatch-Create',
            // 'Dispatch-Direct-Edit',
            // 'Dispatch-Edit',
            // 'Dispatch-View',
            // 'Dispatch-Delete',
            // 'Dispatch-Approve',

            // 'AppUserMgmt-Index',
            // 'AppUserMgmt-Create',
            // 'AppUserMgmt-Edit',
            // 'AppUserMgmt-View',
            // 'AppUserMgmt-Delete',
            // 'AppUserMgmt-InActive',
            // 'AppUserMgmt-Active',

            // 'ItemName-Module',
            'ItemBasicPrice-Index',
            'ItemBasicPrice-Create',
            'ItemBasicPrice-Edit',
            'ItemBasicPrice-View',
            'ItemBasicPrice-Delete',
            'ItemBasicPrice-Approve',

            'ItemSize-Index',
            'ItemSize-Create',
            'ItemSize-Edit',
            'ItemSize-View',
            'ItemSize-InActive',
            'ItemSize-Active',
            // 'ItemSize-Import/Export',

            // 'ItemCategory-Index',
            // 'ItemCategory-Create',
            // 'ItemCategory-Edit',
            // 'ItemCategory-View',
            // 'ItemCategory-InActive',
            // 'ItemCategory-Active',

            // 'Report-Module',
            'OrderReport-Index',
            'DispatchReport-Index',
            'ItemPriceReport-Index',
            'DistributorTeamReport-Index',
            'DistributorReport-Index',
            'DealerReport-Index',

            'UserManagement-Index',
            'UserManagement-Create',
            // 'UserManagement-Edit',
            // 'UserManagement-View',
            // 'UserManagement-InActive',
            // 'UserManagement-Active',

            // 'AccessManagement-Index',
            // 'AccessManagement-Create',
            // 'AccessManagement-Edit',
            // 'AccessManagement-View',
            // 'AccessManagement-Delete',

            'Company-Index',
            // 'Email-Index',

            // 'GST-Index',
            // 'GST-Create',
            // 'GST-Edit',
            // 'GST-View',
            // 'GST-InActive',
            // 'GST-Active',
        ]);
    }
}
