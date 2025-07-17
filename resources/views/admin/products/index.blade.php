{{-- In resources/views/admin/products/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Product Management')

@section('content')

    <h1>Product List</h1>
    <a href="{{ route('admin.products.create') }}" style="margin-bottom: 15px; display: inline-block;">+ Add New Product</a>

    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Price (RM)</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>
                        @if ($product->image_path)
                            <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" width="100">
                        @else
                            No Image
                        @endif
                    </td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->price }}</td>
                    <td>{{ $product->quantity }}</td>
                    <td>
                        <a href="{{ route('admin.products.edit', $product->id) }}">Edit</a>

                        {{-- 删除按钮需要用表单来提交，以保证安全 --}}
                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                            style="display: inline;"
                            onsubmit="return confirm('Are you sure you want to delete this product?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                style="color: red; background: none; border: none; padding: 0; cursor: pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No products found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

@endsection