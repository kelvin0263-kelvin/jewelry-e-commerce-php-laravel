{{-- In resources/views/admin/products/edit.blade.php --}}
<h1>Edit Product: {{ $product->name }}</h1>
<form action="{{ route('admin.products.update', $product->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div>
        <label>Name:</label>
        <input type="text" name="name" value="{{ $product->name }}" required>
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
    <button type="submit">Update Product</button>
</form>