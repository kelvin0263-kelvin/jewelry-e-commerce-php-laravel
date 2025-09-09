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

use function PHPUnit\Framework\isEmpty;

class CartController extends Controller
{
    // Show cart items
    public function index()
    {
        $cartItems = CartItem::where('user_id', Auth::id())->with('product')->get();
        return view('cart', compact('cartItems'));


    }

    // Add product to cart
    public function add($productId)
    {
        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity'); // add 1
        } else {
            CartItem::create([
                'user_id' => Auth::id(),
                'product_id' => $productId,
                'quantity' => 1,
            ]);
        }

        return redirect()->route('cart.index');
    }

    // Update quantity
    public function update(Request $request, $id)
    {
        $cartItem = CartItem::findOrFail($id);

        if ($request->quantity <= 0) {
            $cartItem->delete();
        } else {
            $cartItem->update([
                'quantity' => $request->quantity,
            ]);
        }

        return redirect()->route('cart.index');
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

        return view('checkout', compact('cartItems', 'total'));
    }


    public function placeOrder(Request $request)
    {
        $cartItems = CartItem::where('user_id', Auth::id())->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->back()->withErrors(['cart' => 'Your cart is empty.']);
        }

        // Base validation
        $rules = [
            'shipping' => 'required|in:fast,normal',
            'promocode_option' => 'required|in:none,10percent',
            'promocode' => 'nullable|string',
            'payment' => 'required|in:credit_card,online_banking',

            // Shipping address (required for all)
            'address' => 'required|string|max:255',
            'postal_code' => 'required|digits:5',
        ];

        // Add conditional validation rules
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
                'account_no' => 'required|string|max:30',
            ]);
        }

        $validated = $request->validate($rules);

        $subtotal = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

        // Shipping strategy
        $shippingStrategy = $request->shipping === 'fast' ? new FastDelivery() : new NormalDelivery();
        $shippingCost = $shippingStrategy->getCost($subtotal);

        // Promo strategy
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

        /// Choose the payment strategy
        $paymentStrategy = match ($request->payment) {
            'credit_card' => new CreditCardPayment(),
            'online_banking' => new OnlineBankingPayment(),
        };

        // Build the payment details based on type
        $details = [];

        if ($request->payment === 'credit_card') {
            $details = [
                'card_number' => $request->card_number,
                'name_on_card' => $request->name_on_card,
                'expiry_date' => $request->expiry_date,
                'cvv' => $request->cvv,
            ];
        } elseif ($request->payment === 'online_banking') {
            $details = [
                'bank_name' => $request->bank_name,
                'account_name' => $request->account_name,
                'account_no' => $request->account_no,
            ];
        }

        // Call the strategy with correct details
        $paymentResult = $paymentStrategy->pay($total, $details);



        if ($paymentResult['status'] !== 'success') {
            return redirect()->back()->withInput()->withErrors(['payment' => $paymentResult['message']]);
        }

        // Save the order + items 
        $order = Order::create([
            'user_id' => Auth::id(),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'shipping_cost' => $shippingCost,
            'total_amount' => $total,
            'promo_code' => $request->promocode,
            'payment_method' => $request->payment,
            'payment_status' => 'completed',
            'shipping_address' => $request->address,
            'shipping_postal_code' => $request->postal_code,
            'shipping_method' => $request->shipping,
            'status' => 'pending',
        ]);

        foreach ($cartItems as $item) {
            $order->items()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
                'subtotal' => $item->product->price * $item->quantity,
            ]);
        }

        // Clear cart
        //CartItem::where('user_id', Auth::id())->delete();

        // 5️⃣ Redirect to dashboard with success
        return redirect('/dashboard')->with('success', $paymentResult['message'] . " Order placed! Total: RM {$total}");
    }


}
