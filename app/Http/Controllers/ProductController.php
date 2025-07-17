<?php
// 文件路径: app/Http/Controllers/ProductController.php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * 显示前台的产品列表 (只显示可见的产品)
     */
    public function index()
    {
        $products = Product::where('is_visible', true)->latest()->paginate(12);
        return view('products.index', compact('products'));
    }

    /**
     * 显示前台的单个产品详情页
     */
    public function show(Product $product)
    {
        // 如果产品不可见，则返回404错误
        if (!$product->is_visible) {
            abort(404);
        }
        return view('products.show', compact('product'));
    }
}
