<?php

namespace App\Modules\Product\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Decorators\AdminProductDecorator;
use App\Http\Requests\ProductUpdateRequest;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Inventory\Models\InventoryVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProductManagementController extends Controller
{
    /**
     * Display all products based on inventory variations (SKU-based).
     */
    public function index(Request $request)
    {
        // Check if we're viewing SKU details for a specific inventory
        if ($request->has('inventory_id') && !empty($request->inventory_id)) {
            return $this->showSkuDetails($request->inventory_id);
        }

        // Default: Show inventory summary table
        return $this->showInventorySummary($request);
    }

    /**
     * Show inventory summary table
     */
    private function showInventorySummary(Request $request)
    {
        // Only process messages if this is a fresh visit (not from internal navigation)
        // Check if there's a referer that indicates internal navigation within product management
        $referer = $request->header('referer');
        $isInternalNavigation = $referer && str_contains($referer, 'admin.product-management');
        
        // Clear messages after they are displayed to prevent repeated display
        $messages = [];
        
        // Only process messages if not coming from internal navigation
        if (!$isInternalNavigation) {
            if (session('new_product_added')) {
                $messages['new_product_added'] = session('new_product_added');
                session()->forget('new_product_added');
            }
            if (session('inventory_unpublished')) {
                $messages['inventory_unpublished'] = session('inventory_unpublished');
                session()->forget('inventory_unpublished');
            }
            if (session('inventory_republished')) {
                $messages['inventory_republished'] = session('inventory_republished');
                session()->forget('inventory_republished');
            }
            if (session('inventory_changes')) {
                $messages['inventory_changes'] = session('inventory_changes');
                session()->forget('inventory_changes');
            }
        }

        $query = \App\Modules\Inventory\Models\Inventory::with(['variations.product']);

        // Inventory Summary Page Independent Search functionality
        if ($request->has('search') && $request->search) {
            $sanitizedSearch = $this->sanitizeSearchInput($request->search);
            if (!empty($sanitizedSearch)) {
                $query->where(function($q) use ($sanitizedSearch) {
                    $q->where('name', 'like', '%' . $sanitizedSearch . '%')
                      ->orWhere('type', 'like', '%' . $sanitizedSearch . '%')
                      ->orWhere('description', 'like', '%' . $sanitizedSearch . '%')
                      ->orWhereHas('variations', function($variationQuery) use ($sanitizedSearch) {
                          $variationQuery->where('sku', 'like', '%' . $sanitizedSearch . '%')
                                         ->orWhere('color', 'like', '%' . $sanitizedSearch . '%')
                                         ->orWhere('material', 'like', '%' . $sanitizedSearch . '%');
                      });
                });
                
                // 记录Inventory搜索活动
                $this->logSecurityEvent('Inventory search performed', [
                    'search_term' => $sanitizedSearch,
                    'original_term' => $request->search
                ]);
            }
        }

        // Inventory Summary Page Independent Category filter
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('type', 'like', '%' . $request->category . '%');
        }


        // Show inventories that are either published or were previously published (now draft)
        // This ensures we can show rejected status for unpublished inventories
        $query->where(function($q) {
            $q->where('status', 'published') // Currently published inventories
              ->orWhereHas('variations.product', function($subQ) {
                  $subQ->whereNotNull('published_at'); // Previously published inventories (now draft)
              });
        });

        // Get paginated results
        $inventories = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get issued products (products with status = 'issued' that were previously published and had customer info)
        $issuedProducts = \App\Modules\Product\Models\Product::with(['variation.inventory', 'issuer'])
            ->where('status', 'issued')
            ->whereNotNull('published_at') // Only show products that were previously published
            ->whereNotNull('issued_at') // Only show products that have been issued
            ->where('marketing_description', '!=', 'None') // Only show products that had customer info created
            ->whereNotNull('marketing_description') // Ensure marketing description exists
            ->orderBy('issued_at', 'desc')
            ->get();

        // Transform inventories to summary data
        $inventorySummaries = $inventories->getCollection()->map(function($inventory) {
            $totalStock = $inventory->variations->sum('stock');
            $publishedCount = $inventory->variations->where('product.is_visible', true)
                ->where('product.status', 'published')
                ->count();
            $totalVariations = $inventory->variations->count();
            
            // Check if at least one SKU has user-facing information
            $hasUserFacingInfo = $inventory->variations->where('product.marketing_description', '!=', 'None')
                ->where('product.marketing_description', '!=', null)
                ->count() > 0;
            
            // Get inventory status based on inventory module status and product visibility
            if ($inventory->status === 'draft') {
                // If inventory is unpublished in inventory module, show as rejected
                $status = 'rejected';
            } else {
                // Check if there are any products that are visible but pending or issued (republished products)
                $pendingProducts = $inventory->variations->where('product.is_visible', true)
                    ->whereIn('product.status', ['pending', 'issued'])
                    ->count();
                
                if ($publishedCount > 0 && $pendingProducts === 0) {
                    // If inventory has published products and no pending products
                    $status = 'published';
                } else if ($pendingProducts > 0) {
                    // If there are pending or issued products (republished), show as pending
                    $status = 'pending';
                } else {
                    // If inventory is published but no visible products yet
                    $status = 'pending';
                }
            }
            
            // Get published info from the first published product
            $publishedProduct = $inventory->variations->where('product.is_visible', true)->first()?->product;
            $publishedBy = $publishedProduct?->publisher?->email ?? 'System';
            $publishedAt = $publishedProduct?->published_at;
            
            return (object) [
                'id' => $inventory->id,
                'name' => $inventory->name,
                'type' => $inventory->type,
                'total_stock' => $totalStock,
                'published_count' => $publishedCount,
                'total_variations' => $totalVariations,
                'status' => $status,
                'published_by' => $publishedBy,
                'published_at' => $publishedAt,
                'has_user_facing_info' => $hasUserFacingInfo,
                'inventory' => $inventory,
            ];
        });

        // Create a new paginator with the transformed data
        $inventories = new \Illuminate\Pagination\LengthAwarePaginator(
            $inventorySummaries,
            $inventories->total(),
            $inventories->perPage(),
            $inventories->currentPage(),
            [
                'path' => $inventories->path(),
                'pageName' => $inventories->getPageName(),
            ]
        );

        // Check for inventory changes and add notifications
        if (session('inventory_republished')) {
            $republishedData = session('inventory_republished');
            if (is_array($republishedData)) {
                // Show detailed republished message
                session()->flash('inventory_republished', $republishedData);
            } else {
                // Show general inventory changes message
                $recentInventory = $inventories->getCollection()->first();
                if ($recentInventory && $recentInventory->inventory) {
                    session()->flash('inventory_changes', [
                        'sku' => $recentInventory->inventory->variations->first()?->sku ?? 'N/A',
                        'name' => $recentInventory->name,
                        'updated_at' => $recentInventory->inventory->updated_at->format('M d, Y H:i'),
                        'changes' => $this->getInventoryChanges($recentInventory->inventory)
                    ]);
                }
            }
            // Clear the flag so it only shows once
            session()->forget('inventory_republished');
        }
        
        // Check for inventory unpublish and add notifications
        if (session('inventory_unpublished')) {
            $unpublishedData = session('inventory_unpublished');
            if (is_array($unpublishedData)) {
                // Show detailed unpublished message
                session()->flash('inventory_unpublished', $unpublishedData);
            } else {
                // Show general unpublished message
                $recentInventory = $inventories->getCollection()->first();
                if ($recentInventory && $recentInventory->inventory) {
                    session()->flash('inventory_unpublished', [
                        'sku' => $recentInventory->inventory->variations->first()?->sku ?? 'N/A',
                        'name' => $recentInventory->name,
                        'updated_at' => $recentInventory->inventory->updated_at->format('M d, Y H:i'),
                        'changes' => 'Products have been delisted and marked as issued'
                    ]);
                }
            }
            // Clear the flag so it only shows once
            session()->forget('inventory_unpublished');
        }
        
        // Check for new product added and add notifications
        if (session('new_product_added')) {
            // Find the most recently updated inventory
            $recentInventory = $inventories->getCollection()->first();
            if ($recentInventory && $recentInventory->inventory) {
                session()->flash('new_product_added', [
                    'sku' => $recentInventory->inventory->variations->first()?->sku ?? 'N/A',
                    'name' => $recentInventory->name,
                    'updated_at' => $recentInventory->inventory->updated_at->format('M d, Y H:i'),
                    'changes' => 'New product created and published from inventory module'
                ]);
            }
            // Clear the flag so it only shows once
            session()->forget('new_product_added');
        }

        // Get unique categories for filter
        $categories = \App\Modules\Inventory\Models\Inventory::pluck('type')
            ->unique()
            ->map(function($type) {
                return str_replace('Item', '', strtolower($type));
            })
            ->unique()
            ->sort()
            ->values();

        // Get all reviews with product information
        $reviews = \App\Modules\Product\Models\Review::with('product')
            ->latest()
            ->paginate(10);

        return view('product::admin.product-management.inventory-summary', compact('inventories', 'categories', 'issuedProducts', 'messages', 'reviews'));
    }

    /**
     * Show SKU details for a specific inventory
     */
    public function showSkuDetails($inventoryId)
    {
        $inventory = \App\Modules\Inventory\Models\Inventory::findOrFail($inventoryId);
        
        $query = InventoryVariation::with(['inventory', 'product'])
            ->where('inventory_id', $inventoryId);

        $variations = $query->paginate(15);
        
        // Transform variations to product-like data for display
        $transformedProducts = $variations->getCollection()->map(function($variation) {
            return $this->transformVariationToProductData($variation);
        });

        // Create a new paginator with the transformed data
        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $transformedProducts,
            $variations->total(),
            $variations->perPage(),
            $variations->currentPage(),
            [
                'path' => $variations->path(),
                'pageName' => $variations->getPageName(),
            ]
        );

        // Check for inventory changes and add notifications
        $this->checkInventoryChanges($transformedProducts);

        return view('product::admin.product-management.sku-details', compact('products', 'inventory'));
    }

    /**
     * Publish entire inventory (all SKUs with user-facing info)
     */
    public function publishInventory($inventoryId)
    {
        $inventory = \App\Modules\Inventory\Models\Inventory::with(['variations.product'])->findOrFail($inventoryId);
        
        // Check if at least one SKU has user-facing information
        $skusWithUserFacingInfo = $inventory->variations->where('product.marketing_description', '!=', 'None')
            ->where('product.marketing_description', '!=', null);
            
        if ($skusWithUserFacingInfo->count() == 0) {
            return redirect()->route('admin.product-management.index')
                ->with('error', 'Please create user-facing information for at least one SKU before publishing.');
        }

        // Publish all SKUs that have user-facing information
        $publishedCount = 0;
        foreach ($skusWithUserFacingInfo as $variation) {
            if ($variation->product) {
                $variation->product->update([
                    'status' => 'published',
                    'is_visible' => true,
                    'published_at' => now(),
                    'published_by' => Auth::id(),
                ]);
                $publishedCount++;
            }
        }

        \Log::info('Inventory published', [
            'inventory_id' => $inventory->id,
            'inventory_name' => $inventory->name,
            'published_skus' => $publishedCount,
            'published_by' => Auth::user()->email,
            'published_at' => now(),
            'user_id' => Auth::id()
        ]);

        return redirect()->route('admin.product-management.index')
            ->with('success', "Inventory '{$inventory->name}' published successfully! {$publishedCount} SKU(s) published.");
    }

    /**
     * Unpublish entire inventory (all SKUs)
     */
    public function unpublishInventory($inventoryId)
    {
        $inventory = \App\Modules\Inventory\Models\Inventory::with(['variations.product'])->findOrFail($inventoryId);
        
        // Unpublish all SKUs and mark as issued
        $unpublishedCount = 0;
        foreach ($inventory->variations as $variation) {
            if ($variation->product) {
                // Mark all products as pending (regardless of their current visibility status)
                $variation->product->update([
                    'is_visible' => false,
                    'status' => 'pending',
                    'issued_at' => now(),
                    'issued_by' => Auth::id(),
                ]);
                $unpublishedCount++;
            }
        }

        \Log::info('Inventory unpublished', [
            'inventory_id' => $inventory->id,
            'inventory_name' => $inventory->name,
            'unpublished_skus' => $unpublishedCount,
            'unpublished_by' => Auth::user()->email,
            'unpublished_at' => now(),
            'user_id' => Auth::id()
        ]);

        return redirect()->route('admin.product-management.index')
            ->with('success', "Inventory '{$inventory->name}' unpublished successfully! {$unpublishedCount} SKU(s) marked as issued.");
    }


    /**
     * Check for inventory changes and add notifications.
     */
    private function checkInventoryChanges($products)
    {
        // Only show notification if inventory was recently republished
        // Check if there's a flag indicating inventory was just republished
        if (session('inventory_republished')) {
            foreach ($products as $product) {
                if ($product->inventory) {
                    // Add notification to session
                    session()->flash('inventory_changes', [
                        'sku' => $product->sku,
                        'name' => $product->name,
                        'updated_at' => $product->inventory->updated_at->format('M d, Y H:i'),
                        'changes' => $this->getInventoryChanges($product->inventory)
                    ]);
                    break; // Only show one notification per page
                }
            }
            // Clear the flag so it only shows once
            session()->forget('inventory_republished');
        }
    }

    /**
     * Get inventory changes for notification.
     */
    private function getInventoryChanges($inventory)
    {
        $changes = [];
        
        // Check for common changes
        if ($inventory->wasChanged('name')) {
            $changes[] = 'Product name updated';
        }
        if ($inventory->wasChanged('price')) {
            $changes[] = 'Price updated';
        }
        if ($inventory->wasChanged('description')) {
            $changes[] = 'Description updated';
        }
        if ($inventory->wasChanged('quantity')) {
            $changes[] = 'Quantity updated';
        }
        if ($inventory->wasChanged('status')) {
            $changes[] = 'Status changed to ' . $inventory->status;
        }
        
        return empty($changes) ? ['General inventory updates'] : $changes;
    }

    /**
     * Transform inventory variation to product data for display.
     */
    private function transformVariationToProductData($variation)
    {
        $inventory = $variation->inventory;
        $product = $variation->product; // Now get product directly from variation
        
        // Build features array from variation data
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
        
        // Ensure features is always an array
        $features = $features ?: [];
        
        // Get category from inventory type
        $category = str_replace('Item', '', strtolower($inventory->type));
        
        // Determine completion status
        $isComplete = $product && 
                     $product->is_visible && 
                     $product->marketing_description && 
                     $product->marketing_description !== 'None';
        
        return (object) [
            'id' => $variation->id,
            'sku' => $variation->sku,
            'name' => $inventory->name,
            'price' => $variation->price,
            'selling_price' => $product ? $product->selling_price : null,
            'discount_price' => $product ? $product->discount_price : null,
            'quantity' => $variation->stock,
            'category' => $category,
            'features' => $features,
            'description' => $product ? $product->marketing_description : 'None',
            'status' => $isComplete ? 'complete' : 'incomplete',
            'is_visible' => $product ? $product->is_visible : false,
            'published_at' => $product ? $product->published_at : null,
            'published_by' => $product ? ($product->publisher ? $product->publisher->email : 'System') : 'System',
            'customer_images' => $product ? ($product->customer_images ?? []) : [],
            'product_video' => $product ? $product->product_video : null,
            'variation' => $variation,
            'inventory' => $inventory,
            'product_record' => $product, // Renamed to avoid confusion
        ];
    }

    /**
     * Display the specified product variation.
     */
    public function show($variationId)
    {
        $variation = InventoryVariation::with(['inventory.product.publisher'])->findOrFail($variationId);
        
        // Transform variation to product data
        $product = $this->transformVariationToProductData($variation);
        
        // Check if customer information is created
        if (!$product->product_record || 
            !$product->product_record->marketing_description || 
            $product->product_record->marketing_description === 'None') {
            return redirect()->route('admin.product-management.index')
                ->with('error', 'Please create customer information first before viewing.');
        }
        
        return view('product::admin.product-management.show', compact('product', 'variation'));
    }


    /**
     * Create a new product from inventory variation.
     */
    public function create($variationId)
    {
        $variation = InventoryVariation::with(['inventory', 'product'])->findOrFail($variationId);
        
        // Check if product already exists for this SKU
        if ($variation->product) {
            return redirect()->route('admin.product-management.index')
                ->with('error', 'Product already exists for this SKU.');
        }
        
        // Create product from inventory variation (SKU-specific)
        $product = Product::create([
            'inventory_id' => $variation->inventory->id,
            'inventory_variation_id' => $variation->id,
            'name' => $variation->inventory->name,
            'price' => $variation->price,
            'description' => $variation->inventory->description ?? '',
            'marketing_description' => 'None', // Default as per requirement
            'sku' => $variation->sku,
            'product_id' => $variation->sku, // Use SKU as product ID
            'category' => str_replace('Item', '', strtolower($variation->inventory->type)),
            'status' => 'draft',
            'is_visible' => false,
        ]);
        
        \Log::info('Product created from inventory variation', [
            'product_id' => $product->id,
            'variation_id' => $variation->id,
            'sku' => $variation->sku,
                'user_id' => auth()->id()
            ]);
            
        return redirect()->route('admin.product-management.index')
            ->with('success', 'Product created successfully for SKU: ' . $variation->sku);
    }

    /**
     * Create customer information directly (creates product if not exists)
     */
    public function createInfo($variationId)
    {
        $variation = InventoryVariation::with(['inventory', 'product'])->findOrFail($variationId);

        // If product doesn't exist, create it first
        if (!$variation->product) {
            $product = Product::create([
                'inventory_id' => $variation->inventory->id,
                'inventory_variation_id' => $variation->id,
                'name' => $variation->inventory->name,
                'price' => $variation->price,
                'description' => $variation->inventory->description ?? '',
                'marketing_description' => 'None',
                'sku' => $variation->sku,
                'product_id' => $variation->sku,
                'category' => str_replace('Item', '', strtolower($variation->inventory->type)),
                'status' => 'draft',
                'is_visible' => false,
            ]);

            \Log::info('Product auto-created for customer information', [
                'product_id' => $product->id,
                'variation_id' => $variation->id,
                'sku' => $variation->sku,
                'created_by' => Auth::user()->email,
                'user_id' => Auth::id()
            ]);
            
            // Use the newly created product
            $productToEnhance = $product;
        } else {
            // Use the existing product
            $productToEnhance = $variation->product;
        }

        // Redirect to enhance page to create customer information
        return redirect()->route('admin.product-management.enhance', $productToEnhance);
    }

    /**
     * Show form to enhance a product.
     */
    public function enhance(Product $product)
    {
        // Load the inventory variation and inventory relationship
        $variation = $product->variation;
        
        if (!$variation) {
            return redirect()->route('admin.product-management.index')
                ->with('error', 'No inventory variation found for this product.');
        }
        
        // Transform the variation to product data format
        $transformedProduct = $this->transformVariationToProductData($variation);
        
        return view('product::admin.product-management.enhance', compact('product', 'transformedProduct'));
    }

    /**
     * Store enhanced product details.
     */
    public function storeEnhancement(Request $request, Product $product)
    {
        $data = $request->validate([
            'marketing_description' => 'required|string',
            'selling_price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'customer_images' => 'required|array|min:1|max:5',
            'customer_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'product_video' => 'nullable|file|mimes:mp4,avi,mov|max:10240',
        ]);

        // Handle customer images upload
        if ($request->hasFile('customer_images')) {
            $customerImages = [];
            foreach ($request->file('customer_images') as $image) {
                $customerImages[] = $image->store('products/customer', 'public');
            }
            $data['customer_images'] = $customerImages;
        }

        // Handle product video upload
        if ($request->hasFile('product_video')) {
            // Delete old video
            if ($product->product_video) {
                Storage::disk('public')->delete($product->product_video);
            }
            $data['product_video'] = $request->file('product_video')->store('products/videos', 'public');
        }

        // Update product with enhanced details
        $product->update([
            'marketing_description' => $data['marketing_description'],
            'selling_price' => $data['selling_price'],
            'discount_price' => $data['discount_price'],
            'customer_images' => $data['customer_images'] ?? $product->customer_images,
            'product_video' => $data['product_video'] ?? $product->product_video,
        ]);

        // Redirect back to SKU product info page with inventory_id
        return redirect()->route('admin.product-management.index', ['inventory_id' => $product->inventory_id])
            ->with('success', 'Product enhanced successfully.');
    }

    /**
     * Publish product to customers.
     */
    public function publish(Product $product)
    {
        // Check if customer information is created
        if (!$product->marketing_description || $product->marketing_description === 'None') {
            return redirect()->route('admin.product-management.index')
                ->with('error', 'Please create customer information first before publishing.');
        }

        $product->update([
            'status' => 'published',
            'is_visible' => true,
            'published_at' => now(),
            'published_by' => Auth::id(),
        ]);

        \Log::info('Product published', [
            'product_id' => $product->id,
            'published_by' => Auth::user()->email,
            'published_at' => now(),
            'user_id' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Product published successfully!');
    }

    /**
     * Unpublish product from customers.
     */
    public function unpublish(Product $product)
    {
        $product->update([
            'status' => 'pending',
            'is_visible' => false,
        ]);

        return redirect()->back()->with('success', 'Product unpublished successfully.');
    }

    /**
     * Edit product details.
     */
    public function edit(Product $product)
    {
        // Check if customer information is created
        if (!$product->marketing_description || $product->marketing_description === 'None') {
            return redirect()->route('admin.product-management.index')
                ->with('error', 'Please create customer information first before editing.');
        }

        // Load the inventory variation and inventory relationship
        $variation = $product->variation;
        
        if (!$variation) {
            return redirect()->route('admin.product-management.index')
                ->with('error', 'No inventory variation found for this product.');
        }
        
        // Transform the variation to product data format (same as enhance page)
        $transformedProduct = $this->transformVariationToProductData($variation);

        return view('product::admin.product-management.edit', compact('product', 'transformedProduct'));
    }

    /**
     * Update product details.
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        try {
            \Log::info('=== UPDATE METHOD CALLED ===');
            \Log::info('Product ID: ' . $product->id);
            \Log::info('Product Name: ' . $product->name);
            \Log::info('Request data: ', $request->all());
            
            $data = $request->validated();
            \Log::info('Validated data: ', $data);
            
            \Log::info('Before update - selling_price: ' . ($product->selling_price ?? 'NULL'));
            \Log::info('Before update - discount_price: ' . ($product->discount_price ?? 'NULL'));
            \Log::info('Before update - marketing_description: ' . substr($product->marketing_description ?? 'NULL', 0, 50));

            // Handle customer images upload with error handling
            if ($request->hasFile('customer_images')) {
                try {
                    // Delete old images
                    if ($product->customer_images) {
                        foreach ($product->customer_images as $oldImage) {
                            Storage::disk('public')->delete($oldImage);
                        }
                    }

                    $customerImages = [];
                    foreach ($request->file('customer_images') as $image) {
                        $customerImages[] = $image->store('products/customer', 'public');
                    }
                    $data['customer_images'] = $customerImages;
                } catch (\Exception $e) {
                    \Log::error('Image update failed', [
                        'error' => $e->getMessage(),
                        'user_id' => auth()->id(),
                        'product_id' => $product->id,
                        'filename' => $image->getClientOriginalName() ?? 'unknown'
                    ]);
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Failed to update images. Please try again.');
                }
            }

            // Handle product video upload with error handling
            if ($request->hasFile('product_video')) {
                try {
                    // Delete old video
                    if ($product->product_video) {
                        Storage::disk('public')->delete($product->product_video);
                    }
                    $data['product_video'] = $request->file('product_video')->store('products/videos', 'public');
                } catch (\Exception $e) {
                    \Log::error('Video update failed', [
                        'error' => $e->getMessage(),
                        'user_id' => auth()->id(),
                        'product_id' => $product->id,
                        'filename' => $request->file('product_video')->getClientOriginalName()
                    ]);
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Failed to update video. Please try again.');
                }
            }

            // Update only marketing-related fields
            $marketingData = [
                'marketing_description' => $data['marketing_description'],
                'selling_price' => $data['selling_price'],
                'discount_price' => $data['discount_price'] ?? null,
                'customer_images' => $data['customer_images'] ?? null,
                'product_video' => $data['product_video'] ?? null,
            ];
            
            \Log::info('About to update product with marketing data: ', $marketingData);
            $updateResult = $product->update($marketingData);
            \Log::info('Update result: ' . ($updateResult ? 'SUCCESS' : 'FAILED'));

            // Refresh product to get updated data
            $product->refresh();
            \Log::info('After update - selling_price: ' . ($product->selling_price ?? 'NULL'));
            \Log::info('After update - discount_price: ' . ($product->discount_price ?? 'NULL'));
            \Log::info('After update - marketing_description: ' . substr($product->marketing_description ?? 'NULL', 0, 50));

            \Log::info('Product updated successfully', [
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'product_name' => $product->name,
                'update_result' => $updateResult
            ]);

            return redirect()->route('admin.product-management.sku-details', $product->inventory->id)
                ->with('success', 'Product updated successfully.');

        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error during product update', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'sql_state' => $e->getSqlState(),
                'error_code' => $e->getCode()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'A database error occurred. Please try again.');
                
        } catch (\Exception $e) {
            \Log::error('Unexpected error during product update', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => auth()->id(),
                'product_id' => $product->id
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An unexpected error occurred. Please try again.');
        }
    }

    /**
     * Delete a product.
     */
    public function destroy(Product $product)
    {
        try {
            // Delete associated images
            if ($product->customer_images) {
                foreach ($product->customer_images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            // Delete the product
            $product->delete();

            return redirect()->route('admin.product-management.index')
                ->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Product deletion failed', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            return redirect()->route('admin.product-management.index')
                ->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    /**
     * Validate product data integrity.
     */
    private function validateProductData(Product $product): bool
    {
        // 1. 基础字段验证
        if (empty($product->name) || empty($product->description) || empty($product->marketing_description)) {
            return false;
        }
        
        // 2. 价格验证
        if (!is_numeric($product->price) || $product->price <= 0 || $product->price > 9999999999999.99) {
            return false;
        }
        
        // 3. 折扣价格验证
        if ($product->discount_price !== null) {
            if (!is_numeric($product->discount_price) || 
                $product->discount_price <= 0 || 
                $product->discount_price >= $product->price ||
                $product->discount_price > 9999999999999.99) {
                return false;
            }
        }
        
        // 4. 分类验证
        if (!in_array($product->category, ['earring', 'bracelet', 'necklace', 'ring'])) {
            return false;
        }
        
        // 5. 特征数组验证
        if ($product->features !== null && is_array($product->features)) {
            foreach ($product->features as $feature) {
                if (!is_string($feature) || strlen($feature) > 255) {
                    return false;
                }
            }
        }
        
        // 6. 字符串长度验证
        if (strlen($product->name) > 255 || 
            strlen($product->description) > 5000 || 
            strlen($product->marketing_description) > 2000) {
            return false;
        }
        
        // 7. 特殊字符验证
        if (preg_match('/<script|javascript:|vbscript:|on\w+\s*=/i', $product->name) ||
            preg_match('/<script|javascript:|vbscript:|on\w+\s*=/i', $product->description) ||
            preg_match('/<script|javascript:|vbscript:|on\w+\s*=/i', $product->marketing_description)) {
            return false;
        }
        
        return true;
    }

    /**
     * Log security events.
     */
    private function logSecurityEvent(string $event, array $data = [])
    {
        \Log::info("Security Event: {$event}", array_merge([
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()
        ], $data));
    }

    /**
     * Secure database query with parameter binding.
     */
    private function secureQuery($query, array $params = [])
    {
        try {
            // 使用参数绑定防止SQL注入
            $results = \DB::select($query, $params);
            
            // 验证查询结果
            if (empty($results)) {
                \Log::warning('Empty query result', [
                    'query' => $query,
                    'params' => $params,
                    'user_id' => auth()->id()
                ]);
            }
            
            return $results;
        } catch (\Exception $e) {
            \Log::error('Database query failed', [
                'query' => $query,
                'params' => $params,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            throw $e;
        }
    }

    /**
     * Validate and sanitize search input.
     */
    private function sanitizeSearchInput($input)
    {
        if (!is_string($input)) {
            return '';
        }
        
        // 移除危险字符
        $input = preg_replace('/[<>"\']/', '', $input);
        
        // 限制长度 - 从配置获取
        $maxLength = config('product.security.input_validation.max_search_length', 100);
        $input = mb_substr($input, 0, $maxLength, 'UTF-8');
        
        // 转义特殊字符
        $input = addslashes($input);
        
        return $input;
    }

    /**
     * Reject review
     */
    public function rejectReview($id)
    {
        $review = \App\Modules\Product\Models\Review::findOrFail($id);
        $review->delete();

        return redirect()->route('admin.product-management.index')
            ->with('success', 'Review has been rejected and deleted successfully');
    }
}
