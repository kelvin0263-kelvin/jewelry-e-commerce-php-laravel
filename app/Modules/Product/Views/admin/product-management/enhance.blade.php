@extends('layouts.admin')

@section('title', 'Enhance Product')

@section('content')
    <h1>Enhance Product: {{ $product->name }}</h1>
    <p><em>Add marketing details and customer-facing images to prepare this product for publishing.</em></p>

    <div style="background: #f0f8ff; padding: 15px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 5px;">
        <h3>Basic Product Info (from Inventory):</h3>
        <p><strong>SKU:</strong> {{ $product->sku }}</p>
        <p><strong>Base Price:</strong> RM{{ number_format($product->price, 2) }}</p>
        <p><strong>Current Stock:</strong> {{ $product->quantity }}</p>
        <p><strong>Basic Description:</strong> {{ $product->description }}</p>
        @if($product->internal_image_path)
            <p><strong>Internal Image:</strong></p>
            <img src="{{ asset('storage/' . $product->internal_image_path) }}" alt="{{ $product->name }}" width="100">
        @endif
    </div>

    @if ($errors->any())
        <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 20px;">
            <strong>Please fix the following errors:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.product-management.store-enhancement', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div style="margin-bottom: 15px;">
            <label><strong>Marketing Description (for customers):</strong></label><br>
            <textarea name="marketing_description" rows="5" style="width: 100%;" required>{{ old('marketing_description') }}</textarea>
            <small>This enhanced description will be shown to customers.</small>
        </div>

        <div style="margin-bottom: 15px;">
            <label><strong>Category:</strong></label><br>
            <input type="text" name="category" style="width: 300px;" value="{{ old('category') }}" required>
            <small>e.g., Rings, Necklaces, Earrings, etc.</small>
        </div>

        <div style="margin-bottom: 15px;">
            <label><strong>Product Features:</strong></label><br>
            <div id="features-container">
                <input type="text" name="features[]" placeholder="Feature 1" style="width: 300px; margin-bottom: 5px;">
                <input type="text" name="features[]" placeholder="Feature 2" style="width: 300px; margin-bottom: 5px;">
                <input type="text" name="features[]" placeholder="Feature 3" style="width: 300px; margin-bottom: 5px;">
            </div>
            <button type="button" onclick="addFeature()" style="background: #ddd; border: none; padding: 5px;">+ Add Feature</button>
        </div>

        <div style="margin-bottom: 15px;">
            <label><strong>Discount Price (optional):</strong></label><br>
            <input type="number" step="0.01" name="discount_price" style="width: 200px;" value="{{ old('discount_price') }}">
            <small>Leave empty if no discount. Must be less than base price (${{ $product->price }}).</small>
        </div>

        <div style="margin-bottom: 15px;">
            <label><strong>Customer Images (multiple allowed):</strong></label><br>
            <input type="file" name="customer_images[]" multiple accept="image/*">
            <small>These high-quality images will be shown to customers. Multiple images allowed.</small>
        </div>

        <div style="margin-top: 20px;">
            <button type="submit" style="background: green; color: white; padding: 10px 20px; border: none; cursor: pointer;">
                Enhance Product & Submit for Review
            </button>
            <a href="{{ route('admin.product-management.index') }}" style="margin-left: 10px;">Cancel</a>
        </div>
    </form>

    <script>
        function addFeature() {
            const container = document.getElementById('features-container');
            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'features[]';
            input.placeholder = 'Additional feature';
            input.style.width = '300px';
            input.style.marginBottom = '5px';
            input.style.display = 'block';
            container.appendChild(input);
        }
    </script>

@endsection

