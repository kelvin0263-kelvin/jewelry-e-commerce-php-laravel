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
        $products = Product::forCustomers()->paginate(12);
        return view('product::products.index', compact('products'));
    }

    /**
     * Display individual product details for customers
     */
    public function scopeForCustomers($query)
{
    return $query->where('is_visible', true)
                 ->whereNotNull('published_at')
                 ->whereHas('inventory'); // ✅ Remove status filter
}

}
