@extends('layouts.admin')

@section('title', 'Edit Inventory')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">

        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md mb-6 p-6 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Edit Inventory</h1>
            <a href="{{ route('admin.inventory.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">Back</a>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('admin.inventory.update', $inventory->id) }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            @method('PUT')

            <!-- Inventory Info -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block font-medium">Inventory Name</label>
                    <input type="text" name="name" value="{{ old('name', $inventory->name) }}" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="block font-medium">Type</label>
                    <select name="type" class="w-full border rounded p-2">
                        <option value="RingItem" {{ $inventory->type == 'RingItem' ? 'selected' : '' }}>Ring</option>
                        <option value="NecklaceItem" {{ $inventory->type == 'NecklaceItem' ? 'selected' : '' }}>Necklace</option>
                        <option value="EarringsItem" {{ $inventory->type == 'EarringsItem' ? 'selected' : '' }}>Earrings</option>
                        <option value="BraceletItem" {{ $inventory->type == 'BraceletItem' ? 'selected' : '' }}>Bracelet</option>
                    </select>
                </div>
            </div>

            <!-- Variations Section -->
            <div class="mb-6">
                <label class="block text-lg font-semibold mb-2">Product Variations</label>

                <div id="variation-container" class="space-y-4">
                    @foreach($inventory->variations as $index => $variation)
                        <div class="variation-item border rounded-lg p-4 bg-gray-50 relative">
                            <input type="hidden" name="variations[{{ $index }}][id]" value="{{ $variation->id }}">
                            <!-- Hidden input for delete -->
                            <input type="hidden" name="delete_variations[]" value="{{ $variation->id }}" class="delete-variation-id" disabled>

                            <div class="grid grid-cols-2 gap-4 mb-3">
                                <div>
                                    <label class="block font-medium">SKU</label>
                                    <input type="text" name="variations[{{ $index }}][sku]" value="{{ $variation->sku }}" class="w-full border rounded p-2 sku-input">
                                </div>
                                <div>
                                    <label class="block font-medium">Color</label>
                                    <input type="text" name="variations[{{ $index }}][color]" value="{{ $variation->color }}" class="w-full border rounded p-2">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-3">
                                <div>
                                    <label class="block font-medium">Size</label>
                                    <input type="text" name="variations[{{ $index }}][size]" value="{{ $variation->size }}" class="w-full border rounded p-2">
                                </div>
                                <div>
                                    <label class="block font-medium">Material</label>
                                    <input type="text" name="variations[{{ $index }}][material]" value="{{ $variation->material }}" class="w-full border rounded p-2">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-3">
                                <div>
                                    <label class="block font-medium">Price (RM)</label>
                                    <input type="number" step="0.01" name="variations[{{ $index }}][price]" value="{{ $variation->price }}" class="w-full border rounded p-2">
                                </div>
                                <div>
                                    <label class="block font-medium">Stock</label>
                                    <input type="number" name="variations[{{ $index }}][stock]" value="{{ $variation->stock }}" class="w-full border rounded p-2">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="block font-medium">Image</label>
                                <input type="file" name="variations[{{ $index }}][image_path]" class="w-full border rounded p-2">
                                @if($variation->image_path)
                                    <img src="{{ asset('storage/' . $variation->image_path) }}" class="mt-2 w-20 h-20 object-cover rounded">
                                @endif
                            </div>

                            <button type="button" class="absolute top-2 right-2 bg-red-500 text-white px-3 py-1 rounded remove-variation">
                                Remove
                            </button>
                        </div>
                    @endforeach
                </div>

                <!-- Add Variation Button -->
                <button type="button" id="add-variation" class="mt-4 px-4 py-2 bg-green-600 text-white rounded">+ Add Variation</button>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded">Update Inventory</button>
        </form>
    </div>
</div>

<script>
document.getElementById('add-variation').addEventListener('click', function() {
    const container = document.getElementById('variation-container');
    const index = container.children.length;

    const template = `
        <div class="variation-item border rounded-lg p-4 bg-gray-50 relative">
            <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="block font-medium">SKU</label>
                    <input type="text" name="variations[${index}][sku]" class="w-full border rounded p-2 sku-input">
                </div>
                <div>
                    <label class="block font-medium">Color</label>
                    <input type="text" name="variations[${index}][color]" class="w-full border rounded p-2">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="block font-medium">Size</label>
                    <input type="text" name="variations[${index}][size]" class="w-full border rounded p-2">
                </div>
                <div>
                    <label class="block font-medium">Material</label>
                    <input type="text" name="variations[${index}][material]" class="w-full border rounded p-2">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="block font-medium">Price (RM)</label>
                    <input type="number" step="0.01" name="variations[${index}][price]" class="w-full border rounded p-2">
                </div>
                <div>
                    <label class="block font-medium">Stock</label>
                    <input type="number" name="variations[${index}][stock]" class="w-full border rounded p-2">
                </div>
            </div>

            <div class="mb-3">
                <label class="block font-medium">Image</label>
                <input type="file" name="variations[${index}][image_path]" class="w-full border rounded p-2">
            </div>

            <button type="button" class="absolute top-2 right-2 bg-red-500 text-white px-3 py-1 rounded remove-variation">Remove</button>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', template);
});

// Remove variation
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-variation')) {
        const variationItem = e.target.closest('.variation-item');
        const hiddenInput = variationItem.querySelector('.delete-variation-id');
        if (hiddenInput) hiddenInput.disabled = false; // will be sent to controller
        variationItem.style.display = 'none';
    }
});

// Prevent duplicate SKUs instantly
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('sku-input')) {
        const allSkuInputs = document.querySelectorAll('.sku-input');
        const skuValues = [];
        allSkuInputs.forEach(input => {
            const val = input.value.trim().toUpperCase();
            if (skuValues.includes(val) && val !== '') {
                alert('Duplicate SKU detected: ' + val);
                input.value = '';
            } else if (val !== '') {
                skuValues.push(val);
            }
        });
    }
});
</script>
@endsection
