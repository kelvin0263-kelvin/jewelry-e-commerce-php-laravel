<?php

namespace App\Modules\Product\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Decorators\CustomerProductDecorator;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display customer product listing (only published products from product management)
     */
    public function index(Request $request)
    {
        $query = Product::where('is_visible', true)
                       ->whereNotNull('published_at');

        // Filter by category
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('marketing_description', 'like', '%' . $request->search . '%');
            });
        }

        $products = $query->paginate(20); // 20 products per page
        
        // Decorate products for customer display
        $decoratedProducts = $products->getCollection()->map(function($product) {
            return new CustomerProductDecorator($product);
        });

        $products->setCollection($decoratedProducts);

        // Get available categories for filter
        $categories = Product::where('is_visible', true)
                            ->whereNotNull('published_at')
                            ->distinct()
                            ->pluck('category')
                            ->filter()
                            ->values();

        return view('product::products.index', compact('products', 'categories'));
    }

    /**
     * Display individual product details for customers
     */
    public function show(Product $product)
    {
        // Check if product is published and visible
        if (!$product->is_visible || !$product->published_at) {
            abort(404);
        }

        $decoratedProduct = new CustomerProductDecorator($product);
        
        // Get approved reviews for this product
        $reviews = \App\Review::where('product_id', $product->id)
                             ->where('is_approved', true)
                             ->orderBy('created_at', 'desc')
                             ->get();
        
        return view('product::products.show', compact('decoratedProduct', 'reviews'));
    }

    /**
     * Add product to wishlist (placeholder for future implementation)
     */
    public function addToWishlist(Request $request, Product $product)
    {
        // Validate request data
        $data = $request->validate([
            'quantity' => 'nullable|integer|min:1|max:10',
        ]);

        // TODO: Implement wishlist functionality
        return response()->json(['message' => 'Wishlist functionality coming soon', 'data' => $data]);
    }

    /**
     * Add product to cart (placeholder for future implementation)
     */
    public function addToCart(Request $request, Product $product)
    {
        // Validate request data
        $data = $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
            'size' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
        ]);

        // TODO: Implement cart functionality
        return response()->json(['message' => 'Cart functionality coming soon', 'data' => $data]);
    }

    /**
     * Submit product review (placeholder for future implementation)
     */
    public function submitReview(Request $request, Product $product)
    {
        // Validate review data
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        // TODO: Implement review functionality
        return response()->json(['message' => 'Review functionality coming soon', 'data' => $data]);
    }
}
