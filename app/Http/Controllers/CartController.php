<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cart;
use App\Modules\Product\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = $this->getCartItems();
        $total = $cartItems->sum(function($item) {
            return $item->quantity * $item->price;
        });
        
        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:99'
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity;
        $price = $product->discount_price ?? $product->price;

        // Check if item already exists in cart
        $existingItem = $this->getExistingCartItem($request->product_id);
        
        if ($existingItem) {
            // Update quantity
            $existingItem->quantity += $quantity;
            $existingItem->save();
        } else {
            // Create new cart item
            Cart::create([
                'session_id' => Auth::check() ? null : Session::getId(),
                'user_id' => Auth::check() ? Auth::id() : null,
                'product_id' => $request->product_id,
                'quantity' => $quantity,
                'price' => $price
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully!',
            'cart_count' => $this->getCartCount()
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:99'
        ]);

        $cartItem = $this->getCartItemById($id);
        
        if (!$cartItem) {
            return response()->json(['success' => false, 'message' => 'Cart item not found'], 404);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully!',
            'cart_count' => $this->getCartCount()
        ]);
    }

    public function remove($id)
    {
        $cartItem = $this->getCartItemById($id);
        
        if (!$cartItem) {
            return response()->json(['success' => false, 'message' => 'Cart item not found'], 404);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart!',
            'cart_count' => $this->getCartCount()
        ]);
    }

    public function checkout()
    {
        $cartItems = $this->getCartItems();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        $total = $cartItems->sum(function($item) {
            return $item->quantity * $item->price;
        });

        return view('checkout.index', compact('cartItems', 'total'));
    }

    private function getCartItems()
    {
        if (Auth::check()) {
            return Cart::with('product')->where('user_id', Auth::id())->get();
        } else {
            return Cart::with('product')->where('session_id', Session::getId())->get();
        }
    }

    private function getExistingCartItem($productId)
    {
        if (Auth::check()) {
            return Cart::where('user_id', Auth::id())->where('product_id', $productId)->first();
        } else {
            return Cart::where('session_id', Session::getId())->where('product_id', $productId)->first();
        }
    }

    private function getCartItemById($id)
    {
        if (Auth::check()) {
            return Cart::where('user_id', Auth::id())->where('id', $id)->first();
        } else {
            return Cart::where('session_id', Session::getId())->where('id', $id)->first();
        }
    }

    private function getCartCount()
    {
        if (Auth::check()) {
            return Cart::where('user_id', Auth::id())->sum('quantity');
        } else {
            return Cart::where('session_id', Session::getId())->sum('quantity');
        }
    }
}
