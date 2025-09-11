<?php

namespace App\Modules\Cart\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Cart\Models\CartItem;
use App\Modules\Product\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Modules\Cart\Strategies\FastDelivery;
use App\Modules\Cart\Strategies\NormalDelivery;
use App\Modules\Cart\Strategies\NoPromocode;
use App\Modules\Cart\Strategies\TenPercentPromocode;
use App\Modules\Cart\Strategies\CreditCardPayment;
use App\Modules\Cart\Strategies\OnlineBankingPayment;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Models\OrderItem;
use Illuminate\Support\Facades\Crypt;

class CartController extends Controller
{
    // GET /api/cart
    public function index()
    {
        $cartItems = CartItem::where('user_id', Auth::id())->with('product')->get();

        return response()->json([
            'success' => true,
            'data' => $cartItems
        ]);
    }

    // POST /api/cart/add/{productId}
    public function add($productId)
    {
        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity');
        } else {
            CartItem::create([
                'user_id' => Auth::id(),
                'product_id' => $productId,
                'quantity' => 1,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully'
        ]);
    }

    // PUT /api/cart/update/{id}
    public function update(Request $request, $id)
    {
        $cartItem = CartItem::with('product.inventory.variations')->findOrFail($id);

        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $newQuantity = (int) $request->input('quantity');
        $product = $cartItem->product;

        if (!$product || !$product->inventory) {
            return response()->json([
                'success' => false,
                'message' => 'Inventory not found for this product'
            ], 404);
        }

        $inventory = $product->inventory;

        $availableStock = $inventory->variations()->exists() 
            ? $inventory->variations->sum('stock') 
            : ($inventory->quantity ?? 0);

        if ($newQuantity <= 0) {
            $cartItem->delete();
            return response()->json([
                'success' => true,
                'message' => "{$product->name} removed from your cart."
            ]);
        }

        if ($newQuantity > $availableStock) {
            return response()->json([
                'success' => false,
                'message' => "Only {$availableStock} item(s) available for {$product->name}."
            ], 422);
        }

        $cartItem->update(['quantity' => $newQuantity]);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'cartItem' => $cartItem
        ]);
    }

    // DELETE /api/cart/remove/{id}
    public function remove($id)
    {
        $cartItem = CartItem::findOrFail($id);
        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart item removed successfully'
        ]);
    }

    // DELETE /api/cart/clear
    public function clear()
    {
        CartItem::where('user_id', Auth::id())->delete();

        return response()->json([
            'success' => true,
            'message' => 'All cart items cleared'
        ]);
    }

    // GET /api/cart/checkout
    public function checkout()
    {
        $cartItems = CartItem::where('user_id', Auth::id())->with('product')->get();

        $total = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

        return response()->json([
            'success' => true,
            'data' => [
                'cartItems' => $cartItems,
                'total' => $total
            ]
        ]);
    }

    // POST /api/cart/place-order
    public function placeOrder(Request $request)
    {
        $cartItems = CartItem::where('user_id', Auth::id())->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty.'
            ], 400);
        }

        $rules = [
            'shipping' => 'required|in:fast,normal',
            'promocode_option' => 'required|in:none,10percent',
            'promocode' => 'nullable|string',
            'payment' => 'required|in:credit_card,online_banking',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|digits:5',
        ];

        if ($request->payment === 'credit_card') {
            $rules = array_merge($rules, [
                'card_number' => 'required|digits:16',
                'name_on_card' => 'required|string|max:100',
                'expiry_date' => 'required|date_format:m/y',
                'cvv' => 'required|digits:3',
            ]);
        } elseif ($request->payment === 'online_banking') {
            $rules = array_merge($rules, [
                'bank_name' => 'required|string|max:100',
                'account_name' => 'required|string|max:100',
                'account_no' => 'required|digits:16',
            ]);
        }

        $validated = $request->validate($rules);

        $subtotal = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

        $shippingStrategy = $request->shipping === 'fast' ? new FastDelivery() : new NormalDelivery();
        $shippingCost = $shippingStrategy->getCost($subtotal);

        $promoStrategy = match ($request->promocode_option) {
            'none' => new NoPromocode(),
            '10percent' => new TenPercentPromocode(),
        };

        try {
            $discount = $promoStrategy->discount($subtotal, $request->promocode);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }

        $total = $subtotal + $shippingCost - $discount;

        $paymentStrategy = match ($request->payment) {
            'credit_card' => new CreditCardPayment(),
            'online_banking' => new OnlineBankingPayment(),
        };

        $details = [];
        $maskedCard = null;

        if ($request->payment === 'credit_card') {
            $details = [
                'card_number' => Crypt::encryptString($request->card_number),
                'name_on_card' => $request->name_on_card,
                'expiry_date' => $request->expiry_date,
                'cvv' => Crypt::encryptString($request->cvv),
            ];
            $last4 = substr($request->card_number, -4);
            $maskedCard = "Credit Card (**** **** **** {$last4})";
        } elseif ($request->payment === 'online_banking') {
            $details = [
                'bank_name' => $request->bank_name,
                'account_name' => $request->account_name,
                'account_no' => Crypt::encryptString($request->account_no),
            ];
            $last4 = substr($request->account_no, -4);
            $maskedCard = "{$request->bank_name} Account (**** **** **** {$last4})";
        }

        $paymentResult = $paymentStrategy->pay($total, $details);

        if ($paymentResult['status'] !== 'success') {
            return response()->json([
                'success' => false,
                'message' => $paymentResult['message']
            ], 400);
        }

        $orderController = app(\App\Modules\Order\Controllers\OrderController::class);
        $order = $orderController->createOrder(
            $subtotal,
            $discount,
            $shippingCost,
            $total,
            $request,
            $maskedCard
        );

        $orderItemController = app(\App\Modules\Order\Controllers\OrderItemController::class);
        $orderItemController->createItems($order, $cartItems);

        CartItem::where('user_id', Auth::id())->delete();

        return response()->json([
            'success' => true,
            'message' => $paymentResult['message'] . " Order placed!",
            'order_total' => $total,
            'order_id' => $order->id
        ]);
    }
}
