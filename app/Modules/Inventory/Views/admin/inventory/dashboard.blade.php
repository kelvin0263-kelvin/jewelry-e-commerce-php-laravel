@extends('layouts.admin')

@section('title', 'Inventory Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">

    <h1 class="text-3xl font-bold text-gray-800 mb-8">Inventory Dashboard</h1>

    <!-- Dashboard Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Create Inventory -->
        <a href="{{ route('admin.inventory.create') }}" class="group">
            <div class="bg-white rounded-xl shadow hover:shadow-xl transition p-6 flex flex-col items-center justify-center text-center">
                <img src="{{ asset('images/add-product.png') }}" alt="Add Product" class="w-20 h-20 mb-4">
                <h2 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600 transition">Create Inventory</h2>
                <p class="text-gray-500 mt-2">Add new products to your inventory quickly.</p>
            </div>
        </a>

        <!-- Manage Inventory -->
        <a href="{{ route('admin.inventory.index') }}" class="group">
            <div class="bg-white rounded-xl shadow hover:shadow-xl transition p-6 flex flex-col items-center justify-center text-center">
                <img src="{{ asset('images/Manage.png') }}" alt="View Inventory" class="w-20 h-20 mb-4">
                <h2 class="text-lg font-semibold text-gray-800 group-hover:text-green-600 transition">Edit Inventory</h2>
                <p class="text-gray-500 mt-2">Edit the products, variations, and stock levels.</p>
            </div>
        </a>

        <!-- View invnetory -->
        <a href="{{ route('admin.inventory.list') }}" class="group">
            <div class="bg-white rounded-xl shadow hover:shadow-xl transition p-6 flex flex-col items-center justify-center text-center">
                <img src="{{ asset('images/inventory.png') }}" alt="Inventory History" class="w-20 h-20 mb-4">
                <h2 class="text-lg font-semibold text-gray-800 group-hover:text-purple-600 transition">Inventory List</h2>
                <p class="text-gray-500 mt-2">View all the inventory items.</p>
            </div>
        </a>




    </div>

    <!-- Stats Section -->
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
