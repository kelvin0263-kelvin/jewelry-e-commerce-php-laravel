@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
    <h1>Edit Product: {{ $product->name }}</h1>
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div>
                    <label>Name:</label>
                    <input type=" text" name="name" value="{{ $product->name }}" required>
        </div>
        <div>
            <label>Description:</label>
            <textarea name="description" required>{{ $product->description }}</textarea>
        </div>
        <div>
            <label>Price:</label>
            <input type="number" step="0.01" name="price" value="{{ $product->price }}" required>
        </div>
        <div>
            <label>Quantity:</label>
            <input type="number" name="quantity" value="{{ $product->quantity }}" required>
        </div>
        <div>
            <label>Current Image:</label>
            @if ($product->image_path)
                <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" width="150">
            @else
                <p>No image uploaded.</p>
            @endif
        </div>
        <div>
            <label>Upload New Image (optional):</label>
            <input type="file" name="image">
        </div>
        <button type="submit">Update Product</button>

    </form>
@endsection