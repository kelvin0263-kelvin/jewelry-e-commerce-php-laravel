@extends('layouts.admin')

@section('title', 'View Product Information')

@section('content')
    <!-- Header -->
    <div class="mb-6 flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-eye mr-2"></i>View Product Information
            </h1>
            <p class="text-gray-600 mt-1 text-sm">View detailed product information and customer-facing content</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.product-management.sku-details', $product->variation->inventory_id) }}" 
               class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <i class="fas fa-arrow-left mr-1"></i>Back to SKU Details
            </a>
            @if($product->product)
                <a href="{{ route('admin.product-management.edit', $product->product) }}" 
                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-black bg-yellow-400 hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
            @endif
        </div>
    </div>
        
    @if(session('success'))
        <div class="mb-4 rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif
    
    @if(session('error'))
        <div class="mb-4 rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Side: Inventory Information -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 bg-blue-600">
                <h3 class="text-lg font-medium text-white flex items-center">
                    <i class="fas fa-box mr-2"></i>Inventory Information
                </h3>
            </div>
            <div class="p-4">
                <div class="space-y-3 text-sm">
                    <div class="flex">
                        <div class="w-1/3 font-medium text-gray-700">SKU:</div>
                        <div class="w-2/3 text-gray-900">{{ $product->variation->sku }}</div>
                    </div>
                    
                    <div class="flex">
                        <div class="w-1/3 font-medium text-gray-700">Product Name:</div>
                        <div class="w-2/3 text-gray-900">{{ $product->variation->inventory->name }}</div>
                    </div>
                    
                    <div class="flex">
                        <div class="w-1/3 font-medium text-gray-700">Type:</div>
                        <div class="w-2/3 text-gray-900">{{ str_replace('Item', '', $product->variation->inventory->type) }}</div>
                    </div>
                    
                    <div class="flex">
                        <div class="w-1/3 font-medium text-gray-700">Quantity:</div>
                        <div class="w-2/3 text-gray-900">{{ $product->variation->stock }} units</div>
                    </div>
                    
                    <div class="flex">
                        <div class="w-1/3 font-medium text-gray-700">Price:</div>
                        <div class="w-2/3 text-gray-900">RM{{ number_format($product->variation->price, 2) }}</div>
                    </div>
                    
                    <div class="flex">
                        <div class="w-1/3 font-medium text-gray-700">Features:</div>
                        <div class="w-2/3">
                            @foreach($product->features ?? [] as $feature)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mr-1 mb-1">{{ $feature }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Product Information -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 bg-green-600">
                <h3 class="text-lg font-medium text-white flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>Product Information
                </h3>
            </div>
            <div class="p-4">
                @if($product->product)
                    <!-- Product Information (Read-only) -->
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                        <h6 class="text-gray-600 mb-3 text-sm font-medium">Product Information</h6>
                        <div class="space-y-2 text-sm">
                            <div class="flex">
                                <div class="w-1/3 font-medium text-gray-700">SKU:</div>
                                <div class="w-2/3 text-gray-900">{{ $product->sku }}</div>
                            </div>
                            <div class="flex">
                                <div class="w-1/3 font-medium text-gray-700">Product Name:</div>
                                <div class="w-2/3 text-gray-900">{{ $product->name }}</div>
                            </div>
                            <div class="flex">
                                <div class="w-1/3 font-medium text-gray-700">Category:</div>
                                <div class="w-2/3 text-gray-900">{{ ucfirst($product->category) }}</div>
                            </div>
                            <div class="flex">
                                <div class="w-1/3 font-medium text-gray-700">Quantity:</div>
                                <div class="w-2/3 text-gray-900">{{ $product->quantity }}</div>
                            </div>
                            <div class="flex">
                                <div class="w-1/3 font-medium text-gray-700">Price:</div>
                                <div class="w-2/3 text-gray-900">RM{{ number_format($product->price, 2) }}</div>
                            </div>
                            <div class="flex">
                                <div class="w-1/3 font-medium text-gray-700">Features:</div>
                                <div class="w-2/3">
                                    @foreach($product->features ?? [] as $feature)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mr-1 mb-1">{{ $feature }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Marketing Information -->
                    <div class="mb-4 text-sm">
                        <h6 class="text-gray-600 mb-3 text-sm font-medium">Marketing Information</h6>
                        
                        <div class="mb-3">
                            <strong class="text-gray-700">Marketing Description:</strong>
                            <p class="mt-2 p-3 bg-gray-50 rounded text-sm">{{ $product->product->marketing_description ?: 'None' }}</p>
                        </div>

                        @if($product->product->discount_price)
                        <div class="mb-3">
                            <strong class="text-gray-700">Discounted Price:</strong>
                            <p class="mt-2 text-green-600 text-sm">RM{{ number_format($product->product->discount_price, 2) }}</p>
                        </div>
                        @endif

                        <div class="mb-3">
                            <strong class="text-gray-700">Images:</strong>
                            <div class="mt-2">
                                @if($product->product->customer_images && count($product->product->customer_images) > 0)
                                    <div class="grid grid-cols-3 gap-2">
                                        @foreach($product->product->customer_images as $image)
                                            <div>
                                                <img src="{{ asset('storage/' . $image) }}" alt="Product Image" class="w-full h-24 object-cover rounded border">
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500 text-sm">No images uploaded</p>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <strong class="text-gray-700">Status:</strong>
                            <div class="mt-2">
                                @if($product->status === 'published')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Published</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                @endif
                            </div>
                        </div>

                        @if($product->published_by && $product->published_by !== 'System')
                        <div class="mb-3">
                            <strong class="text-gray-700">Published By:</strong>
                            <p class="mt-2 text-sm">{{ $product->published_by }}</p>
                        </div>
                        @endif

                        @if($product->published_at)
                        <div class="mb-3">
                            <strong class="text-gray-700">Published At:</strong>
                            <p class="mt-2 text-sm">{{ $product->published_at->format('M d, Y H:i') }}</p>
                        </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mb-4 text-4xl"></i>
                        <h5 class="text-gray-600 text-lg font-medium mb-2">No Product Information Available</h5>
                        <p class="text-gray-500 text-sm mb-4">This inventory item has not been converted to a product yet.</p>
                        <a href="{{ route('admin.product-management.create', $product->id) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="fas fa-plus mr-1"></i>Create Product
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
