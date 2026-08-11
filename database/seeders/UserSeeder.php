<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed initial RBAC user accounts idempotently.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('password123');

        $users = [
            [
                'name' => 'System Super Admin',
                'email' => 'superadmin@dairy.com',
                'role' => 'super_admin',
                'status' => true,
                'password' => $defaultPassword,
            ],
            [
                'name' => 'Dairy Operations Manager',
                'email' => 'manager@dairy.com',
                'role' => 'manager',
                'status' => true,
                'password' => $defaultPassword,
            ],
            [
                'name' => 'Field Collection Staff',
                'email' => 'collection@dairy.com',
                'role' => 'collection_staff',
                'status' => true,
                'password' => $defaultPassword,
            ],
            [
                'name' => 'Main Center Staff',
                'email' => 'center@dairy.com',
                'role' => 'center_staff',
                'status' => true,
                'password' => $defaultPassword,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
