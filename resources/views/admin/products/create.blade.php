@extends('layouts.admin')

@section('title', 'Add New Product')

@section('content')
    <h1>Add New Product</h1>
    {{-- Add this error block --}}
    @if ($errors->any())
        <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 20px;">
            <strong>Whoops! Something went wrong.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data"> @csrf
        <div>
            <label>Name:</label>
            <input type="text" name="name" required>
        </div>
        <div>
            <label>Description:</label>
            <textarea name="description" required></textarea>
        </div>
        <div>
            <label>Price:</label>
            <input type="number" step="0.01" name="price" required>
        </div>
        <div>
            <label>Quantity:</label>
            <input type="number" name="quantity" required>
        </div>
        <div>
            <label>Image:</label>
            <input type="file" name="image">
        </div>
        <div>
            <label>
                <input type="checkbox" name="is_visible" value="1">
                Visible to public
            </label>
        </div>
        <button type="submit">Save Product</button>
@endsection