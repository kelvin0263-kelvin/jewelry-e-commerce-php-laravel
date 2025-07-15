<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
class ProductController extends Controller
{
    // 显示产品列表
    public function index()
    {
        $products = Product::all();  //Eloquent will execute  SELECT * FROM products SQL 

        // 返回一个视图，将 $products 变量传递给视图 'products' 是数组的 键（key）$products 是数组的 值（value）
        return view('admin.products.index', ['products' => $products]);
    }


    // 显示创建产品的表单
    public function create()
    {
        return view('admin.products.create');
    }

    // 储存新创建的产品
    public function store(Request $request)
    {
        // 增加对图片文件的验证规则
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // 可选|必须是图片|格式限制|大小限制2MB
        ]);

        // 如果有图片上传
        if ($request->hasFile('image')) {
            // 将图片存储在 storage/app/public/products 目录下，并返回路径
            $path = $request->file('image')->store('products', 'public');
            $data['image_path'] = $path;
        }

        // 创建产品
        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    // 显示编辑产品的表单
    public function edit(Product $product)
    {
        // Laravel 的路由模型绑定会自动根据 URL 中的 ID 找到对应的 Product 实例
        return view('admin.products.edit', compact('product'));
    }

    // 更新产品信息
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // 如果有新的图片上传
        if ($request->hasFile('image')) {
            // 1. 删除旧图片 (如果存在)
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            // 2. 存储新图片并获取路径
            $path = $request->file('image')->store('products', 'public');
            $data['image_path'] = $path;
        }

        // 更新产品信息
        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    // 删除产品
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
