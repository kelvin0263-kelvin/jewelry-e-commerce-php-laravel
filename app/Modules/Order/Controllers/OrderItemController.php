<?php

namespace App\Modules\Order\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Order\Models\Order;
use App\Modules\Inventory\Models\InventoryVariation;
use Illuminate\Support\Facades\Auth;

class OrderItemController extends Controller
{
    public function view($orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->with('items.product')
            ->firstOrFail();

        $orderItems = $order->items;

        return view('order::orderitem', compact('order', 'orderItems'));
    }

    public function createItems($order, $cartItems)
    {
        foreach ($cartItems as $item) {
            $unitPrice = $item->product->discount_price ?? $item->product->selling_price;

            // Save the order item
            $order->items()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $unitPrice,
                'subtotal' => $unitPrice * $item->quantity,
            ]);

            // ✅ Reduce stock (check variation first, then inventory)
            if ($item->product->variation) {
                $item->product->variation->decrement('stock', $item->quantity);
            } elseif ($item->product->inventory) {
                $item->product->inventory->decrement('quantity', $item->quantity);
            }
        }
    }

}
