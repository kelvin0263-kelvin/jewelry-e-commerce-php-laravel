<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SimpleUsersSeeder::class,
            InventoryAndProductsSeeder::class,
            // OrderSeeder::class, // Enable after adding the order_product pivot migration
        ]);
    }
}
