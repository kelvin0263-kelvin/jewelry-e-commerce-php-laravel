@extends('layouts.admin')

@section('title', 'Inventory Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Inventory Dashboard</h1>

    <!-- Action Buttons -->
    <div class="flex gap-4 mb-6">
        <a href="{{ route('admin.inventory.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
            + Add Product
        </a>
        <a href="{{ route('admin.inventory.index') }}"
           class="px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition">
            View Products
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-gray-500">Total Products</p>
            <p class="text-2xl font-bold">{{ $totalProducts }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-gray-500">Total Variations</p>
            <p class="text-2xl font-bold">{{ $totalVariations }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-gray-500">Total Stock</p>
            <p class="text-2xl font-bold">{{ $totalStock }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-gray-500">Low Stock Items</p>
            <p class="text-2xl font-bold">{{ $lowStockCount }}</p>
        </div>
    </div>
</div>
@endsection
