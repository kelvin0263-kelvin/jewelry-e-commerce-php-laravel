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
        // Handle both GET and POST requests
        if ($request->isMethod('post')) {
            $data = $request->validate([
                'product_id' => 'required|integer',
                'quantity' => 'required|integer|min:1',
                'price' => 'nullable|numeric|min:0'
            ]);
            
            $productId = $data['product_id'];
            $quantity = $data['quantity'];
            $price = $data['price'] ?? null;
        } else {
            $quantity = 1;
            $price = null;
        }

        // Get the product with inventory information
        $product = \App\Modules\Product\Models\Product::with('inventory.variations')->find($productId);
        
        if (!$product || !$product->inventory) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Product not found or inventory not available.']);
            }
            return redirect()->back()->withErrors(['cart' => 'Product not found or inventory not available.']);
        }

        $inventory = $product->inventory;

        // Calculate available stock
        $availableStock = 0;
        if ($inventory->variations()->exists()) {
            // If variations exist, sum their stock
            $availableStock = $inventory->variations->sum('stock');
        } else {
            $availableStock = $inventory->quantity ?? 0;
        }

        // Check if product is out of stock
        if ($availableStock <= 0) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'This product is currently out of stock.']);
            }
            return redirect()->back()->withErrors(['cart' => 'This product is currently out of stock.']);
        }

        // Check if requested quantity exceeds available stock
        if ($quantity > $availableStock) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => "Only {$availableStock} item(s) available in stock."]);
            }
            return redirect()->back()->withErrors(['cart' => "Only {$availableStock} item(s) available in stock."]);
        }

        // Check existing cart item
        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            // Check if adding this quantity would exceed stock
            $newTotalQuantity = $cartItem->quantity + $quantity;
            if ($newTotalQuantity > $availableStock) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => "Cannot add {$quantity} more items. Only " . ($availableStock - $cartItem->quantity) . " more available."]);
                }
                return redirect()->back()->withErrors(['cart' => "Cannot add {$quantity} more items. Only " . ($availableStock - $cartItem->quantity) . " more available."]);
            }
            
            $cartItem->increment('quantity', $quantity);
        } else {
            try {
                $finalPrice = $price ?? $product->selling_price ?? $product->price;
                
                if (!$finalPrice || $finalPrice <= 0) {
                    if ($request->expectsJson()) {
                        return response()->json(['success' => false, 'message' => 'Invalid product price.']);
                    }
                    return redirect()->back()->withErrors(['cart' => 'Invalid product price.']);
                }
                
                CartItem::create([
                    'user_id' => Auth::id(),
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $finalPrice,
                ]);
            } catch (\Exception $e) {
                \Log::error('Cart creation error: ' . $e->getMessage());
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Failed to add product to cart. Please try again.']);
                }
                return redirect()->back()->withErrors(['cart' => 'Failed to add product to cart. Please try again.']);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Product added to cart successfully!']);
        }

        return redirect()->route('cart.index')->with('success', 'Product added to cart successfully!');
    }

    // Update quantity
    public function update(Request $request, $id)
    {
        $cartItem = CartItem::with('product.inventory.variations')->findOrFail($id);

        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $newQuantity = (int) $request->input('quantity');
        $product = $cartItem->product;

        if (!$product || !$product->inventory) {
            return redirect()->route('cart.index')
                ->withErrors(['cart' => 'Inventory not found for this product.']);
        }

        $inventory = $product->inventory;

        // ✅ Decide whether to use variation stock or inventory stock
        if ($inventory->variations()->exists()) {
            // If variations exist, sum their stock or pick the relevant variation
            $availableStock = $inventory->variations->sum('stock');
        } else {
            $availableStock = $inventory->quantity ?? 0;
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

        $total = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

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
