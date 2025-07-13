{{-- In resources/views/admin/products/create.blade.php --}}
<h1>Add New Product</h1>
<form action="{{ route('admin.products.store') }}" method="POST">
    @csrf
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
    <button type="submit">Save Product</button>
</form>