<?php
// 文件路径: app/Http/Controllers/ProductController.php

namespace App\Modules\Product\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display customer product listing (only published products from product management)
     */
    public function index()
    {
        $products = Product::forCustomers()->latest()->paginate(12);
        return view('product::products.index', compact('products'));
    }

    /**
     * Display individual product details for customers
     */
    public function show(Product $product)
    {
        // Only show published products to customers
        if (!$product->isPublished()) {
            abort(404);
        }
        return view('product::products.show', compact('product'));
    }
}
