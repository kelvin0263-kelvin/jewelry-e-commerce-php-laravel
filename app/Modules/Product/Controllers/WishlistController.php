<?php

namespace App\Modules\Product\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Product\Models\Wishlist;
use App\Modules\Product\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlistItems = $this->getWishlistItems();
        
        return view('wishlist.index', compact('wishlistItems'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        // Check if item already exists in wishlist
        $existingItem = $this->getExistingWishlistItem($request->product_id);
        
        if ($existingItem) {
            return response()->json([
                'success' => false,
                'message' => 'Product is already in your wishlist!'
            ]);
        }

        // Create new wishlist item
        Wishlist::create([
            'session_id' => Auth::check() ? null : Session::getId(),
            'user_id' => Auth::check() ? Auth::id() : null,
            'product_id' => $request->product_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product added to wishlist successfully!',
            'wishlist_count' => $this->getWishlistCount()
        ]);
    }

    public function remove($id)
    {
        $wishlistItem = $this->getWishlistItemById($id);
        
        if (!$wishlistItem) {
            return response()->json(['success' => false, 'message' => 'Wishlist item not found'], 404);
        }

        $wishlistItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from wishlist!',
            'wishlist_count' => $this->getWishlistCount()
        ]);
    }

    private function getWishlistItems()
    {
        if (Auth::check()) {
            return Wishlist::with('product')->where('user_id', Auth::id())->get();
        } else {
            return Wishlist::with('product')->where('session_id', Session::getId())->get();
        }
    }

    private function getExistingWishlistItem($productId)
    {
        if (Auth::check()) {
            return Wishlist::where('user_id', Auth::id())->where('product_id', $productId)->first();
        } else {
            return Wishlist::where('session_id', Session::getId())->where('product_id', $productId)->first();
        }
    }

    private function getWishlistItemById($id)
    {
        if (Auth::check()) {
            return Wishlist::where('user_id', Auth::id())->where('id', $id)->first();
        } else {
            return Wishlist::where('session_id', Session::getId())->where('id', $id)->first();
        }
    }

    private function getWishlistCount()
    {
        if (Auth::check()) {
            return Wishlist::where('user_id', Auth::id())->count();
        } else {
            return Wishlist::where('session_id', Session::getId())->count();
        }
    }
}
