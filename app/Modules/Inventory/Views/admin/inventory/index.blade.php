@extends('layouts.admin')

@section('title', 'Inventory Management')

@section('content')

<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">

        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md mb-6 p-6 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Edit Inventory</h1>

            <div class="flex gap-2">
                <a href="{{ route('admin.inventory.dashboard') }}" 
                class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                    ← Back
                </a>

                <a href="{{ route('admin.inventory.create') }}" 
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    + Add Inventory
                </a>
            </div>
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

        <!-- Inventory Table -->
        <div class="bg-white rounded-lg shadow-md overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">ID</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Name</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Type</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Quantity</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Variations</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($inventories as $inv)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <!-- ID -->
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $inv->id }}</td>

                            <!-- Name -->
                            <td class="px-4 py-3 text-sm font-semibold text-gray-800">{{ $inv->name }}</td>

                            <!-- Type -->
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-medium">
                                    {{ $inv->type }}
                                </span>
                            </td>

                            <!-- Quantity -->
                            <!-- Quantity -->
                            <td class="px-4 py-3 text-sm {{ $inv->total_quantity <= $inv->min_stock_level ? 'text-red-600 font-bold' : 'text-gray-700' }}">
                                {{ $inv->total_quantity ?? 0 }}
                            </td>


                            <!-- Status -->
                            <td class="px-4 py-3 text-sm">
                                <form action="{{ route('admin.inventory.toggleStatus', $inv->id) }}"
                                      method="POST" class="inline-block">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit"
                                            class="px-3 py-1 rounded-full text-white text-sm font-medium 
                                                {{ $inv->status === 'published' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }}">
                                        {{ ucfirst($inv->status) }}
                                    </button>
                                </form>
                            </td>

                            <!-- Variations Column -->
                            <td class="px-4 py-3 text-sm">
                                @if($inv->variations->count() > 0)
                                    <!-- Toggle Button -->
                                    <button onclick="toggleVariations({{ $inv->id }})"
                                        class="px-3 py-1 bg-indigo-500 hover:bg-indigo-600 text-white text-xs rounded transition">
                                        {{ $inv->variations->count() }} Variation{{ $inv->variations->count() > 1 ? 's' : '' }}
                                    </button>

                                    <!-- Collapsible Variation Table -->
                                    <div id="variations-{{ $inv->id }}" class="hidden mt-3 border rounded bg-gray-50 p-2">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-100">
                                                <tr>
                                                    <th class="px-3 py-2 text-xs font-medium text-gray-700">SKU</th>
                                                    <th class="px-3 py-2 text-xs font-medium text-gray-700">Color</th>
                                                    <th class="px-3 py-2 text-xs font-medium text-gray-700">Size</th>
                                                    <th class="px-3 py-2 text-xs font-medium text-gray-700">Material</th>
                                                    <th class="px-3 py-2 text-xs font-medium text-gray-700">Stock</th>
                                                    <th class="px-3 py-2 text-xs font-medium text-gray-700">Price (RM)</th>
                                                    <th class="px-3 py-2 text-xs font-medium text-gray-700">Image</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($inv->variations as $var)
                                                    <tr class="hover:bg-gray-50 text-xs">
                                                        <td class="px-3 py-2">{{ $var->sku }}</td>
                                                        <td class="px-3 py-2">{{ $var->color ?? 'N/A' }}</td>
                                                        <td class="px-3 py-2">{{ $var->size ?? 'N/A' }}</td>
                                                        <td class="px-3 py-2">{{ $var->material ?? 'N/A' }}</td>
                                                        <td class="px-3 py-2">{{ $var->stock }}</td>
                                                        <td class="px-3 py-2">RM{{ number_format($var->price, 2) }}</td>
                                                        <td class="px-3 py-2">
                                                             @if($var->image_path)
                                                                <img src="{{ asset($var->image_path) }}"
                                                                    alt="Variation Image"
                                                                    class="mt-2 w-20 h-20 rounded border object-cover">
                                                            @else
                                                                <img src="{{ asset('images/no-image.png') }}"
                                                                    alt="No Image"
                                                                    class="mt-2 w-20 h-20 rounded border object-cover">
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic text-xs">No variations</span>
                                @endif
                            </td>

                            <!-- Actions Column -->
                            <td class="px-4 py-3 text-sm flex gap-2">
                                <!-- Edit Button -->
                                <form id="edit-form-{{ $inv->id }}" 
                                    action="{{ route('admin.inventory.toggleStatus', $inv->id) }}" 
                                    method="POST" 
                                    class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="redirect_edit" value="1">

                                   @if($inv->status === 'draft')
                                        <a href="{{ route('admin.inventory.edit', $inv->id) }}"
                                           class="px-2 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 text-xs">
                                           Edit
                                        </a>
                                    @else
                                        <a href="javascript:void(0)"
                                           class="px-2 py-1 bg-gray-400 text-white rounded cursor-not-allowed text-xs"
                                           onclick="alert('Set to Draft before editing!')">
                                           Edit
                                        </a>
                                    @endif
                                </form>

                                <!-- Delete Button -->
                                <form id="delete-form-{{ $inv->id }}"
                                      action="{{ route('admin.inventory.destroy', $inv->id) }}"
                                      method="POST"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="redirect_delete" value="1">

                                    @if($inv->status === 'draft')
                                        <button type="submit"
                                            class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-xs"
                                            onclick="return confirm('Are you sure you want to delete this inventory?')">
                                            Delete
                                        </button>
                                    @else
                                        <button type="button"
                                            class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-xs"
                                            onclick="confirmDelete({{ $inv->id }})">
                                            Delete
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(method_exists($inventories, 'links'))
            <div class="mt-6">
                {{ $inventories->links() }}
            </div>
        @endif

    </div>
</div>

<!-- JS for Collapsible Variations -->
<script>
function toggleVariations(id) {
    const section = document.getElementById('variations-' + id);
    section.classList.toggle('hidden');
}

function confirmDelete(id) {
    if (confirm("This inventory is published. To delete, we will set it to Draft first. Continue?")) {
        document.getElementById('delete-form-' + id).submit();
    }
}

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
