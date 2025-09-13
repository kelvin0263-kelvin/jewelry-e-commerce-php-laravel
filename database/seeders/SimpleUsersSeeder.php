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


        $admin2 = User::updateOrCreate(
            ['email' => 'admin2@example.com'],
            [
                'name' => 'Admin2',
                'password' => Hash::make('password'),
            ]
        );

        // Direct attribute assignment bypasses mass-assignment restrictions
        $admin2->is_admin = true;
        $admin2->save();

        // Simple customer account
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Customer',
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'user2@example.com'],
            [
                'name' => 'Customer2',
                'password' => Hash::make('password'),
            ]
        );
    }
}
