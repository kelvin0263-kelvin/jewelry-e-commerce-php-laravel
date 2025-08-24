<?php

namespace App\Modules\Inventory\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    /**
     * Display basic products in inventory (draft stage only)
     */
    public function index()
    {
        // Only show draft products in inventory - once they move to product management, they disappear from here
        $products = Product::draft()->latest()->paginate(15);
        return view('inventory::admin.inventory.index', compact('products'));
    }

    /**
     * 显示创建产品的表单
     */
    public function create()
    {
        return view('inventory::admin.inventory.create');
    }

    /**
     * Store basic product info (inventory stage)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:products,sku',
            'description' => 'required|string', // Basic description for internal use
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:1',
            'internal_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Internal use only
        ]);

        // Handle internal image upload
        if ($request->hasFile('internal_image')) {
            $data['internal_image_path'] = $request->file('internal_image')->store('inventory', 'public');
        }

        // Generate SKU if not provided
        if (empty($data['sku'])) {
            $data['sku'] = 'PRD-' . strtoupper(substr(md5($data['name'] . time()), 0, 8));
        }

        // Set as draft status (not visible to customers yet)
        $data['status'] = 'draft';
        $data['is_visible'] = false;

        $product = Product::create($data);

        return redirect()->route('admin.product-management.enhance', $product)
            ->with('success', 'Basic product created! Now add marketing details to publish it to customers.');
    }

    /**
     * 显示编辑产品的表单
     */
    public function edit(Product $product)
    {
        return view('inventory::admin.inventory.edit', compact('product'));
    }

    /**
     * 更新产品信息
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_visible' => 'sometimes|boolean',
        ]);

        $data['is_visible'] = $request->has('is_visible');

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.inventory.index')->with('success', 'Product updated successfully.');
    }

    /**
     * 删除产品
     */
    public function destroy(Product $product)
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        $product->delete();
        return redirect()->route('admin.inventory.index')->with('success', 'Product deleted successfully.');
    }
}
