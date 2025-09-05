@extends('layouts.admin')

@section('title', 'Create Inventory')

@section('content')
<h1>Create Inventory</h1>

<form action="{{ route('admin.inventory.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Type</label>
        <select name="type" class="form-control" required>
            <option value="RingItem">RingItem</option>
            <option value="NecklaceItem">NecklaceItem</option>
            <option value="EarringsItem">EarringsItem</option>
            <option value="BraceletItem">BraceletItem</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Quantity</label>
        <input type="number" name="quantity" class="form-control">
    </div>

    <h4>Variations</h4>
    <div id="variations-container">
        <div class="variation mb-2">
            <input type="text" name="variations[0][sku]" placeholder="SKU">
            <input type="text" name="variations[0][material]" placeholder="Material">
            <input type="number" name="variations[0][stock]" placeholder="Stock">
            <input type="number" name="variations[0][price]" placeholder="Price">
        </div>
    </div>

    <button type="button" class="btn btn-secondary mb-3" onclick="addVariation()">Add Variation</button>
    <br>
    <button type="submit" class="btn btn-success">Save Inventory</button>
</form>

<script>
let variationIndex = 1;
function addVariation() {
    const container = document.getElementById('variations-container');
    const div = document.createElement('div');
    div.className = 'variation mb-2';
    div.innerHTML = `
        <input type="text" name="variations[${variationIndex}][sku]" placeholder="SKU">
        <input type="text" name="variations[${variationIndex}][material]" placeholder="Material">
        <input type="number" name="variations[${variationIndex}][stock]" placeholder="Stock">
        <input type="number" name="variations[${variationIndex}][price]" placeholder="Price">
    `;
    container.appendChild(div);
    variationIndex++;
}
</script>
@endsection
