<?php

namespace App\Modules\Product\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Decorators\AdminProductDecorator;
use App\Modules\Product\Services\DatabaseSecurityService;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProductManagementController extends Controller
{
    /**
     * Display all products.
     */
    public function index(Request $request)
    {
        $query = Product::with(['inventory', 'publisher']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'published') {
                $query->where('is_visible', true)->whereNotNull('published_at');
            } elseif ($request->status === 'pending') {
                $query->where('is_visible', false)->whereNull('published_at');
            }
        }

        // Filter by category
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Search functionality with input sanitization
        if ($request->has('search') && $request->search) {
            $sanitizedSearch = $this->sanitizeSearchInput($request->search);
            if (!empty($sanitizedSearch)) {
                $query->where(function($q) use ($sanitizedSearch) {
                    $q->where('name', 'like', '%' . $sanitizedSearch . '%')
                      ->orWhere('sku', 'like', '%' . $sanitizedSearch . '%')
                      ->orWhere('description', 'like', '%' . $sanitizedSearch . '%');
                });
                
                // 记录搜索活动
                $this->logSecurityEvent('Product search performed', [
                    'search_term' => $sanitizedSearch,
                    'original_term' => $request->search
                ]);
            }
        }

        $products = $query->paginate(15);
        
        // Decorate products for admin display
        $decoratedProducts = $products->getCollection()->map(function($product) {
            return new AdminProductDecorator($product);
        });

        $products->setCollection($decoratedProducts);

        return view('product::admin.product-management.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        return view('product::admin.product-management.create');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        // 验证产品数据完整性
        if (!$this->validateProductData($product)) {
            \Log::error('Invalid product data detected', ['product_id' => $product->id]);
            abort(500, 'Invalid product data.');
        }

        $product->load(['inventory', 'publisher']);
        $decoratedProduct = new AdminProductDecorator($product);
        
        return view('product::admin.product-management.show', compact('product', 'decoratedProduct'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(ProductStoreRequest $request)
    {
        try {
            $data = $request->validated();

            // Handle customer images upload with error handling
            if ($request->hasFile('customer_images')) {
                $customerImages = [];
                foreach ($request->file('customer_images') as $image) {
                    try {
                        $customerImages[] = $image->store('products/customer', 'public');
                    } catch (\Exception $e) {
                        \Log::error('Image upload failed', [
                            'error' => $e->getMessage(),
                            'user_id' => auth()->id(),
                            'filename' => $image->getClientOriginalName()
                        ]);
                        return redirect()->back()
                            ->withInput()
                            ->with('error', 'Failed to upload one or more images. Please try again.');
                    }
                }
                $data['customer_images'] = $customerImages;
            }

            // Handle product video upload with error handling
            if ($request->hasFile('product_video')) {
                try {
                    $data['product_video'] = $request->file('product_video')->store('products/videos', 'public');
                } catch (\Exception $e) {
                    \Log::error('Video upload failed', [
                        'error' => $e->getMessage(),
                        'user_id' => auth()->id(),
                        'filename' => $request->file('product_video')->getClientOriginalName()
                    ]);
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Failed to upload video. Please try again.');
                }
            }

            // Generate unique SKU and Product ID with error handling
            try {
                $data['sku'] = 'PROD-' . strtoupper(uniqid());
                $data['product_id'] = 'PD' . date('y') . str_pad(Product::count() + 1, 3, '0', STR_PAD_LEFT);
            } catch (\Exception $e) {
                \Log::error('ID generation failed', [
                    'error' => $e->getMessage(),
                    'user_id' => auth()->id()
                ]);
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to generate product identifiers. Please try again.');
            }
            
            // Set default values
            $data['status'] = 'draft';
            $data['is_visible'] = false;

            // Create the product with secure transaction
            $product = DatabaseSecurityService::secureTransaction(function () use ($data) {
                return Product::create($data);
            }, ['operation' => 'product_creation', 'product_name' => $data['name']]);

            \Log::info('Product created successfully', [
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'product_name' => $product->name
            ]);

            return redirect()->route('admin.product-management.index')
                ->with('success', 'Product created successfully.');

        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error during product creation', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'sql_state' => $e->getSqlState(),
                'error_code' => $e->getCode()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'A database error occurred. Please try again.');
                
        } catch (\Exception $e) {
            \Log::error('Unexpected error during product creation', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => auth()->id()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An unexpected error occurred. Please try again.');
        }
    }

    /**
     * Show form to enhance a product.
     */
    public function enhance(Product $product)
    {
        return view('product::admin.product-management.enhance', compact('product'));
    }

    /**
     * Store enhanced product details.
     */
    public function storeEnhancement(Request $request, Product $product)
    {
        $data = $request->validate([
            'marketing_description' => 'required|string',
            'category' => 'required|string|max:255|in:earring,bracelet,necklace,ring',
            'features' => 'nullable|array|max:10',
            'features.*' => 'string|max:255',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'customer_images' => 'nullable|array|max:5',
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
            'category' => $data['category'],
            'features' => $data['features'],
            'discount_price' => $data['discount_price'],
            'customer_images' => $data['customer_images'] ?? [],
            'product_video' => $data['product_video'] ?? $product->product_video,
        ]);

        return redirect()->route('admin.product-management.index')
            ->with('success', 'Product enhanced successfully.');
    }

    /**
     * Publish product to customers.
     */
    public function publish(Product $product)
    {
        $product->update([
            'is_visible' => true,
            'published_at' => now(),
            'published_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Product published successfully!');
    }

    /**
     * Unpublish product from customers.
     */
    public function unpublish(Product $product)
    {
        $product->update([
            'is_visible' => false,
        ]);

        return redirect()->back()->with('success', 'Product unpublished successfully.');
    }

    /**
     * Edit product details.
     */
    public function edit(Product $product)
    {
        return view('product::admin.product-management.edit', compact('product'));
    }

    /**
     * Update product details.
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        try {
            \Log::info('Update method called for product: ' . $product->id);
            $data = $request->validated();
            \Log::info('Validated data: ', $data);

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

            // Update product with secure transaction
            DatabaseSecurityService::secureTransaction(function () use ($product, $data) {
                $product->update($data);
            }, ['operation' => 'product_update', 'product_id' => $product->id, 'product_name' => $data['name'] ?? $product->name]);

            \Log::info('Product updated successfully', [
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'product_name' => $product->name
            ]);

            return redirect()->route('admin.product-management.show', $product)
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
        
        // 限制长度
        $input = mb_substr($input, 0, 100, 'UTF-8');
        
        // 转义特殊字符
        $input = addslashes($input);
        
        return $input;
    }
}
