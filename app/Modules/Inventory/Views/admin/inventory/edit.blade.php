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
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if ($errors->has('variations'))
            <div class="alert alert-danger mb-3 rounded-lg p-3">
                <strong>{{ $errors->first('variations') }}</strong>
            </div>
        @endif
        @error('variations.0.stock')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror

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
                    <select name="type" id="type" class="w-full border rounded p-2" onchange="showTypeSpecificFields()">
                        <option value="RingItem" {{ $inventory->type == 'RingItem' ? 'selected' : '' }}>Ring</option>
                        <option value="NecklaceItem" {{ $inventory->type == 'NecklaceItem' ? 'selected' : '' }}>Necklace</option>
                        <option value="EarringsItem" {{ $inventory->type == 'EarringsItem' ? 'selected' : '' }}>Earrings</option>
                        <option value="BraceletItem" {{ $inventory->type == 'BraceletItem' ? 'selected' : '' }}>Bracelet</option>
                    </select>
                </div>
            </div>

            <!-- Type-Specific Fields -->
            <div id="type-specific-fields" class="mb-6">
                <h3 class="text-lg font-semibold mb-4">Type-Specific Attributes</h3>
                
               <!-- Ring Fields (HIDDEN) -->
            
            <div id="ring-fields" class="type-fields hidden">
                <p class="text-gray-500 italic">Ring attributes are managed per variation (size & stone type).</p>
            </div>

                <!-- Necklace Fields -->
                <div id="necklace-fields" class="type-fields" style="display: none;">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium">Necklace Length (cm)</label>
                            <select name="necklace_length" class="w-full border rounded p-2">
                                <option value="">Select Length</option>
                                @foreach([30, 35, 40, 45, 50, 55, 60, 65, 70, 75, 80] as $length)
                                    <option value="{{ $length }}" {{ $inventory->necklace_length == $length ? 'selected' : '' }}>{{ $length }}cm</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium">Has Pendant</label>
                            <select name="has_pendant" class="w-full border rounded p-2">
                                <option value="">Select Option</option>
                                <option value="1" {{ $inventory->has_pendant == 1 ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ $inventory->has_pendant == 0 ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Earrings Fields -->
                <div id="earrings-fields" class="type-fields" style="display: none;">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium">Earring Style</label>
                            <select name="earring_style" class="w-full border rounded p-2">
                                <option value="">Select Style</option>
                                <option value="Stud" {{ $inventory->earring_style == 'Stud' ? 'selected' : '' }}>Stud</option>
                                <option value="Hoop" {{ $inventory->earring_style == 'Hoop' ? 'selected' : '' }}>Hoop</option>
                                <option value="Drop" {{ $inventory->earring_style == 'Drop' ? 'selected' : '' }}>Drop</option>
                                <option value="Chandelier" {{ $inventory->earring_style == 'Chandelier' ? 'selected' : '' }}>Chandelier</option>
                                <option value="Cluster" {{ $inventory->earring_style == 'Cluster' ? 'selected' : '' }}>Cluster</option>
                                <option value="Dangle" {{ $inventory->earring_style == 'Dangle' ? 'selected' : '' }}>Dangle</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium">Is Pair</label>
                            <select name="is_pair" class="w-full border rounded p-2">
                                <option value="">Select Option</option>
                                <option value="1" {{ $inventory->is_pair == 1 ? 'selected' : '' }}>Yes (Pair)</option>
                                <option value="0" {{ $inventory->is_pair == 0 ? 'selected' : '' }}>No (Single)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Bracelet Fields -->
                <div id="bracelet-fields" class="type-fields" style="display: none;">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium">Clasp Type</label>
                            <select name="bracelet_clasp" class="w-full border rounded p-2">
                                <option value="">Select Clasp Type</option>
                                <option value="Standard" {{ $inventory->bracelet_clasp == 'Standard' ? 'selected' : '' }}>Standard</option>
                                <option value="Magnetic" {{ $inventory->bracelet_clasp == 'Magnetic' ? 'selected' : '' }}>Magnetic</option>
                                <option value="Toggle" {{ $inventory->bracelet_clasp == 'Toggle' ? 'selected' : '' }}>Toggle</option>
                                <option value="Lobster" {{ $inventory->bracelet_clasp == 'Lobster' ? 'selected' : '' }}>Lobster</option>
                                <option value="Box" {{ $inventory->bracelet_clasp == 'Box' ? 'selected' : '' }}>Box</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium">Adjustable</label>
                            <select name="adjustable" class="w-full border rounded p-2">
                                <option value="">Select Option</option>
                                <option value="1" {{ $inventory->adjustable == 1 ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ $inventory->adjustable == 0 ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Variations Section -->
            <div class="mb-6">
                <label class="block text-lg font-semibold mb-2">Product Variations</label>

                <div id="variation-container" class="space-y-4">
                    @foreach($inventory->variations as $index => $variation)
                        <div class="variation-item border rounded-lg p-4 bg-gray-50 relative">
                            <!-- Hidden ID so controller can detect existing record -->
                            <input type="hidden" name="variations[{{ $index }}][id]" value="{{ $variation->id }}">

                            <div class="grid grid-cols-2 gap-4 mb-3">
                                <div>
                                    <label class="block font-medium">SKU</label>
                                    <input type="text" name="variations[{{ $index }}][sku]" 
                                        value="{{ $variation->sku }}" 
                                        class="w-full border rounded p-2 sku-input">
                                </div>
                                <div>
                                    <label class="block font-medium">Color</label>
                                    <input type="text" name="variations[{{ $index }}][color]" 
                                        value="{{ $variation->color }}" 
                                        class="w-full border rounded p-2">
                                </div>
                            </div>

                            <!-- Type-specific attributes repeated -->
                            {{-- For Ring (dropdowns) --}}
                            @if($inventory->type === 'RingItem')
                                <div class="grid grid-cols-2 gap-4 mb-3">
                                    <div>
                                        <label class="block font-medium">Ring Size</label>
                                        <select name="variations[{{ $index }}][size]" class="w-full border rounded p-2">
                                            <option value="">Select Ring Size</option>
                                            @for($i = 4; $i <= 10; $i++)
                                                <option value="{{ $i }}" {{ $variation->size == $i ? 'selected' : '' }}>Size {{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-medium">Stone Type</label>
                                        <select name="variations[{{ $index }}][material]" class="w-full border rounded p-2">
                                            <option value="">Select Stone</option>
                                            @foreach(['Diamond','Ruby','Sapphire','Emerald','Pearl','Amethyst'] as $stone)
                                                <option value="{{ $stone }}" {{ $variation->material == $stone ? 'selected' : '' }}>{{ $stone }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            {{-- For all other types (text inputs) --}}
                            @else
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
                            @endif

                            <div class="grid grid-cols-2 gap-4 mb-3">
                                <div>
                                    <label class="block font-medium">Price (RM)</label>
                                    <input type="number" step="0.01" 
                                        name="variations[{{ $index }}][price]" 
                                        value="{{ $variation->price }}" 
                                        class="w-full border rounded p-2">
                                </div>
                                <div>
                                    <label class="block font-medium">Stock</label>
                                    <input type="number" 
                                        name="variations[{{ $index }}][stock]" 
                                        value="{{ $variation->stock }}" 
                                        class="w-full border rounded p-2">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="block font-medium">Image</label>
                                <input type="file" name="variations[{{ $index }}][image_path]" class="w-full border rounded p-2">
                                @if($variation->image_path)
                                    <img src="{{ asset('storage/' . $variation->image_path) }}" 
                                        class="mt-2 w-24 h-24 object-cover rounded">
                                @endif
                            </div>

                            <!-- Delete marker -->
                            <input type="hidden" name="variations[{{ $index }}][delete]" value="0" class="delete-variation-flag">

                            <button type="button" 
                                    class="absolute top-2 right-2 bg-red-500 text-white px-3 py-1 rounded remove-variation">
                                Remove
                            </button>
                        </div>
                    @endforeach
                </div>
                


                <!-- Add Variation Button -->
                <button type="button" id="add-variation" class="mt-4 px-4 py-2 bg-green-600 text-white rounded">
                    + Add Variation
                </button>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded">Update Inventory</button>
        </form>
    </div>
</div>

<script>
function showTypeSpecificFields() {
    const type = document.getElementById('type').value;
    const typeFields = document.querySelectorAll('.type-fields');
    
    // Hide all
    typeFields.forEach(field => field.style.display = 'none');
    
    // Show selected
    switch(type) {
        case 'RingItem':
            document.getElementById('ring-fields').style.display = 'block';
            break;
        case 'NecklaceItem':
            document.getElementById('necklace-fields').style.display = 'block';
            break;
        case 'EarringsItem':
            document.getElementById('earrings-fields').style.display = 'block';
            break;
        case 'BraceletItem':
            document.getElementById('bracelet-fields').style.display = 'block';
            break;
    }
}


// Show type-specific fields on page load
document.addEventListener('DOMContentLoaded', function() {
    showTypeSpecificFields();
});

document.getElementById('add-variation').addEventListener('click', function() {
    const container = document.getElementById('variation-container');
    const index = container.children.length;
    const type = document.getElementById('type').value;

    // Build type-specific fields dynamically
    let typeSpecificFields = '';
    if (type === 'RingItem') {
        typeSpecificFields = `
            <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="block font-medium">Ring Size</label>
                    <select name="variations[${index}][size]" class="w-full border rounded p-2">
                        <option value="">Select Ring Size</option>
                        ${[...Array(7)].map((_, i) => {
                            const size = i + 4;
                            return `<option value="${size}">Size ${size}</option>`;
                        }).join('')}
                    </select>
                </div>
                <div>
                    <label class="block font-medium">Stone Type</label>
                    <select name="variations[${index}][material]" class="w-full border rounded p-2">
                        <option value="">Select Stone</option>
                        ${['Diamond','Ruby','Sapphire','Emerald','Pearl','Amethyst'].map(stone =>
                            `<option value="${stone}">${stone}</option>`
                        ).join('')}
                    </select>
                </div>
            </div>
        `;
    } else {
        typeSpecificFields = `
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
        `;
    }

    // Final template
    const template = `
    <div class="variation-item border rounded-lg p-4 bg-gray-50 relative">
        <input type="hidden" name="variations[${index}][delete]" value="0" class="delete-variation-flag">
        
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

        ${typeSpecificFields}

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

        <button type="button" class="absolute top-2 right-2 bg-red-500 text-white px-3 py-1 rounded remove-variation">
            Remove
        </button>
    </div>
`;

    container.insertAdjacentHTML('beforeend', template);
});


document.addEventListener('click', function(e) {
    // Look for remove button even if click inside icon/span
    const btn = e.target.closest('.remove-variation');
    if (!btn) return;

    const variationItem = btn.closest('.variation-item');
    if (!variationItem) return;

    // Get the delete flag
    const deleteFlag = variationItem.querySelector('input.delete-variation-flag');
    if (!deleteFlag) {
        console.error('Delete flag not found!');
        return;
    }

    // Mark for deletion if existing, otherwise remove completely
    const idField = variationItem.querySelector('input[name*="[id]"]');
    if (idField) {
        deleteFlag.value = 1; // mark for deletion
        variationItem.style.display = 'none'; // hide visually
    } else {
        variationItem.remove(); // remove new variation completely
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

document.addEventListener('change', function (e) {
    // Check if the file input belongs to variations
    if (e.target.matches('input[type="file"][name^="variations"]')) {
        const fileInput = e.target;
        const previewImg = fileInput.closest('div').querySelector('img');
        const file = fileInput.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
                if (previewImg) {
                    previewImg.src = event.target.result; // Update existing preview
                } else {
                    // If there’s no preview, create one
                    const newImg = document.createElement('img');
                    newImg.src = event.target.result;
                    newImg.classList.add('w-12', 'h-12', 'rounded', 'border', 'object-cover', 'mt-2');
                    fileInput.insertAdjacentElement('afterend', newImg);
                }
            };
            reader.readAsDataURL(file);
        }
    }
});
</script>
@endsection
