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
    // Show cart items
    public function index()
    {
        $cartItems = CartItem::where('user_id', Auth::id())->with('product')->get();
        return view('cart::cart', compact('cartItems'));  // loads Views/cart.blade.php



    }

    // Add product to cart
    public function add(Request $request, $productId = null)
    {
        // Load product + relations we'll need
        $product = Product::with(['variation', 'inventory.variations'])->findOrFail($productId);

        // Determine available stock (variation first, then inventory)
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

        // Find existing cart item
        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        $currentQty = $cartItem ? (int) $cartItem->quantity : 0;
        $newQty = $currentQty + 1;

        // If nothing available
        if ($availableStock <= 0) {
            return redirect()->route('products.index')
                ->withErrors(['cart' => "Sorry — {$product->name} is out of stock."]);
        }

        // Check against stock
        if ($newQty > $availableStock) {
            return redirect()->route('products.index')
                ->withErrors(['cart' => "Cannot add more than {$availableStock} item(s) of {$product->name} to your cart."]);
        }

        // Safe to add
        if ($cartItem) {
            $cartItem->increment('quantity', 1);
        } else {
            CartItem::create([
                'user_id' => Auth::id(),
                'product_id' => $productId,
                'quantity' => 1,
            ]);
        }

        return redirect()->route('cart.index')->with('success', "{$product->name} added to cart.");
    }

    // Update quantity
    public function update(Request $request, $id)
    {
        $cartItem = CartItem::with('product.variation', 'product.inventory')->findOrFail($id);

        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $newQuantity = (int) $request->input('quantity');
        $product = $cartItem->product;

        if (!$product) {
            return redirect()->route('cart.index')
                ->withErrors(['cart' => 'Product not found.']);
        }

        // ✅ Get available stock (variation-specific or inventory stock)
        if ($product->variation) {
            $availableStock = $product->variation->stock ?? 0;
        } elseif ($product->inventory) {
            $availableStock = $product->inventory->quantity ?? 0;
        } else {
            $availableStock = 0;
        }

        // If quantity is zero, remove the cart item
        if ($newQuantity <= 0) {
            $cartItem->delete();
            return redirect()->route('cart.index')
                ->with('success', "{$product->name} removed from your cart.");
        }

        // Check against available stock
        if ($newQuantity > $availableStock) {
            return redirect()->route('cart.index')
                ->withErrors([
                    'quantity' => "Only {$availableStock} item(s) available for {$product->name}."
                ]);
        }

        // Update
        $cartItem->update(['quantity' => $newQuantity]);

        return redirect()->route('cart.index')->with('success', 'Cart updated successfully.');
    }



    // Remove item from cart
    public function remove($id)
    {
        $cartItem = CartItem::findOrFail($id);
        $cartItem->delete();

        return redirect()->route('cart.index')
            ->with('success', 'Cart items deleted successfully');
    }

    // Clear all cart items
    public function clear()
    {
        CartItem::where('user_id', Auth::id())->delete();
        return redirect()->route('cart.index');
    }

    public function checkout()
    {
        $cartItems = CartItem::where('user_id', Auth::id())->with('product')->get();

        $total = $cartItems->sum(function ($item) {
            $unitPrice = $item->product->discount_price ?? $item->product->selling_price;
            return $unitPrice * $item->quantity;
        });

        return view('cart::checkout', compact('cartItems', 'total')); // loads Views/checkout.blade.php

    }

    public function placeOrder(Request $request)
    {
        $cartItems = CartItem::where('user_id', Auth::id())->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->back()->withErrors(['cart' => 'Your cart is empty.']);
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
            return redirect()->back()->withInput()->withErrors(['promocode' => $e->getMessage()]);
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
            // ⚠️ Encrypt sensitive fields
            $details = [
                'card_number' => Crypt::encryptString($request->card_number),
                'name_on_card' => $request->name_on_card,
                'expiry_date' => $request->expiry_date,
                'cvv' => Crypt::encryptString($request->cvv),
            ];

            // ✅ Save only masked info in DB
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
            return redirect()->back()->withInput()->withErrors(['payment' => $paymentResult['message']]);
        }

        // Save order (⚠️ only save masked info, not raw/encrypted details)
        $orderController = app(\App\Modules\Order\Controllers\OrderController::class);
        $order = $orderController->createOrder(
            $subtotal,
            $discount,
            $shippingCost,
            $total,
            $request,
            $maskedCard // ✅ masked card/account goes to DB
        );

        $orderItemController = app(\App\Modules\Order\Controllers\OrderItemController::class);
        $orderItemController->createItems($order, $cartItems);

        // Clear cart
        CartItem::where('user_id', Auth::id())->delete();

        return redirect('/orders')->with('success', $paymentResult['message'] . " Order placed! Total: RM {$total}");
    }


}
