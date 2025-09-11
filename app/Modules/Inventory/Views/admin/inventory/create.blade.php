@extends('layouts.admin')

@section('title', 'Create Inventory')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Create New Inventory</h1>
        <div class="flex gap-3">
             <button type="button" onclick="history.back()"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg shadow hover:bg-gray-400 transition">
                ← Back
            </button>
            <a href="{{ route('admin.inventory.index') }}"
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg shadow hover:bg-gray-300 transition">
                Cancel
            </a>
            <button type="submit" form="inventoryForm"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                Create Inventory
            </button>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
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

    <!-- Form -->
    <form id="inventoryForm" action="{{ route('admin.inventory.store') }}" method="POST" enctype="multipart/form-data"
          class="space-y-6">
        @csrf

        <!-- Section: Basic Information -->
        <div class="bg-white rounded-xl shadow p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h2>
                <div class="space-y-4">
                    <!-- Inventory Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Inventory Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Enter jewelry name" required>
                        @error('name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Jewelry Type *</label>
                        <select name="type" id="type" onchange="showTypeSpecificFields()" required
                                class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Type</option>
                            <option value="RingItem" {{ old('type') == 'RingItem' ? 'selected' : '' }}>Ring</option>
                            <option value="NecklaceItem" {{ old('type') == 'NecklaceItem' ? 'selected' : '' }}>Necklace</option>
                            <option value="EarringsItem" {{ old('type') == 'EarringsItem' ? 'selected' : '' }}>Earrings</option>
                            <option value="BraceletItem" {{ old('type') == 'BraceletItem' ? 'selected' : '' }}>Bracelet</option>
                        </select>
                        @error('type')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Description</label>
                        <textarea name="description" rows="3"
                                  class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Enter product description">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div>
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Specifications</h2>
                <div class="space-y-4">
                    <!-- Price -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Price Range (RM)</label>
                        <input type="text" id="price" name="price" readonly
                            class="mt-1 w-full border-gray-300 rounded-lg shadow-sm bg-gray-100 cursor-not-allowed"
                            placeholder="Select type to see price">
                    </div>


                    <!-- Quantity -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Quantity</label>
                        <input type="number" name="quantity" id="quantity" value="0"
                            class="mt-1 w-full border-gray-300 rounded-lg shadow-sm bg-gray-100 cursor-not-allowed"
                            placeholder="0" readonly>
                    </div>


                    <!-- Min Stock Level -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Minimum Stock</label>
                        <input type="number" name="min_stock_level" value="{{ old('min_stock_level') }}"
                               class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="0">
                        @error('min_stock_level')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Status</label>
                        <select name="status"
                                class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                        </select>
                        @error('status')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Section: Type-Specific Fields -->
        <div id="type-specific-fields" class="bg-white rounded-xl shadow p-6 hidden">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Type-Specific Attributes</h2>

            <!-- Ring Fields -->
            <div id="ring-fields" class="type-fields hidden grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium">Stone Type</label>
                    <select id="stone_type" name="stone_type" class="w-full border rounded p-2">
                        <option value="">Select Stone Type</option>
                        <option value="Diamond">Diamond</option>
                        <option value="Ruby">Ruby</option>
                        <option value="Sapphire">Sapphire</option>
                        <option value="Emerald">Emerald</option>
                        <option value="Pearl">Pearl</option>
                        <option value="Amethyst">Amethyst</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium">Ring Size</label>
                    <select id="ring_size" name="ring_size" class="w-full border rounded p-2">
                        <option value="">Select Size</option>
                        <option value="4">Size 4</option>
                        <option value="5">Size 5</option>
                        <option value="6">Size 6</option>
                        <option value="7">Size 7</option>
                        <option value="8">Size 8</option>
                        <option value="9">Size 9</option>
                        <option value="10">Size 10</option>
                    </select>
                </div>
            </div>

            <!-- Necklace Fields -->
            <div id="necklace-fields" class="type-fields hidden grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium">Necklace Length (cm)</label>
                    <select name="necklace_length" class="w-full border rounded p-2">
                        <option value="">Select Length</option>
                        <option value="30">30 cm</option>
                        <option value="35">35 cm</option>
                        <option value="40">40 cm</option>
                        <option value="45">45 cm</option>
                        <option value="50">50 cm</option>
                        <option value="55">55 cm</option>
                        <option value="60">60 cm</option>
                        <option value="65">65 cm</option>
                        <option value="70">70 cm</option>
                        <option value="75">75 cm</option>
                        <option value="80">80 cm</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium">Has Pendant</label>
                    <select name="has_pendant" class="w-full border rounded p-2">
                        <option value="">Select Option</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>

            <!-- Earrings Fields -->
            <div id="earrings-fields" class="type-fields hidden grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium">Earring Style</label>
                    <select name="earring_style" class="w-full border rounded p-2">
                        <option value="">Select Style</option>
                        <option value="Stud">Stud</option>
                        <option value="Hoop">Hoop</option>
                        <option value="Drop">Drop</option>
                        <option value="Chandelier">Chandelier</option>
                        <option value="Cluster">Cluster</option>
                        <option value="Dangle">Dangle</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium">Is Pair</label>
                    <select name="is_pair" class="w-full border rounded p-2">
                        <option value="">Select Option</option>
                        <option value="1">Yes (Pair)</option>
                        <option value="0">No (Single)</option>
                    </select>
                </div>
            </div>

            <!-- Bracelet Fields -->
            <div id="bracelet-fields" class="type-fields hidden grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium">Clasp Type</label>
                    <select name="bracelet_clasp" class="w-full border rounded p-2">
                        <option value="">Select Clasp Type</option>
                        <option value="Standard">Standard</option>
                        <option value="Magnetic">Magnetic</option>
                        <option value="Toggle">Toggle</option>
                        <option value="Lobster">Lobster</option>
                        <option value="Box">Box</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium">Adjustable</label>
                    <select name="adjustable" class="w-full border rounded p-2">
                        <option value="">Select Option</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>



        <!-- Section: Variations -->
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Variations</h2>
                <button type="button" onclick="addVariation()"
                        class="px-3 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                    + Add Variation
                </button>
            </div>

            @if ($errors->has('variations'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                    {{ $errors->first('variations') }}
                </div>
            @endif

            <div id="variations-container" class="space-y-4">
                <div class="variation-item border p-4 rounded-lg shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                        <input type="text" name="variations[0][sku]" placeholder="SKU" class="input-field">
                        <input type="text" name="variations[0][color]" placeholder="Color" class="input-field">
                       <!-- Size (read-only from ring_size if type is Ring) -->
                        <input type="text" name="variations[0][size]" placeholder="Size"
                            value="{{ old('ring_size', $inventory->ring_size ?? '') }}"
                            class="input-field @if(old('type', $inventory->type ?? '') == 'RingItem') bg-gray-100 cursor-not-allowed @endif"
                            @if(old('type', $inventory->type ?? '') == 'RingItem') readonly @endif>

                        <!-- Material (read-only from stone_type if type is Ring) -->
                        <input type="text" name="variations[0][material]" placeholder="Material"
                            value="{{ old('stone_type', $inventory->stone_type ?? '') }}"
                            class="input-field @if(old('type', $inventory->type ?? '') == 'RingItem') bg-gray-100 cursor-not-allowed @endif"
                            @if(old('type', $inventory->type ?? '') == 'RingItem') readonly @endif>

                       
                        <input type="number" name="variations[0][stock]" placeholder="Stock" class="input-field">
                    </div>
                    <div class="mt-3">
                        <input type="file" name="variations[0][image_path]"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                               file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let variationIndex = 1;

const priceRanges = {
    RingItem: '600 - 1000',
    NecklaceItem: '400 - 1100',
    EarringsItem: '400 - 600',
    BraceletItem: '50 - 100'
};

function showTypeSpecificFields() {
    const type = document.getElementById('type').value;
    const section = document.getElementById('type-specific-fields');
    document.querySelectorAll('.type-fields').forEach(el => el.classList.add('hidden'));

    const priceField = document.getElementById('price');
    priceField.value = priceRanges[type] || '';

    if (type) {
        section.classList.remove('hidden');
        document.getElementById(type.replace('Item', '').toLowerCase() + '-fields')?.classList.remove('hidden');
    } else {
        section.classList.add('hidden');
    }
}

function syncFirstVariation() {
    const type = document.getElementById('type').value;
    const firstVariation = document.querySelector('.variation-item');
    if (!firstVariation) return;

    const sizeInput = firstVariation.querySelector('input[name="variations[0][size]"]');
    const materialInput = firstVariation.querySelector('input[name="variations[0][material]"]');

    if (type === 'RingItem') {
        // Set fields to ring values and make read-only
        const ringSize = document.getElementById('ring_size').value;
        const stoneType = document.getElementById('stone_type').value;

        if (sizeInput) {
            sizeInput.value = ringSize;
            sizeInput.readOnly = true;
            sizeInput.classList.add('bg-gray-100', 'cursor-not-allowed');
        }
        if (materialInput) {
            materialInput.value = stoneType;
            materialInput.readOnly = true;
            materialInput.classList.add('bg-gray-100', 'cursor-not-allowed');
        }
    } else {
        // Reset fields and make editable
        if (sizeInput) {
            sizeInput.readOnly = false;
            sizeInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
            sizeInput.value = '';
        }
        if (materialInput) {
            materialInput.readOnly = false;
            materialInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
            materialInput.value = '';
        }
    }
}

function addVariation() {
    const container = document.getElementById('variations-container');
    const type = document.getElementById('type').value;

    // Grab defaults from the top section
    const ringSize = document.getElementById('ring_size')?.value || '';
    const stoneType = document.getElementById('stone_type')?.value || '';

    let extraFields = '';

    if (type === 'RingItem') {
    extraFields = `

        <select name="variations[${variationIndex}][size]" class="input-field">
            <option value="">Select Size</option>
            ${[...Array(7)].map((_,i)=> {
                const size = i+4;
                return `<option value="${size}" ${ringSize == size ? 'selected' : ''}>${size}</option>`;
            }).join('')}
        </select>
        <select name="variations[${variationIndex}][material]" class="input-field">
            <option value="">Select Stone</option>
            <option value="Diamond" ${stoneType === 'Diamond' ? 'selected' : ''}>Diamond</option>
            <option value="Ruby" ${stoneType === 'Ruby' ? 'selected' : ''}>Ruby</option>
            <option value="Sapphire" ${stoneType === 'Sapphire' ? 'selected' : ''}>Sapphire</option>
            <option value="Emerald" ${stoneType === 'Emerald' ? 'selected' : ''}>Emerald</option>
            <option value="Pearl" ${stoneType === 'Pearl' ? 'selected' : ''}>Pearl</option>
            <option value="Amethyst" ${stoneType === 'Amethyst' ? 'selected' : ''}>Amethyst</option>
        </select>
    `;
    }else {
    extraFields = `
        <input type="text" name="variations[${variationIndex}][size]" placeholder="Size" class="input-field">
        <input type="text" name="variations[${variationIndex}][material]" placeholder="Material" class="input-field">
    `;
    }

    const div = document.createElement('div');
    div.className = 'variation-item border p-4 rounded-lg shadow-sm';
    div.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <input type="text" name="variations[${variationIndex}][sku]" placeholder="SKU" class="input-field">
            <input type="text" name="variations[${variationIndex}][color]" placeholder="Color" class="input-field">
            ${extraFields}
            <input type="number" name="variations[${variationIndex}][stock]" placeholder="Stock" class="input-field">
        </div>
        <div class="mt-3 flex justify-between items-center">
            <input type="file" name="variations[${variationIndex}][image_path]"
                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg
                   file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            <button type="button" class="ml-4 px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600"
                    onclick="removeVariation(this)">
                Remove
            </button>
        </div>`;
    container.appendChild(div);
    variationIndex++;
    updateQuantity();
}

function removeVariation(button) {
    button.closest('.variation-item').remove();
    updateQuantity();
}

function updateQuantity() {
    let total = 0;
    document.querySelectorAll('input[name^="variations"][name$="[stock]"]').forEach(input => {
        total += parseInt(input.value) || 0;
    });
    document.getElementById('quantity').value = total;
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    showTypeSpecificFields();
    syncFirstVariation();
});

document.getElementById('ring_size')?.addEventListener('change', syncFirstVariation);
document.getElementById('stone_type')?.addEventListener('change', syncFirstVariation);
document.getElementById('type')?.addEventListener('change', syncFirstVariation);

document.addEventListener('input', function(e) {
    if (e.target.name && e.target.name.includes('[stock]')) {
        updateQuantity();
    }
});
</script>

<style>
.input-field {
    @apply w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500;
}
</style>
@endsection

