@extends('layouts.admin')

@section('title', 'Create Basic Product')

@section('content')
    <h1>Create Basic Product (Inventory Stage)</h1>
    <p><em>Create basic product info here. Enhanced details and publishing will be done in Product Management.</em></p>
    
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
    
    <form action="{{ route('admin.inventory.store') }}" method="POST" enctype="multipart/form-data"> 
        @csrf
        <div>
            <label>Product Name:</label>
            <input type="text" name="name" value="{{ old('name') }}" required>
        </div>
        
        <div>
            <label>SKU (optional - will auto-generate if empty):</label>
            <input type="text" name="sku" value="{{ old('sku') }}">
        </div>
        
        <div>
            <label>Basic Description (internal use):</label>
            <textarea name="description" required>{{ old('description') }}</textarea>
        </div>
        
        <div>
            <label>Base Price:</label>
            <input type="number" step="0.01" name="price" value="{{ old('price') }}" required>
        </div>
        
        <div>
            <label>Initial Quantity:</label>
            <input type="number" name="quantity" value="{{ old('quantity') }}" required>
        </div>
        
        <div>
            <label>Minimum Stock Level:</label>
            <input type="number" name="min_stock_level" value="{{ old('min_stock_level', 5) }}" required>
        </div>
        
        <div>
            <label>Internal Image (for inventory reference only):</label>
            <input type="file" name="internal_image" accept="image/*">
            <small>This image is for internal use only. Customer images will be added in Product Management.</small>
        </div>
        
        <button type="submit">Create Basic Product</button>
        <a href="{{ route('admin.inventory.index') }}">Cancel</a>
    </form>
@endsection