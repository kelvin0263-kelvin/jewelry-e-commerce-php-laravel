<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Modules\User\Models\User;

class SimpleUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );

        // Direct attribute assignment bypasses mass-assignment restrictions
        $admin->is_admin = true;
        $admin->save();

        // Simple customer account
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Customer',
                'password' => Hash::make('password'),
            ]
        );
    }
}

