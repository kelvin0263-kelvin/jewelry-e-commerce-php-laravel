<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // 显示产品列表
    public function index()
    {
        $products = Product::all();  //Eloquent will execute  SELECT * FROM products SQL 

        // 返回一个视图，将 $products 变量传递给视图 'products' 是数组的 键（key）$products 是数组的 值（value）
        return view('products.index', ['products' => $products]);
    }


    // 显示创建产品的表单
    public function create()
    {
        return view('admin.products.create');
    }

    // 储存新创建的产品
    public function store(Request $request)
    {
        // 验证请求数据
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
        ]);

        // 创建产品
        Product::create($request->all());

        // 重定向到后台产品列表页，并附带成功消息
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
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
        ]);

        $product->update($request->all());

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    // 删除产品
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
