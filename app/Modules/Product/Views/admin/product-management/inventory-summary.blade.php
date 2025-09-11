@extends('layouts.admin')

@section('title', 'Product Management - Inventory Summary')

@push('styles')
    <!-- Bootstrap CSS for buttons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid" style="max-width: 1400px; margin: 0 auto;">
        <!-- Enhanced Page Header -->
        <div class="page-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h5 mb-1 fw-bold">
                            <i class="fas fa-gem me-1"></i>Inventory Summary
                        </h1>
                        <p class="mb-0 opacity-75" style="font-size: 0.7rem;">Manage products by inventory groups</p>
                    </div>
                    <div class="d-flex gap-1">
                        <!-- No additional buttons needed for inventory summary -->
                    </div>
                </div>
            </div>
        </div>
    
    @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show auto-dismiss" role="alert" data-dismiss-delay="5000">
            {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show auto-dismiss" role="alert" data-dismiss-delay="5000">
            {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(isset($messages['inventory_changes']))
        @php
            $changes = $messages['inventory_changes'];
        @endphp
        <div class="alert alert-info alert-dismissible fade show auto-dismiss" role="alert" data-dismiss-delay="5000">
            <h6 class="alert-heading">
                <i class="fas fa-info-circle me-2"></i>Inventory Changes Detected
            </h6>
            <p class="mb-2">
                <strong>SKU:</strong> {{ is_array($changes) ? $changes['sku'] : 'N/A' }} - 
                <strong>Product:</strong> {{ is_array($changes) ? $changes['name'] : 'N/A' }}
            </p>
            <p class="mb-0">
                <strong>Changes:</strong> {{ is_array($changes) ? (is_array($changes['changes']) ? implode(', ', $changes['changes']) : $changes['changes']) : 'Changes detected' }}
            </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(isset($messages['inventory_unpublished']))
        @php
            $unpublished = $messages['inventory_unpublished'];
        @endphp
        <div class="alert alert-warning alert-dismissible fade show auto-dismiss" role="alert" data-dismiss-delay="5000">
            <h6 class="alert-heading">
                <i class="fas fa-exclamation-triangle me-2"></i>Products Delisted
            </h6>
            <p class="mb-2">
                <strong>Inventory:</strong> {{ is_array($unpublished) ? $unpublished['inventory_name'] : 'N/A' }}
            </p>
            <p class="mb-0">
                <strong>Changes:</strong> {{ is_array($unpublished) ? $unpublished['message'] : 'Products have been delisted' }}
            </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(isset($messages['new_product_added']))
        @php
            $newProduct = $messages['new_product_added'];
        @endphp
        <div class="alert alert-success alert-dismissible fade show auto-dismiss" role="alert" data-dismiss-delay="5000">
            <h6 class="alert-heading">
                <i class="fas fa-plus-circle me-2"></i>New Product Added
            </h6>
            <p class="mb-2">
                <strong>Product:</strong> {{ is_array($newProduct) ? $newProduct['name'] : 'N/A' }}
            </p>
            <p class="mb-0">
                <strong>Changes:</strong> {{ is_array($newProduct) ? $newProduct['changes'] : 'New product added' }}
            </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(isset($messages['inventory_republished']))
        @php
            $republished = $messages['inventory_republished'];
        @endphp
        <div class="alert alert-info alert-dismissible fade show auto-dismiss" role="alert" data-dismiss-delay="5000">
            <h6 class="alert-heading">
                <i class="fas fa-arrow-up me-2"></i>Products Relisted
            </h6>
            <p class="mb-2">
                <strong>Inventory:</strong> {{ is_array($republished) ? $republished['inventory_name'] : 'N/A' }}
            </p>
            <p class="mb-0">
                <strong>Changes:</strong> {{ is_array($republished) ? $republished['message'] : 'Products have been relisted' }}
            </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

        <!-- Filters and Search -->
        <div class="card border-0 shadow-sm mb-2">
            <div class="card-header bg-white border-0 py-2">
                <h6 class="mb-0 text-dark fw-semibold" style="font-size: 0.8rem;">
                    <i class="fas fa-filter me-2 text-primary"></i>Filter & Search
                </h6>
            </div>
            <div class="card-body bg-light py-2">
                <form method="GET" action="{{ route('admin.product-management.index') }}" class="row g-2">
                    <div class="col-md-4">
                        <label for="search" class="form-label fw-semibold text-dark" style="font-size: 0.75rem;">Search</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-0" style="font-size: 0.75rem; padding: 0.4rem 0.6rem;">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-0 shadow-sm" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Search inventories..." style="font-size: 0.75rem; padding: 0.4rem 0.6rem;">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="category" class="form-label fw-semibold text-dark" style="font-size: 0.75rem;">Category</label>
                        <select class="form-select border-0 shadow-sm" id="category" name="category" style="font-size: 0.75rem; padding: 0.4rem 0.6rem;">
                            <option value="all" {{ request('category') == 'all' ? 'selected' : '' }}>All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ ucfirst($category) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-dark" style="font-size: 0.75rem;">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary shadow-sm" style="font-size: 0.75rem; padding: 0.4rem 0.6rem;">
                                <i class="fas fa-search me-1"></i>Filter
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark" style="font-size: 0.75rem;">&nbsp;</label>
                        <div class="d-grid">
                            <a href="{{ route('admin.product-management.index') }}" class="btn btn-outline-secondary shadow-sm" style="font-size: 0.75rem; padding: 0.4rem 0.6rem;">
                                <i class="fas fa-times me-1"></i>Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Inventory Summary Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-1">
                <h6 class="mb-0 text-dark fw-semibold small">
                    <i class="fas fa-list me-2 text-primary"></i>Inventory Summary
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="d-flex justify-content-between align-items-center px-3 py-1 bg-light border-bottom">
                    <span class="text-muted small">
                        <i class="fas fa-box me-1"></i>{{ $inventories->total() }} inventories
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 table-sm">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem; width: 80px; min-width: 80px;">
                                    ID
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem; width: 200px; min-width: 200px;">
                                    Product Name
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem;">
                                    Type
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem;">
                                    Total Stock
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem;">
                                    Published SKUs
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem;">
                                    Total SKUs
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem;">
                                    Status
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem;">
                                    Published By
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem;">
                                    Published At
                                </th>
                                <th class="py-1 px-2 fw-bold text-dark text-center" style="width: 200px; min-width: 200px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem;">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($inventories as $inventory)
                                <tr class="border-bottom hover-row">
                                    <td class="border-end py-1 px-2 align-middle text-center" style="width: 80px; min-width: 80px;">
                                        <span class="text-dark" style="font-size: 0.7rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;" title="{{ $inventory->id }}">{{ $inventory->id }}</span>
                                    </td>
                                    <td class="border-end py-1 px-2 align-middle text-center" style="width: 200px; min-width: 200px;">
                                        <span class="text-dark" style="font-size: 0.7rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;" title="{{ $inventory->name }}">{{ $inventory->name }}</span>
                                    </td>
                                    <td class="border-end py-1 px-2 align-middle text-center">
                                        <span class="badge bg-light text-dark border" style="font-size: 0.7rem;">{{ ucfirst(str_replace('Item', '', strtolower($inventory->type))) }}</span>
                                    </td>
                                    <td class="border-end py-1 px-2 align-middle text-center">
                                        <span class="text-dark" style="font-size: 0.7rem;">{{ number_format($inventory->total_stock) }}</span>
                                    </td>
                                    <td class="border-end py-1 px-2 align-middle text-center">
                                        <span class="text-dark" style="font-size: 0.7rem;">{{ $inventory->published_count }}</span>
                                    </td>
                                    <td class="border-end py-1 px-2 align-middle text-center">
                                        <span class="text-dark" style="font-size: 0.7rem;">{{ $inventory->total_variations }}</span>
                                    </td>
                                    <td class="border-end py-1 px-2 align-middle text-center">
                                        @if($inventory->status === 'published')
                                            <span class="badge bg-success" style="font-size: 0.7rem;">Published</span>
                                        @elseif($inventory->status === 'pending')
                                            <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">Pending</span>
                                        @elseif($inventory->status === 'rejected')
                                            <span class="badge bg-danger" style="font-size: 0.7rem;">Rejected</span>
                                        @else
                                            <span class="badge bg-secondary" style="font-size: 0.7rem;">Draft</span>
                                        @endif
                                    </td>
                                    <td class="border-end py-1 px-2 align-middle text-center">
                                        <span class="text-muted" style="font-size: 0.7rem;">{{ $inventory->published_by ?: 'Not published' }}</span>
                                    </td>
                                    <td class="border-end py-1 px-2 align-middle text-center">
                                        <span class="text-muted" style="font-size: 0.7rem;">{{ $inventory->published_at ? $inventory->published_at->format('M d, Y') : 'Not published' }}</span>
                                    </td>
                                    <td class="py-1 px-2 align-middle text-center" style="width: 200px; min-width: 200px;">
                                        <div class="d-flex flex-column gap-1 justify-content-center align-items-center">
                                            <a href="{{ route('admin.product-management.index', ['inventory_id' => $inventory->id]) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye me-1"></i> View SKUs
                                            </a>
                                            <button onclick="checkAndViewProduct({{ $inventory->id }})" 
                                                    class="btn btn-sm btn-info">
                                                <i class="fas fa-external-link-alt me-1"></i> View Product
                                            </button>
                                            @if($inventory->status === 'published')
                                                <form action="{{ route('admin.product-management.unpublish-inventory', $inventory->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-warning" 
                                                            onclick="return confirm('Are you sure you want to unpublish this inventory?')">
                                                        <i class="fas fa-eye-slash me-1"></i> Unpublish
                                                    </button>
                                                </form>
                                            @elseif($inventory->status === 'rejected')
                                                <button class="btn btn-sm btn-outline-secondary" disabled 
                                                        title="Inventory has been rejected. Please contact administrator or create a new inventory.">
                                                    <i class="fas fa-arrow-up me-1"></i> Publish
                                                </button>
                                            @elseif($inventory->has_user_facing_info)
                                                <form action="{{ route('admin.product-management.publish-inventory', $inventory->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" 
                                                            onclick="return confirm('Are you sure you want to publish this inventory?')">
                                                        <i class="fas fa-arrow-up me-1"></i> Publish
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-sm btn-outline-secondary" disabled 
                                                        title="Please create user-facing information for at least one SKU first">
                                                    <i class="fas fa-arrow-up me-1"></i> Publish
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <i class="fas fa-box-open text-muted mb-2" style="font-size: 2rem;"></i>
                                        <h5 class="text-muted" style="font-size: 0.9rem;">No Inventory Found</h5>
                                        <p class="text-muted" style="font-size: 0.75rem;">No inventory items match your search criteria.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
        <!-- Pagination -->
        @if($inventories->hasPages())
            <div class="d-flex justify-content-center mt-2">
                <div class="pagination-container" style="padding: 0.8rem 1.2rem;">
                    {{ $inventories->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        @endif
            </div>
        </div>

        <!-- Issued Products Table -->
        @if($issuedProducts->count() > 0)
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-0 py-1">
                    <h6 class="mb-0 text-dark fw-semibold small">
                        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>Issued Products
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="d-flex justify-content-between align-items-center px-3 py-1 bg-light border-bottom">
                        <span class="text-muted small">
                            <i class="fas fa-box me-1"></i>{{ $issuedProducts->count() }} issued products
                        </span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem; width: 150px; min-width: 150px;">
                                        SKU
                                    </th>
                                    <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem; width: 200px; min-width: 200px;">
                                        Product Name
                                    </th>
                                    <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem;">
                                        Price
                                    </th>
                                    <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem;">
                                        Category
                                    </th>
                                    <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem;">
                                        Issued At
                                    </th>
                                    <th class="py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem;">
                                        Reason
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($issuedProducts as $product)
                                    <tr class="border-bottom hover-row">
                                        <td class="border-end py-1 px-2 align-middle text-center" style="width: 150px; min-width: 150px;">
                                            <span class="text-dark" style="font-size: 0.7rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;" title="{{ $product->sku }}">{{ $product->sku }}</span>
                                        </td>
                                        <td class="border-end py-1 px-2 align-middle text-center" style="width: 200px; min-width: 200px;">
                                            <span class="text-dark" style="font-size: 0.7rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;" title="{{ $product->name }}">{{ $product->name }}</span>
                                        </td>
                                        <td class="border-end py-1 px-2 align-middle text-center">
                                            <span class="text-dark" style="font-size: 0.7rem;">RM{{ number_format($product->price, 2) }}</span>
                                        </td>
                                        <td class="border-end py-1 px-2 align-middle text-center">
                                            <span class="badge bg-light text-dark border" style="font-size: 0.7rem;">{{ ucfirst($product->category) }}</span>
                                        </td>
                                        <td class="border-end py-1 px-2 align-middle text-center">
                                            <span class="text-muted" style="font-size: 0.7rem;">{{ $product->issued_at ? $product->issued_at->format('M d, Y H:i') : 'Unknown' }}</span>
                                        </td>
                                        <td class="py-1 px-2 align-middle text-center">
                                            <span class="text-dark" style="font-size: 0.7rem;">Inventory delisted</span>
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
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white border-0 py-1">
                <h6 class="mb-0 text-dark fw-semibold small">
                    <i class="fas fa-star me-2 text-warning"></i>Reviews Management
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="d-flex justify-content-between align-items-center px-3 py-1 bg-light border-bottom">
                    <span class="text-muted small">
                        <i class="fas fa-comment me-1"></i>{{ $reviews->total() }} reviews
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 table-sm">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem; width: 200px; min-width: 200px;">
                                    Product
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem; width: 150px; min-width: 150px;">
                                    User Name
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem; width: 100px; min-width: 100px;">
                                    Rating
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem; width: 200px; min-width: 200px;">
                                    Review Title
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem;">
                                    Review Content
                                </th>
                                <th class="py-1 px-2 fw-bold text-dark text-center" style="width: 100px; min-width: 100px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem;">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reviews as $review)
                                <tr class="border-bottom hover-row">
                                    <!-- Product -->
                                    <td class="border-end py-1 px-2 align-middle text-center" style="width: 200px; min-width: 200px;">
                                        @if($review->product)
                                            <div class="text-dark" style="font-size: 0.7rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;" title="{{ $review->product->name }}">
                                                {{ $review->product->name }}
                                            </div>
                                            <div class="text-muted" style="font-size: 0.6rem;">ID: {{ $review->product->id }}</div>
                                        @else
                                            <span class="text-danger" style="font-size: 0.7rem; font-style: italic;">Product not found</span>
                                        @endif
                                    </td>

                                    <!-- User Name -->
                                    <td class="border-end py-1 px-2 align-middle text-center" style="width: 150px; min-width: 150px;">
                                        <span class="text-dark fw-medium" style="font-size: 0.7rem;">{{ $review->reviewer_name }}</span>
                                    </td>

                                    <!-- Rating -->
                                    <td class="border-end py-1 px-2 align-middle text-center" style="width: 100px; min-width: 100px;">
                                        <div class="d-flex align-items-center justify-content-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <svg class="w-3 h-3 text-warning me-1" fill="currentColor" viewBox="0 0 20 20" style="width: 12px; height: 12px;">
                                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-3 h-3 text-muted me-1" fill="currentColor" viewBox="0 0 20 20" style="width: 12px; height: 12px;">
                                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                    </svg>
                                                @endif
                                            @endfor
                                            <span class="text-muted ms-1" style="font-size: 0.6rem;">{{ $review->rating }}/5</span>
                                        </div>
                                    </td>

                                    <!-- Review Title -->
                                    <td class="border-end py-1 px-2 align-middle text-center" style="width: 200px; min-width: 200px;">
                                        <div class="text-dark fw-medium" style="font-size: 0.7rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;" title="{{ $review->title }}">
                                            {{ $review->title }}
                                        </div>
                                    </td>

                                    <!-- Review Content -->
                                    <td class="border-end py-1 px-2 align-middle text-center">
                                        <div class="text-muted" style="font-size: 0.7rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block; max-width: 300px;" title="{{ $review->content }}">
                                            {{ Str::limit($review->content, 80) }}
                                        </div>
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-1 px-2 align-middle text-center" style="width: 100px; min-width: 100px;">
                                        <div class="d-flex justify-content-center">
                                            <!-- Reject Button -->
                                            <form action="{{ route('admin.product-management.rejectReview', $review->id) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to reject this review? This action cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-danger" 
                                                        style="font-size: 0.65rem; padding: 0.25rem 0.5rem; height: 24px;">
                                                    <i class="fas fa-times me-1"></i>Reject
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="mb-2">
                                                <i class="fas fa-star text-muted" style="font-size: 2rem; opacity: 0.5;"></i>
                                            </div>
                                            <h5 class="text-muted mb-2" style="font-size: 0.9rem;">No Reviews Found</h5>
                                            <p class="text-muted mb-0" style="font-size: 0.75rem;">No reviews have been submitted yet.</p>
                                        </div>
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
            <div class="d-flex justify-content-center mt-2">
                <div class="pagination-container" style="padding: 0.8rem 1.2rem;">
                    {{ $reviews->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

    <!-- Bootstrap JS for alerts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Styles -->
    <style>
 body a,
body a:link,
body a:visited,
body a:hover,
body a:active {
    text-decoration: none;
    color: black
}

        /* Enhanced Page Header */
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            padding: 0.75rem 0 !important;
            margin-bottom: 0.75rem !important;
            border-radius: 0 0 8px 8px !important;
            position: relative !important;
            overflow: hidden !important;
        }
        
        .page-header::before {
            content: '' !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 50%, rgba(255,255,255,0.1) 100%) !important;
            animation: shimmer 3s infinite !important;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .page-header > * {
            position: relative;
            z-index: 1;
        }
        
        .table {
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table thead th {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .table tbody td {
            border: 1px solid #dee2e6;
            vertical-align: middle;
            font-size: 0.75rem;
        }
        
        .table tbody td small {
            font-size: 0.75rem;
        }
        
        .table tbody td .badge {
            font-size: 0.75rem;
        }
        
        .d-flex.gap-1 .btn {
            font-size: 0.65rem;
            width: 80px;
            min-width: 80px;
            max-width: 80px;
            white-space: nowrap;
            border-radius: 4px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 0.25rem 0.5rem;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        
        .d-flex.gap-1 .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .hover-row:hover {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            transform: scale(1.01);
            transition: all 0.3s ease;
        }
        
        .badge {
            font-weight: 500;
            padding: 0.4em 0.6em;
            font-size: 0.75rem;
            border-radius: 6px;
        }
        
        .card {
            border-radius: 12px;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
        }
        
        .btn {
            border-radius: 8px;
        }
        
        .table {
            border-radius: 12px;
            overflow: hidden;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        /* Enhanced Pagination */
        .pagination-container {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 1.5rem 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border: 1px solid #dee2e6;
        }
        
        .pagination .page-link {
            border: 1px solid #dee2e6;
            color: #495057;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.6rem 1rem;
            margin: 0 0.2rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: white;
        }
        
        .pagination .page-link:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .pagination .page-item.disabled .page-link {
            background: #f8f9fa;
            color: #6c757d;
            border-color: #dee2e6;
            cursor: not-allowed;
        }
        
        .pagination .page-item.disabled .page-link:hover {
            background: #f8f9fa;
            color: #6c757d;
            border-color: #dee2e6;
            transform: none;
            box-shadow: none;
        }
    </style>

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
    </script>
@endsection
