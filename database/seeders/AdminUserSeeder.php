<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // TSSU Admin Account
        User::updateOrCreate(
            ['email' => 'tssu.admin@dnsc.edu.ph'],
            [
                'name' => 'TSSU Admin',
                'first_name' => 'TSSU',
                'last_name' => 'Admin',
                'password' => Hash::make('Password123'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        // Director Account (Charlo)
        User::updateOrCreate(
            ['email' => 'director@dnsc.edu.ph'],
            [
                'name' => 'Charlo (Director)',
                'first_name' => 'Charlo',
                'last_name' => 'Director',
                'password' => Hash::make('Password123'),
                'role' => User::ROLE_DIRECTOR,
            ]
        );

        // Manager Account
        User::updateOrCreate(
            ['email' => 'manager@dnsc.edu.ph'],
            [
                'name' => 'Manager',
                'first_name' => 'Manager',
                'last_name' => 'Account',
                'password' => Hash::make('Password123'),
                'role' => User::ROLE_MANAGER,
            ]
        );

        // Staff Account
        User::updateOrCreate(
            ['email' => 'staff@dnsc.edu.ph'],
            [
                'name' => 'James (Staff)',
                'first_name' => 'James',
                'last_name' => 'Staff',
                'password' => Hash::make('password'),
                'role' => User::ROLE_STAFF,
            ]
        );
    }
}
