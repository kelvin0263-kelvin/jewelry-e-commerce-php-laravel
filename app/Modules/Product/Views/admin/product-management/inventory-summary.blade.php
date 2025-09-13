@extends('layouts.admin')

@section('title', 'Product Management - Inventory Summary')

@section('content')
    <!-- Header -->
    <div class="mb-6 flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-gem mr-2"></i>Inventory Summary
            </h1>
            <p class="text-gray-600 mt-1 text-sm">Manage products by inventory groups</p>
        </div>
    </div>
    
    @if(session('success'))
        <div class="mb-4 rounded-md bg-green-50 p-4 auto-dismiss" data-dismiss-delay="5000">
            <div class="flex">
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif
    
    @if(session('error'))
        <div class="mb-4 rounded-md bg-red-50 p-4 auto-dismiss" data-dismiss-delay="5000">
            <div class="flex">
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(isset($messages['inventory_changes']) && !empty($messages['inventory_changes']))
        @php
            $changes = $messages['inventory_changes'];
        @endphp
        <div class="mb-4 rounded-md bg-blue-50 p-4 auto-dismiss" data-dismiss-delay="5000">
            <div class="flex">
                <div class="ml-3">
                    <h6 class="text-sm font-medium text-blue-800 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>Inventory Changes Detected
                    </h6>
                    <p class="mt-1 text-sm text-blue-700">
                        <strong>SKU:</strong> {{ (is_array($changes) && isset($changes['sku'])) ? $changes['sku'] : 'N/A' }} - 
                        <strong>Product:</strong> {{ (is_array($changes) && isset($changes['name'])) ? $changes['name'] : 'N/A' }}
                    </p>
                    <p class="mt-1 text-sm text-blue-700">
                        <strong>Changes:</strong> {{ (is_array($changes) && isset($changes['changes'])) ? (is_array($changes['changes']) ? implode(', ', $changes['changes']) : $changes['changes']) : 'Changes detected' }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if(isset($messages['inventory_unpublished']) && !empty($messages['inventory_unpublished']))
        @php
            $unpublished = $messages['inventory_unpublished'];
        @endphp
        <div class="mb-4 rounded-md bg-yellow-50 p-4 auto-dismiss" data-dismiss-delay="5000">
            <div class="flex">
                <div class="ml-3">
                    <h6 class="text-sm font-medium text-yellow-800 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Products Delisted
                    </h6>
                    <p class="mt-1 text-sm text-yellow-700">
                        <strong>Changes:</strong> {{ (is_array($unpublished) && isset($unpublished['changes'])) ? $unpublished['changes'] : 'Products have been delisted' }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if(isset($messages['new_product_added']) && !empty($messages['new_product_added']))
        @php
            $newProduct = $messages['new_product_added'];
        @endphp
        <div class="mb-4 rounded-md bg-green-50 p-4 auto-dismiss" data-dismiss-delay="5000">
            <div class="flex">
                <div class="ml-3">
                    <h6 class="text-sm font-medium text-green-800 flex items-center">
                        <i class="fas fa-plus-circle mr-2"></i>New Product Added
                    </h6>
                    <p class="mt-1 text-sm text-green-700">
                        <strong>Product:</strong> {{ (is_array($newProduct) && isset($newProduct['name'])) ? $newProduct['name'] : 'N/A' }}
                    </p>
                    <p class="mt-1 text-sm text-green-700">
                        <strong>Changes:</strong> {{ (is_array($newProduct) && isset($newProduct['changes'])) ? $newProduct['changes'] : 'New product added' }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if(isset($messages['inventory_republished']) && !empty($messages['inventory_republished']))
        @php
            $republished = $messages['inventory_republished'];
        @endphp
        <div class="mb-4 rounded-md bg-blue-50 p-4 auto-dismiss" data-dismiss-delay="5000">
            <div class="flex">
                <div class="ml-3">
                    <h6 class="text-sm font-medium text-blue-800 flex items-center">
                        <i class="fas fa-arrow-up mr-2"></i>Products Relisted
                    </h6>
                    <p class="mt-1 text-sm text-blue-700">
                        <strong>Changes:</strong> {{ (is_array($republished) && isset($republished['changes'])) ? $republished['changes'] : 'Products have been relisted' }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Search + Summary -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4">
            <h6 class="mb-4 text-gray-800 font-semibold flex items-center">
                <i class="fas fa-filter mr-2 text-blue-500"></i>Filter & Search
            </h6>
            <form method="GET" action="{{ route('admin.product-management.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="search" name="search" value="{{ request('search') }}" 
                               placeholder="Search inventories..." 
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select id="category" name="category" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="all" {{ request('category') == 'all' ? 'selected' : '' }}>All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ ucfirst($category) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">&nbsp;</label>
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-search mr-1"></i>Filter
                    </button>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">&nbsp;</label>
                    <a href="{{ route('admin.product-management.index') }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        <i class="fas fa-times mr-1"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Inventory Summary Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h6 class="text-lg font-medium text-gray-800 flex items-center">
                <i class="fas fa-list mr-2 text-blue-500"></i>Inventory Summary
            </h6>
        </div>
        <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
            <span class="text-sm text-gray-600 flex items-center">
                <i class="fas fa-box mr-1"></i>{{ $inventories->total() }} inventories
            </span>
        </div>
        <div class="product-module-table" style="overflow: hidden !important; position: relative;">
            <div class="table-scroll-container" style="overflow-x: auto; overflow-y: hidden; scrollbar-width: none; -ms-overflow-style: none; -webkit-scrollbar: none; width: 100%;">
                <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Published SKUs</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total SKUs</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Published By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Published At</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($inventories as $inventory)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 w-20">
                                {{ $inventory->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $inventory->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst(str_replace('Item', '', strtolower($inventory->type))) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($inventory->total_stock) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $inventory->published_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $inventory->total_variations }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($inventory->status === 'published')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Published</span>
                                @elseif($inventory->status === 'pending')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($inventory->status === 'rejected')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Rejected</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Draft</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $inventory->published_by ?: 'Not published' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $inventory->published_at ? $inventory->published_at->format('M d, Y') : 'Not published' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex flex-col space-y-1">
                                    <a href="{{ route('admin.product-management.index', ['inventory_id' => $inventory->id]) }}" 
                                       class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 w-24 justify-center">
                                        <i class="fas fa-eye mr-1"></i> View SKUs
                                    </a>
                                    <button onclick="checkAndViewProduct({{ $inventory->id }})" 
                                            class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-cyan-500 hover:bg-cyan-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 w-24 justify-center">
                                        <i class="fas fa-external-link-alt mr-1"></i> View Product
                                    </button>
                                    @if($inventory->status === 'published')
                                        <form action="{{ route('admin.product-management.unpublish-inventory', $inventory->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 w-24 justify-center" 
                                                    onclick="return confirm('Are you sure you want to unpublish this inventory?')">
                                                <i class="fas fa-eye-slash mr-1"></i> Unpublish
                                            </button>
                                        </form>
                                    @elseif($inventory->status === 'rejected')
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-400 w-24 justify-center" title="Inventory has been rejected. Please contact administrator or create a new inventory.">
                                            <i class="fas fa-arrow-up mr-1"></i> Publish
                                        </span>
                                    @elseif($inventory->total_stock <= 0)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-600 w-24 justify-center" title="该产品没有stock - Cannot publish inventory with zero stock">
                                            <i class="fas fa-exclamation-triangle mr-1"></i> No Stock
                                        </span>
                                    @elseif($inventory->has_user_facing_info)
                                        <form action="{{ route('admin.product-management.publish-inventory', $inventory->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-cyan-500 hover:bg-cyan-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 w-24 justify-center" 
                                                    onclick="return confirm('Are you sure you want to publish this inventory?')">
                                                <i class="fas fa-arrow-up mr-1"></i> Publish
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-400 w-24 justify-center" title="Please create user-facing information for at least one SKU first">
                                            <i class="fas fa-arrow-up mr-1"></i> Publish
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-10 text-center text-gray-500">
                                <i class="fas fa-box-open text-gray-400 mb-2 text-3xl"></i>
                                <h5 class="text-gray-600 text-lg font-medium mb-2">No Inventory Found</h5>
                                <p class="text-gray-500 text-sm">No inventory items match your search criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        @if($inventories->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                {{ $inventories->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

        <!-- Issued Products Table -->
        @if(isset($issuedProducts) && $issuedProducts->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h6 class="text-lg font-medium text-gray-800 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2 text-yellow-500"></i>Previously Published Products
                    </h6>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
                    <span class="text-sm text-gray-600 flex items-center">
                        <i class="fas fa-box mr-1"></i>{{ $issuedProducts->count() }} previously published products (now delisted)
                    </span>
                </div>
                <div class="product-module-table" style="overflow: hidden !important; position: relative;">
                    <div class="table-scroll-container" style="overflow-x: auto; overflow-y: hidden; scrollbar-width: none; -ms-overflow-style: none; -webkit-scrollbar: none; width: 100%;">
                        <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issued At</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($issuedProducts as $product)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $product->sku }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        RM{{ number_format($product->price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ ucfirst($product->category) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $product->issued_at ? $product->issued_at->format('M d, Y H:i') : 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Previously published, now delisted
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Reviews Management Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden mt-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h6 class="text-lg font-medium text-gray-800 flex items-center">
                    <i class="fas fa-star mr-2 text-yellow-500"></i>Reviews Management
                </h6>
            </div>
            <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
                <span class="text-sm text-gray-600 flex items-center">
                    <i class="fas fa-comment mr-1"></i>{{ $reviews->total() }} reviews
                </span>
            </div>
            <div class="product-module-table" style="overflow: hidden !important; position: relative;">
                <div class="table-scroll-container" style="overflow-x: auto; overflow-y: hidden; scrollbar-width: none; -ms-overflow-style: none; -webkit-scrollbar: none; width: 100%;">
                    <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Review Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Review Content</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($reviews as $review)
                            <tr class="hover:bg-gray-50">
                                <!-- Product -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($review->product)
                                        <div class="text-sm font-medium text-gray-900">{{ $review->product->name }}</div>
                                        <div class="text-sm text-gray-500">ID: {{ $review->product->id }}</div>
                                    @else
                                        <span class="text-sm text-red-600 italic">Product not found</span>
                                    @endif
                                </td>

                                <!-- User Name -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $review->reviewer_name }}
                                </td>

                                <!-- Rating -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-gray-300 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                            @endif
                                        @endfor
                                        <span class="text-sm text-gray-500 ml-1">{{ $review->rating }}/5</span>
                                    </div>
                                </td>

                                <!-- Review Title -->
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $review->title }}</div>
                                </td>

                                <!-- Review Content -->
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500">{{ Str::limit($review->content, 80) }}</div>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <form action="{{ route('admin.product-management.rejectReview', $review->id) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Are you sure you want to reject this review? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 w-20 justify-center">
                                            <i class="fas fa-times mr-1"></i>Reject
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                    <i class="fas fa-star text-gray-400 mb-2 text-3xl"></i>
                                    <h5 class="text-gray-600 text-lg font-medium mb-2">No Reviews Found</h5>
                                    <p class="text-gray-500 text-sm">No reviews have been submitted yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Reviews Pagination -->
        @if($reviews->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                {{ $reviews->appends(request()->query())->links() }}
            </div>
        @endif
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-dismiss alerts after specified delay
            const autoDismissAlerts = document.querySelectorAll('.auto-dismiss');
            
            autoDismissAlerts.forEach(function(alert) {
                const delay = parseInt(alert.getAttribute('data-dismiss-delay')) || 5000;
                
                setTimeout(function() {
                    if (alert && alert.parentNode) {
                        // Add fade out effect
                        alert.classList.add('fade');
                        alert.classList.remove('show');
                        
                        // Remove from DOM after fade animation
                        setTimeout(function() {
                            if (alert && alert.parentNode) {
                                alert.parentNode.removeChild(alert);
                            }
                        }, 150); // Wait for fade animation to complete
                    }
                }, delay);
            });
        });

        // Function to check product status and view product
        function checkAndViewProduct(inventoryId) {
            // Show loading state
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Checking...';
            button.disabled = true;

            // Make AJAX request
            fetch(`/api/products/check-published/${inventoryId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.published) {
                        // Product is published, open in new tab
                        window.open(data.url, '_blank');
                    } else {
                        // Product not published, show alert
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while checking product status');
                })
                .finally(() => {
                    // Restore button state
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
        }
        
        // 强制隐藏滚动条 - 使用更激进的方法
        document.addEventListener('DOMContentLoaded', function() {
            const tables = document.querySelectorAll('.product-module-table');
            const scrollContainers = document.querySelectorAll('.table-scroll-container');
            
            // 处理主表格容器
            tables.forEach(function(table) {
                table.style.overflow = 'hidden';
                table.style.position = 'relative';
            });
            
            // 处理滚动容器
            scrollContainers.forEach(function(container) {
                container.style.scrollbarWidth = 'none';
                container.style.msOverflowStyle = 'none';
                container.style.overflowX = 'auto';
                container.style.overflowY = 'hidden';
                
                // 强制应用样式
                container.setAttribute('style', container.getAttribute('style') + '; scrollbar-width: none !important; -ms-overflow-style: none !important;');
            });
            
            // 添加全局CSS来强制隐藏滚动条
            const style = document.createElement('style');
            style.textContent = `
                .table-scroll-container::-webkit-scrollbar {
                    display: none !important;
                    width: 0 !important;
                    height: 0 !important;
                    background: transparent !important;
                }
                .table-scroll-container::-webkit-scrollbar:horizontal {
                    display: none !important;
                    height: 0 !important;
                    width: 0 !important;
                }
                .table-scroll-container::-webkit-scrollbar-track {
                    display: none !important;
                    width: 0 !important;
                    height: 0 !important;
                }
                .table-scroll-container::-webkit-scrollbar-thumb {
                    display: none !important;
                    width: 0 !important;
                    height: 0 !important;
                }
                .table-scroll-container::-webkit-scrollbar-corner {
                    display: none !important;
                }
                .table-scroll-container::-webkit-scrollbar-button {
                    display: none !important;
                    width: 0 !important;
                    height: 0 !important;
                }
            `;
            document.head.appendChild(style);
        });
    </script>
@endsection

@push('styles')
<style>
    /* 完全隐藏滚动条 - 使用嵌套div方法 */
    .product-module-table {
        overflow: hidden !important;
        position: relative !important;
    }
    
    .table-scroll-container {
        overflow-x: auto !important;
        overflow-y: hidden !important;
        scrollbar-width: none !important;
        -ms-overflow-style: none !important;
        width: 100% !important;
        height: 100% !important;
    }
    
    /* 隐藏所有webkit滚动条 */
    .table-scroll-container::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
        background: transparent !important;
    }
    
    .table-scroll-container::-webkit-scrollbar:horizontal {
        display: none !important;
        height: 0 !important;
        width: 0 !important;
    }
    
    .table-scroll-container::-webkit-scrollbar-track {
        display: none !important;
        background: transparent !important;
        width: 0 !important;
        height: 0 !important;
    }
    
    .table-scroll-container::-webkit-scrollbar-thumb {
        display: none !important;
        background: transparent !important;
        width: 0 !important;
        height: 0 !important;
    }
    
    .table-scroll-container::-webkit-scrollbar-corner {
        display: none !important;
        background: transparent !important;
    }
    
    .table-scroll-container::-webkit-scrollbar-button {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }
    
    .table-scroll-container::-webkit-scrollbar-button:horizontal {
        display: none !important;
        height: 0 !important;
    }
    
    .table-scroll-container::-webkit-scrollbar-track-piece {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }
    
    .table-scroll-container::-webkit-scrollbar-track-piece:horizontal {
        display: none !important;
        height: 0 !important;
    }
    
    /* 强制隐藏所有滚动条元素 */
    .table-scroll-container *::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }
    
    .table-scroll-container *::-webkit-scrollbar-track {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }
    
    .table-scroll-container *::-webkit-scrollbar-thumb {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }
    
    /* 确保没有滚动条空间被保留 */
    .table-scroll-container {
        scrollbar-gutter: stable !important;
        scrollbar-color: transparent transparent !important;
    }
    
    /* 覆盖任何现有的滚动条样式 */
    .table-scroll-container[style*="scrollbar"] {
        scrollbar-width: none !important;
        -ms-overflow-style: none !important;
    }
    
    /* 额外的webkit滚动条隐藏 */
    .table-scroll-container {
        -webkit-scrollbar-width: none !important;
        -webkit-scrollbar-height: none !important;
    }
</style>
@endpush
