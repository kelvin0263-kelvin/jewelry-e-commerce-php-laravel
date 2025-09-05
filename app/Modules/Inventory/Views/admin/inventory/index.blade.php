@extends('layouts.admin')

@section('title', 'Inventory Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">

        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Inventory Management</h1>
                    <p class="text-gray-600 mt-1">Manage all inventories and their variations here.</p>
                </div>
                <a href="{{ route('admin.inventory.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700">+ Add Inventory</a>
            </div>
        </div>

        <!-- Inventory Table Card -->
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
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $inv->id }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-800">{{ $inv->name }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-medium">{{ $inv->type }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $inv->quantity ?? $inv->total_stock }}</td>
                            <td class="px-4 py-3 text-sm">
                                <form action="{{ route('admin.inventory.toggleStatus', $inv->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="px-3 py-1 rounded-full text-white text-sm font-medium 
                                        {{ $inv->status === 'published' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }}">
                                        {{ ucfirst($inv->status) }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($inv->variations->count() > 0)
                                    <ul class="space-y-1">
                                        @foreach($inv->variations as $var)
                                            <li class="bg-gray-50 px-2 py-1 rounded border text-gray-700 text-xs">
                                                <strong>SKU:</strong> {{ $var->sku }} |
                                                <strong>Material:</strong> {{ $var->material ?? 'N/A' }} |
                                                <strong>Stock:</strong> {{ $var->stock }} |
                                                <strong>Price:</strong> ${{ number_format($var->price, 2) }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-gray-400 italic text-xs">No variations</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm flex gap-2">
                                <a href="{{ route('admin.inventory.edit', $inv->id) }}" class="px-2 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 text-xs">Edit</a>
                                <form action="{{ route('admin.inventory.destroy', $inv->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-xs" onclick="return confirm('Are you sure you want to delete this inventory?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(method_exists($inventories, 'links'))
            <div class="mt-6">
                {{ $inventories->links() }}
            </div>
        @endif

    </div>
</div>
@endsection
