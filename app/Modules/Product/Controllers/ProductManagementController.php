<?php

namespace App\Modules\Product\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProductManagementController extends Controller
{
    /**
     * Display products ready for enhancement and publishing
     */
    public function index()
    {
        // Show all products except those still in draft (inventory stage)
        $products = Product::whereIn('status', ['pending_review', 'approved', 'published'])
            ->with('publishedBy')
            ->latest()
            ->paginate(15);
            
        return view('product::admin.product-management.index', compact('products'));
    }

    /**
     * Show form to enhance a basic product from inventory
     */
    public function enhance(Product $product)
    {
        // Only allow enhancement of draft products
        if ($product->status !== 'draft') {
            return redirect()->route('admin.product-management.index')
                ->with('error', 'This product has already been enhanced.');
        }

        return view('product::admin.product-management.enhance', compact('product'));
    }

    /**
     * Store enhanced product details
     */
    public function storeEnhancement(Request $request, Product $product)
    {
        $data = $request->validate([
            'marketing_description' => 'required|string',
            'category' => 'required|string|max:255',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'customer_images' => 'nullable|array',
            'customer_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Handle customer images upload
        if ($request->hasFile('customer_images')) {
            $customerImages = [];
            foreach ($request->file('customer_images') as $image) {
                $customerImages[] = $image->store('products/customer', 'public');
            }
            $data['customer_images'] = $customerImages;
        }

        // Update product with enhanced details
        $product->update([
            'marketing_description' => $data['marketing_description'],
            'category' => $data['category'],
            'features' => $data['features'],
            'discount_price' => $data['discount_price'],
            'customer_images' => $data['customer_images'] ?? [],
            'status' => 'pending_review'
        ]);

        return redirect()->route('admin.product-management.index')
            ->with('success', 'Product enhanced successfully and is now pending review.');
    }

    /**
     * Approve product for publishing
     */
    public function approve(Product $product)
    {
        if ($product->status !== 'pending_review') {
            return redirect()->back()->with('error', 'Product is not in pending review status.');
        }

        $product->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Product approved. It can now be published.');
    }

    /**
     * Publish product to customers
     */
    public function publish(Product $product)
    {
        if (!$product->canBePublished()) {
            return redirect()->back()->with('error', 'Product cannot be published yet.');
        }

        $product->update([
            'status' => 'published',
            'is_visible' => true,
            'published_at' => now(),
            'published_by' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Product published successfully! It is now visible to customers.');
    }

    /**
     * Unpublish product from customers
     */
    public function unpublish(Product $product)
    {
        if ($product->status !== 'published') {
            return redirect()->back()->with('error', 'Product is not published.');
        }

        $product->update([
            'is_visible' => false,
            'status' => 'approved' // Keep it approved but not visible
        ]);

        return redirect()->back()->with('success', 'Product unpublished. It is no longer visible to customers.');
    }

    /**
     * Edit enhanced product details
     */
    public function edit(Product $product)
    {
        if ($product->status === 'draft') {
            return redirect()->route('admin.product-management.enhance', $product)
                ->with('info', 'This product needs to be enhanced first.');
        }

        return view('product::admin.product-management.edit', compact('product'));
    }

    /**
     * Update enhanced product details
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'description' => 'required|string',
            'marketing_description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:1',
            'category' => 'required|string|max:255',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'customer_images' => 'nullable|array',
            'customer_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Handle customer images upload
        if ($request->hasFile('customer_images')) {
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
        }

        $product->update($data);

        return redirect()->route('admin.product-management.index')
            ->with('success', 'Product updated successfully.');
    }
}
