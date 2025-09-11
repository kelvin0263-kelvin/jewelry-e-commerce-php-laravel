<?php

namespace App\Modules\Product\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Decorators\CustomerProductDecorator;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Check if product is published (AJAX endpoint)
     */
    public function checkPublished($inventoryId)
    {
        $inventory = \App\Modules\Inventory\Models\Inventory::with(['variations.product'])
            ->findOrFail($inventoryId);

        // Get all published products for this inventory
        $publishedProducts = $inventory->variations()
            ->whereHas('product', function($query) {
                $query->where('is_visible', true)
                      ->whereNotNull('published_at');
            })
            ->with('product')
            ->get();

        if ($publishedProducts->isEmpty()) {
            return response()->json([
                'published' => false,
                'message' => 'Product haven\'t been published'
            ]);
        }

        return response()->json([
            'published' => true,
            'url' => route('products.show', $inventoryId)
        ]);
    }

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

        // Group by product name to ensure only one product per name is shown
        $products = $query->get()->groupBy('name')->map(function($productGroup) {
            // Return the first product from each group (first SKU)
            return $productGroup->first();
        })->values();
        
        // Create pagination manually since we're grouping
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $items = $products->slice($offset, $perPage);
        
        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $products->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );
        
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
     * Handles both direct product access and inventory-based access with SKU selection
     */
    public function show($productOrInventoryId)
    {
        // Check if it's a numeric ID (could be product ID or inventory ID)
        if (is_numeric($productOrInventoryId)) {
            // First try to find as inventory ID (since admin links use inventory IDs)
            $inventory = \App\Modules\Inventory\Models\Inventory::find($productOrInventoryId);
            
            if ($inventory) {
                // It's an inventory ID, show inventory with SKU selection
                return $this->showInventoryWithSKUSelection($productOrInventoryId);
            } else {
                // Try to find as product ID
                $product = Product::find($productOrInventoryId);
                if ($product) {
                    return $this->showProduct($product);
                } else {
                    abort(404, 'Product or inventory not found');
                }
            }
        } else {
            // It's a product model instance
            return $this->showProduct($productOrInventoryId);
        }
    }

    /**
     * Display individual product details for customers (existing logic)
     */
    private function showProduct(Product $product)
    {
        // Check if product is published and visible
        if (!$product->is_visible || !$product->published_at) {
            abort(404);
        }

        $decoratedProduct = new CustomerProductDecorator($product);
        
        // Get approved reviews for this product
        $reviews = \App\Modules\Product\Models\Review::where('product_id', $product->id)
                             ->where('is_approved', true)
                             ->orderBy('created_at', 'desc')
                             ->get();
        
        // Get the inventory for this product to show SKU selection
        $inventory = null;
        $allVariations = collect();
        
        if ($product->inventory_id) {
            // Product has direct inventory relationship
            $inventory = \App\Modules\Inventory\Models\Inventory::with(['variations.product'])
                ->find($product->inventory_id);
        } elseif ($product->variation) {
            // Product is linked to a variation
            $inventory = $product->variation->inventory;
            $inventory->load(['variations.product']);
        }
        
        if ($inventory) {
            // Get all published products for this inventory
            $publishedVariations = $inventory->variations()
                ->whereHas('product', function($query) {
                    $query->where('is_visible', true)
                          ->whereNotNull('published_at');
                })
                ->with('product')
                ->get();

            // Prepare all variations for SKU selection
            $allVariations = $publishedVariations->map(function($variation) {
                $decoratedProduct = new CustomerProductDecorator($variation->product);
                $productData = $decoratedProduct->getDecoratedData();
                
                // Create features from variation attributes
                $variationFeatures = [];
                if ($variation->color) {
                    $color = is_array($variation->color) ? implode(', ', $variation->color) : $variation->color;
                    $variationFeatures[] = "Color: {$color}";
                }
                if ($variation->material) {
                    $material = is_array($variation->material) ? implode(', ', $variation->material) : $variation->material;
                    $variationFeatures[] = "Material: {$material}";
                }
                if ($variation->size) {
                    $size = is_array($variation->size) ? implode(', ', $variation->size) : $variation->size;
                    $variationFeatures[] = "Size: {$size}";
                }
                // Skip properties - not needed for display
                
                // Merge product features with variation features
                $existingFeatures = $productData['features'] ?? [];
                if (is_array($existingFeatures)) {
                    $allFeatures = array_merge($existingFeatures, $variationFeatures);
                } else {
                    $allFeatures = $variationFeatures;
                }
                
                return [
                    'id' => $variation->id,
                    'sku' => $variation->sku,
                    'color' => $variation->color,
                    'material' => $variation->material,
                    'stock' => $variation->stock,
                    'product' => $decoratedProduct,
                    'productData' => array_merge($productData, ['features' => $allFeatures])
                ];
            });
        }
        
        return view('product::products.show', compact(
            'decoratedProduct', 
            'reviews', 
            'inventory',
            'allVariations'
        ));
    }

    /**
     * Display inventory with SKU selection for customers
     */
    private function showInventoryWithSKUSelection($inventoryId)
    {
        $inventory = \App\Modules\Inventory\Models\Inventory::with(['variations.product'])
            ->findOrFail($inventoryId);

        // Get all published products for this inventory
        $publishedVariations = $inventory->variations()
            ->whereHas('product', function($query) {
                $query->where('is_visible', true)
                      ->whereNotNull('published_at');
            })
            ->with('product')
            ->get();

        if ($publishedVariations->isEmpty()) {
            // Return JavaScript alert and redirect back
            return response('<script>alert("Product haven\'t been published"); window.history.back();</script>');
        }

        // Get the first published product as default
        $firstProduct = $publishedVariations->first()->product;
        $decoratedProduct = new CustomerProductDecorator($firstProduct);

        // Get approved reviews for the first product
        $reviews = \App\Modules\Product\Models\Review::where('product_id', $firstProduct->id)
                             ->where('is_approved', true)
                             ->orderBy('created_at', 'desc')
                             ->get();

        // Prepare all variations for SKU selection
        $allVariations = $publishedVariations->map(function($variation) {
            $decoratedProduct = new CustomerProductDecorator($variation->product);
            $productData = $decoratedProduct->getDecoratedData();
            
            // Create features from variation attributes
            $variationFeatures = [];
            if ($variation->color) {
                $color = is_array($variation->color) ? implode(', ', $variation->color) : $variation->color;
                $variationFeatures[] = "Color: {$color}";
            }
            if ($variation->material) {
                $material = is_array($variation->material) ? implode(', ', $variation->material) : $variation->material;
                $variationFeatures[] = "Material: {$material}";
            }
            if ($variation->size) {
                $size = is_array($variation->size) ? implode(', ', $variation->size) : $variation->size;
                $variationFeatures[] = "Size: {$size}";
            }
            // Skip properties - not needed for display
            
            // Merge product features with variation features
            $existingFeatures = $productData['features'] ?? [];
            if (is_array($existingFeatures)) {
                $allFeatures = array_merge($existingFeatures, $variationFeatures);
            } else {
                $allFeatures = $variationFeatures;
            }
            
            return [
                'id' => $variation->id,
                'sku' => $variation->sku,
                'color' => $variation->color,
                'material' => $variation->material,
                'stock' => $variation->stock,
                'product' => $decoratedProduct,
                'productData' => array_merge($productData, ['features' => $allFeatures])
            ];
        });

        return view('product::products.show', compact(
            'decoratedProduct', 
            'reviews', 
            'inventory', 
            'allVariations'
        ));
    }

    /**
     * Build features array from variation data
     */
    private function buildFeaturesArray($variation)
    {
        $features = [];
        if ($variation->color) {
            $features[] = "Color: " . $variation->color;
        }
        if ($variation->size) {
            $features[] = "Size: " . $variation->size;
        }
        if ($variation->material) {
            $features[] = "Material: " . $variation->material;
        }
        return $features;
    }

    /**
     * Get SKU details for AJAX requests
     */
    public function getSkuDetails(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id'
        ]);

        $product = Product::with('variation')->findOrFail($request->product_id);
        
        if (!$product->is_visible || !$product->published_at) {
            return response()->json(['error' => 'Product not available'], 404);
        }

        $decoratedProduct = new CustomerProductDecorator($product);
        $data = $decoratedProduct->getDecoratedData();

        return response()->json([
            'product_id' => $product->id,
            'sku' => $product->sku,
            'price' => $product->price,
            'discount_price' => $product->discount_price,
            'stock' => $product->variation->stock ?? 0,
            'features' => $data['features'],
            'images' => $data['gallery_images'],
            'main_image' => $data['main_image']
        ]);
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
