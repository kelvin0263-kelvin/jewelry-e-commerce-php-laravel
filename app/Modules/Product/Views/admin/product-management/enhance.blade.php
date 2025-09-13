@extends('layouts.admin')

@section('title', 'Create Product Information')

@section('content')
    <!-- Header -->
    <div class="mb-6 flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-plus-circle mr-2"></i>Create Product Information
            </h1>
            <p class="text-gray-600 mt-1 text-sm">Add marketing details and customer-facing information</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.product-management.sku-details', $product->variation->inventory_id) }}" 
               class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <i class="fas fa-arrow-left mr-1"></i>Back
            </a>
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
                        <div class="w-2/3 text-gray-900">{{ $transformedProduct->variation->sku }}</div>
                    </div>
                    
                    <div class="flex">
                        <div class="w-1/3 font-medium text-gray-700">Product Name:</div>
                        <div class="w-2/3 text-gray-900">{{ $transformedProduct->variation->inventory->name }}</div>
                    </div>
                    
                    <div class="flex">
                        <div class="w-1/3 font-medium text-gray-700">Cost:</div>
                        <div class="w-2/3 text-gray-900">RM{{ number_format($transformedProduct->price, 2) }}</div>
                    </div>
                    
                    <div class="flex">
                        <div class="w-1/3 font-medium text-gray-700">Type:</div>
                        <div class="w-2/3 text-gray-900">{{ str_replace('Item', '', $transformedProduct->variation->inventory->type) }}</div>
                    </div>
                    
                    <div class="flex">
                        <div class="w-1/3 font-medium text-gray-700">Quantity:</div>
                        <div class="w-2/3 text-gray-900">{{ $transformedProduct->variation->stock }} units</div>
                    </div>
                    
                    <div class="flex">
                        <div class="w-1/3 font-medium text-gray-700">Features:</div>
                        <div class="w-2/3">
                            @foreach($transformedProduct->features ?? [] as $feature)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mr-1 mb-1">{{ $feature }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Create Product Information Form -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 bg-green-600">
                <h3 class="text-lg font-medium text-white flex items-center">
                    <i class="fas fa-edit mr-2"></i>Create Product Information
                </h3>
            </div>
            <div class="p-4">
                <!-- Product Information (Read-only) -->
                <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                    <h6 class="text-gray-600 mb-3 text-sm font-medium">Product Information for SKU: {{ $transformedProduct->variation->sku }} (Read-only)</h6>
                    <div class="space-y-2 text-sm">
                        <div class="flex">
                            <div class="w-1/3 font-medium text-gray-700">SKU:</div>
                            <div class="w-2/3 text-gray-900">{{ $transformedProduct->sku }}</div>
                        </div>
                        <div class="flex">
                            <div class="w-1/3 font-medium text-gray-700">Product Name:</div>
                            <div class="w-2/3 text-gray-900">{{ $transformedProduct->name }}</div>
                        </div>
                        <div class="flex">
                            <div class="w-1/3 font-medium text-gray-700">Category:</div>
                            <div class="w-2/3 text-gray-900">{{ ucfirst($transformedProduct->category) }}</div>
                        </div>
                        <div class="flex">
                            <div class="w-1/3 font-medium text-gray-700">Quantity:</div>
                            <div class="w-2/3 text-gray-900">{{ $transformedProduct->quantity }}</div>
                        </div>
                        <div class="flex">
                            <div class="w-1/3 font-medium text-gray-700">Features:</div>
                            <div class="w-2/3">
                                @foreach($transformedProduct->features ?? [] as $feature)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mr-1 mb-1">{{ $feature }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                        @if($product->marketing_description && $product->marketing_description !== 'None')
                            <!-- Show existing customer information (read-only) -->
                            <div class="mb-4 p-3 bg-gray-50 rounded-lg" style="font-size: 0.8rem;">
                                <h6 class="text-gray-500 mb-3 text-sm font-medium">Existing Customer Information (Read-only)</h6>
                                
                                <div class="mb-3">
                                    <strong>Marketing Description:</strong>
                                    <p class="mt-2 p-2 bg-white rounded border">{{ $product->marketing_description }}</p>
                                </div>

                                @if($product->discount_price)
                                <div class="mb-3">
                                    <strong>Discounted Price:</strong>
                                    <p class="mt-2 text-lg font-semibold text-green-600">RM{{ number_format($product->discount_price, 2) }}</p>
                                </div>
                                @endif

                                <div class="mb-3">
                                    <strong>Images:</strong>
                                    <div class="mt-2">
                                        @if($product->customer_images && count($product->customer_images) > 0)
                                            <div class="grid grid-cols-4 gap-2">
                                                @foreach($product->customer_images as $image)
                                                    <div>
                                                        <img src="{{ asset('storage/' . $image) }}" alt="Product Image" class="w-full h-20 object-cover rounded border">
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-gray-500 text-sm">No images uploaded</p>
                                        @endif
                                    </div>
                                </div>

                            </div>

                            <div class="mt-4">
                                <a href="{{ route('admin.product-management.edit', $product) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-edit mr-1"></i>Edit Information
                                </a>
                            </div>
                        @else
                            <!-- Show create form -->
                            <form action="{{ route('admin.product-management.store-enhancement', $product) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="mb-4">
                                    <label for="marketing_description" class="block text-sm font-medium text-gray-700 mb-1">
                                        <strong>Marketing Description for SKU: {{ $transformedProduct->variation->sku }} *</strong>
                                    </label>
                                    <textarea class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('marketing_description') border-red-300 @enderror" 
                                              id="marketing_description" name="marketing_description" rows="3" required 
                                              minlength="10">{{ old('marketing_description') }}</textarea>
                                    <p class="mt-1 text-xs text-gray-500">This description will be shown to customers on the website for this specific SKU only. Minimum 10 characters required.</p>
                                    @error('marketing_description')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="selling_price" class="block text-sm font-medium text-gray-700 mb-1">
                                        <strong>Selling Price for SKU: {{ $transformedProduct->variation->sku }} *</strong>
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">RM</span>
                                        </div>
                                        <input type="number" step="0.01" min="0" 
                                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('selling_price') border-red-300 @enderror" 
                                               id="selling_price" name="selling_price" 
                                               value="{{ old('selling_price') }}" required>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">The price customers will pay for this SKU. *Required</p>
                                    @error('selling_price')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="discount_price" class="block text-sm font-medium text-gray-700 mb-1">
                                        <strong>Discounted Price for SKU: {{ $transformedProduct->variation->sku }}</strong>
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">RM</span>
                                        </div>
                                        <input type="number" step="0.01" min="0" 
                                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('discount_price') border-red-300 @enderror" 
                                               id="discount_price" name="discount_price" 
                                               value="{{ old('discount_price') }}">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Optional. Leave empty if no discount for this SKU.</p>
                                    @error('discount_price')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="customer_images" class="block text-sm font-medium text-gray-700 mb-1">
                                        <strong>Images for SKU: {{ $transformedProduct->variation->sku }} *</strong>
                                    </label>
                                    <input type="file" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('customer_images') border-red-300 @enderror" 
                                           id="customer_images" name="customer_images[]" multiple accept="image/*" required>
                                    <p class="mt-1 text-xs text-gray-500">Upload up to 5 images for this specific SKU. Supported formats: JPEG, PNG, JPG, GIF, WebP. Max size: 2MB per image. *Required</p>
                                    @error('customer_images')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>


                                <div class="flex space-x-3">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <i class="fas fa-save mr-1"></i>Create Information for SKU: {{ $transformedProduct->variation->sku }}
                                    </button>
                                    <a href="{{ route('admin.product-management.sku-details', $product->variation->inventory_id) }}" 
                                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                        <i class="fas fa-times mr-1"></i>Cancel
                                    </a>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const marketingDescription = document.getElementById('marketing_description');
    
    if (form && marketingDescription) {
        form.addEventListener('submit', function(e) {
            const description = marketingDescription.value.trim();
            
            if (description.length < 10) {
                e.preventDefault();
                alert('Marketing description must be at least 10 characters long.');
                marketingDescription.focus();
                return false;
            }
        });
        
        // Real-time validation
        marketingDescription.addEventListener('input', function() {
            const description = this.value.trim();
            const isValid = description.length >= 10;
            
            if (isValid) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        });
    }
});
</script>
@endpush