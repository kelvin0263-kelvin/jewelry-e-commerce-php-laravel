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
            KnowledgeBaseSeeder::class,
            // InventoryAndProductsSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class

        ]);
    }
}
