{{-- In resources/views/admin/products/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Inventory Management')

@section('content')

    <h1>Inventory Management</h1>
    <p><em>Create basic products here. They will be available for enhancement in Product Management once created.</em></p>
    
    <a href="{{ route('admin.inventory.create') }}" style="margin-bottom: 15px; display: inline-block;">+ Create Basic Product</a>

    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>ID</th>
                <th>SKU</th>
                <th>Internal Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Min Stock</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->sku }}</td>
                    <td>
                        @if ($product->internal_image_path)
                            <img src="{{ asset('storage/' . $product->internal_image_path) }}" alt="{{ $product->name }}" width="60">
                        @else
                            No Image
                        @endif
                    </td>
                    <td>{{ $product->name }}</td>
                    <td>${{ number_format($product->price, 2) }}</td>
                    <td>
                        <span style="color: {{ $product->quantity <= $product->min_stock_level ? 'red' : 'green' }}">
                            {{ $product->quantity }}
                        </span>
                    </td>
                    <td>{{ $product->min_stock_level }}</td>
                    <td>
                        <span style="background: orange; color: white; padding: 2px 6px; border-radius: 3px;">
                            {{ ucfirst($product->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.inventory.edit', $product->id) }}">Edit Basic Info</a>
                        
                        <form action="{{ route('admin.inventory.destroy', $product->id) }}" method="POST"
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
                    <td colspan="9">No basic products found. Create your first product above.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

@endsection