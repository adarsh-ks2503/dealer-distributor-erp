<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define roles from your image
        $roles = [
            'Super Admin',
            'Admin',
            // 'Salesman',
            // 'Accountant',
            // 'Dispatch Incharge',
            // 'Dealer',
            // 'Distributor',
        ];

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $permissions = Permission::pluck('id')->all();
            $role->syncPermissions($permissions);
        }

        // 3. Define dummy users for each role
        $users = [
            [
                'name' => 'Super',
                'last_name' => 'Admin',
                'mobile' => '9876543210',
                'email' => 'yashams.singhal@gmail.com',
                'role' => 'Super Admin'
            ]
        ];

        // 4. Create Users and Assign Roles
        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']], // Unique key to find the user
                [
                    'name' => $userData['name'],
                    'last_name' => $userData['last_name'],
                    'mobile' => $userData['mobile'],
                    'user_type' => 'web',
                    'address' => 'India',
                    'password' => Hash::make('Success2025$')
                ]
            );
            $user->assignRole($userData['role']);
        }
    }
}
