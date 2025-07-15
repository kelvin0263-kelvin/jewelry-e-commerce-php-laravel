<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Carbon;

class OrderSeeder extends Seeder
{
     public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        // Check if there are enough users and products to proceed
        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->info('Cannot seed orders. Please make sure you have created users and products.');
            return;
        }

        $availableProductsCount = $products->count();

        for ($i = 0; $i < 50; $i++) {
            $order = Order::create([
                'user_id' => $users->random()->id,
                'total_amount' => 0, // We'll calculate this later
                'created_at' => Carbon::now()->subDays(rand(0, 365)),
            ]);

            $totalAmount = 0;
            
            // THE FIX IS HERE:
            // Never try to pick more products than are available.
            // We pick a random number between 1 and either 3 or the total number of products, whichever is smaller.
            $productCount = rand(1, min(3, $availableProductsCount));

            $selectedProducts = $products->random($productCount);

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 2);
                $price = $product->price;
                $order->products()->attach($product->id, ['quantity' => $quantity, 'price' => $price]);
                $totalAmount += $price * $quantity;
            }

            $order->update(['total_amount' => $totalAmount]);
        }
    }
}
