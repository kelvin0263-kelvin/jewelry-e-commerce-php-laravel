<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Models\OrderItem;
use App\Modules\User\Models\User;
use App\Modules\Product\Models\Product;
use Illuminate\Support\Carbon;

class OrderSeeder extends Seeder
{
	public function run(): void
	{
		$faker = \Faker\Factory::create();
		$users = User::where('is_admin', false)->get();
		$products = Product::all();

		if ($users->isEmpty() || $products->isEmpty()) {
			$this->command?->warn('OrderSeeder: requires users and products. Skipping.');
			return;
		}

		$statuses = ['pending', 'shipped', 'delivered', 'completed', 'refund'];
		$paymentMethods = ['credit_card', 'paypal', 'bank_transfer', 'cod'];
		$shippingMethods = ['standard', 'express', 'pickup'];

		for ($i = 0; $i < 80; $i++) {
			$user = $users->random();
			$createdAt = Carbon::now()->subDays(rand(0, 90))->subMinutes(rand(0, 1440));

			// Create order with placeholders; amounts will be updated after items
			$order = Order::create([
				'user_id' => $user->id,
				'subtotal' => 0,
				'discount' => 0,
				'shipping_cost' => 0,
				'total_amount' => 0,
				'promo_code' => rand(0, 4) === 0 ? strtoupper($faker->bothify('PROMO##')) : null,
				'payment_method' => $faker->randomElement($paymentMethods),
				'payment_status' => 'completed',
				'shipping_address' => $faker->streetAddress(),
				'shipping_postal_code' => $faker->postcode(),
				'shipping_method' => $faker->randomElement($shippingMethods),
				'status' => $faker->randomElement($statuses),
				'tracking_number' => rand(0, 2) ? strtoupper($faker->bothify('TRK########')) : null,
				'refund_status' => null,
				'refund_reason' => null,
				'created_at' => $createdAt,
				'updated_at' => $createdAt,
			]);

			$itemCount = rand(1, min(4, $products->count()));
			$selected = $products->random($itemCount);
			$subtotal = 0.0;

			foreach (is_iterable($selected) ? $selected : [$selected] as $product) {
				$quantity = rand(1, 3);
				$unitPrice = (float) ($product->discount_price ?? $product->selling_price ?? $product->price ?? 0);
				$lineTotal = round($unitPrice * $quantity, 2);
				$subtotal += $lineTotal;

				OrderItem::create([
					'order_id' => $order->id,
					'product_id' => $product->id,
					'quantity' => $quantity,
					'price' => $unitPrice,
					'subtotal' => $lineTotal,
					'created_at' => $createdAt,
					'updated_at' => $createdAt,
				]);
			}

			$discount = rand(0, 3) === 0 ? round($subtotal * 0.05, 2) : 0.0;
			$shippingOptions = [0, 5.90, 8.90, 12.00];
			$shipping = (float) $shippingOptions[array_rand($shippingOptions)];
			$total = max(0, round($subtotal - $discount + $shipping, 2));

			$order->update([
				'subtotal' => round($subtotal, 2),
				'discount' => $discount,
				'shipping_cost' => $shipping,
				'total_amount' => $total,
			]);
		}
	}
}
