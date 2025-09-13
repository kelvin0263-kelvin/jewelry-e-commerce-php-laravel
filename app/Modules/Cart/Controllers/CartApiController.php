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

class CartApiController extends Controller
{
    /**
     * Get all cart items for the authenticated user
     */
    public function index()
    {
        try {
            $cartItems = CartItem::where('user_id', Auth::id())
                ->with([
                    'product' => function ($query) {
                        $query->with('inventory.variations');
                    }
                ])
                ->get();

            $total = $cartItems->sum(function ($item) {
                $unitPrice = $item->product->discount_price ?? $item->product->selling_price;
                return $unitPrice * $item->quantity;
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'cart_items' => $cartItems,
                    'total' => $total,
                    'item_count' => $cartItems->count()
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve cart items',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add a product to cart
     */
    public function add(Request $request, $productId)
    {
        try {
            $product = Product::with(['variation', 'inventory.variations'])->findOrFail($productId);

            $availableStock = 0;
            if ($product->variation) {
                $availableStock = (int) ($product->variation->stock ?? 0);
            } elseif ($product->inventory) {
                $inventory = $product->inventory;
                if ($inventory->relationLoaded('variations') && $inventory->variations->count() > 0) {
                    $availableStock = (int) $inventory->variations->sum('stock');
                } else {
                    $availableStock = (int) ($inventory->quantity ?? 0);
                }
            }

            $cartItem = CartItem::where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->first();

            $currentQty = $cartItem ? (int) $cartItem->quantity : 0;
            $newQty = $currentQty + 1;

            if ($availableStock <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => "{$product->name} is out of stock."
                ], 400);
            }

            if ($newQty > $availableStock) {
                return response()->json([
                    'success' => false,
                    'message' => "Only {$availableStock} item(s) available for {$product->name}."
                ], 422);
            }

            if ($cartItem) {
                $cartItem->increment('quantity', 1);
                $message = 'Product quantity updated in cart';
            } else {
                CartItem::create([
                    'user_id' => Auth::id(),
                    'product_id' => $productId,
                    'quantity' => 1,
                ]);
                $message = 'Product added to cart successfully';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Update cart item quantity
     */
    public function update(Request $request, $id)
    {
        try {
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

            // Check available stock
            if ($inventory->variations()->exists()) {
                $availableStock = $inventory->variations->sum('stock');
            } else {
                $availableStock = $inventory->quantity ?? 0;
            }

            // If quantity is zero, remove the cart item
            if ($newQuantity <= 0) {
                $cartItem->delete();
                return response()->json([
                    'success' => true,
                    'message' => "{$product->name} removed from your cart"
                ], 200);
            }

            // Check against available stock
            if ($newQuantity > $availableStock) {
                return response()->json([
                    'success' => false,
                    'message' => "Only {$availableStock} item(s) available for {$product->name}"
                ], 400);
            }

            // Update quantity
            $cartItem->update(['quantity' => $newQuantity]);

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully',
                'data' => [
                    'cart_item_id' => $id,
                    'new_quantity' => $newQuantity
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove item from cart
     */
    public function remove($id)
    {
        try {
            $cartItem = CartItem::findOrFail($id);
            $productName = $cartItem->product->name ?? 'Item';
            $cartItem->delete();

            return response()->json([
                'success' => true,
                'message' => "{$productName} removed from cart successfully"
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item from cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear all cart items
     */
    public function clear()
    {
        try {
            $deletedCount = CartItem::where('user_id', Auth::id())->count();
            CartItem::where('user_id', Auth::id())->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully',
                'data' => [
                    'items_removed' => $deletedCount
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get checkout summary
     */
    public function checkout()
    {
        try {
            $cartItems = CartItem::where('user_id', Auth::id())
                ->with([
                    'product' => function ($query) {
                        $query->with('inventory.variations');
                    }
                ])
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your cart is empty'
                ], 400);
            }

            $subtotal = $cartItems->sum(function ($item) {
                $unitPrice = $item->product->discount_price ?? $item->product->selling_price;
                return $unitPrice * $item->quantity;
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'cart_items' => $cartItems,
                    'subtotal' => $subtotal,
                    'item_count' => $cartItems->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get checkout summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Place order
     */
    public function placeOrder(Request $request)
    {
        try {
            $cartItems = CartItem::where('user_id', Auth::id())->with('product')->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your cart is empty'
                ], 400);
            }

            // Validation rules
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

            // Calculate subtotal
            $subtotal = $cartItems->sum(function ($item) {
                $unitPrice = $item->product->discount_price ?? $item->product->selling_price;
                return $unitPrice * $item->quantity;
            });

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
                ], 400);
            }

            $total = $subtotal + $shippingCost - $discount;

            // Payment Strategy
            $paymentStrategy = match ($request->payment) {
                'credit_card' => new CreditCardPayment(),
                'online_banking' => new OnlineBankingPayment(),
            };

            // Build safe payment details
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

            // Call the strategy
            $paymentResult = $paymentStrategy->pay($total, $details);

            if ($paymentResult['status'] !== 'success') {
                return response()->json([
                    'success' => false,
                    'message' => $paymentResult['message']
                ], 400);
            }

            // Save order
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

            // Clear cart
            CartItem::where('user_id', Auth::id())->delete();

            return response()->json([
                'success' => true,
                'message' => $paymentResult['message'] . " Order placed! Total: RM {$total}",
                'data' => [
                    'order_id' => $order->id,
                    'total' => $total,
                    'subtotal' => $subtotal,
                    'shipping_cost' => $shippingCost,
                    'discount' => $discount,
                    'payment_method' => $maskedCard
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

