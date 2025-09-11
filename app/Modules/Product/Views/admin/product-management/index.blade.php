@extends('layouts.admin')

@section('title', 'Product Management')

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
                            <i class="fas fa-gem me-1"></i>Product Management
                        </h1>
                        <p class="mb-0 opacity-75" style="font-size: 0.7rem;">Manage products based on inventory SKU variations</p>
                    </div>
                    <div class="d-flex gap-1">
                        <!-- Create product functionality removed -->
                    </div>
                </div>
            </div>
        </div>
    
    @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('inventory_changes'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <h6 class="alert-heading">
                <i class="fas fa-info-circle me-2"></i>Inventory Changes Detected
            </h6>
            <p class="mb-2">
                <strong>SKU:</strong> {{ session('inventory_changes.sku') }} - 
                <strong>Product:</strong> {{ session('inventory_changes.name') }}
            </p>
            <p class="mb-2">
                <strong>Updated:</strong> {{ session('inventory_changes.updated_at') }}
            </p>
            <p class="mb-0">
                <strong>Changes:</strong> {{ implode(', ', session('inventory_changes.changes')) }}
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
                    <div class="col-md-3">
                        <label for="search" class="form-label fw-semibold text-dark" style="font-size: 0.75rem;">Search</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-0" style="font-size: 0.75rem; padding: 0.4rem 0.6rem;">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-0 shadow-sm" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Search products..." style="font-size: 0.75rem; padding: 0.4rem 0.6rem;">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="category" class="form-label fw-semibold text-dark" style="font-size: 0.75rem;">Category</label>
                        <select class="form-select border-0 shadow-sm" id="category" name="category" style="font-size: 0.75rem; padding: 0.4rem 0.6rem;">
                            <option value="all" {{ request('category') == 'all' ? 'selected' : '' }}>All Categories</option>
                            <option value="earring" {{ request('category') == 'earring' ? 'selected' : '' }}>Earring</option>
                            <option value="bracelet" {{ request('category') == 'bracelet' ? 'selected' : '' }}>Bracelet</option>
                            <option value="necklace" {{ request('category') == 'necklace' ? 'selected' : '' }}>Necklace</option>
                            <option value="ring" {{ request('category') == 'ring' ? 'selected' : '' }}>Ring</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label fw-semibold text-dark" style="font-size: 0.75rem;">Status</label>
                        <select class="form-select border-0 shadow-sm" id="status" name="status" style="font-size: 0.75rem; padding: 0.4rem 0.6rem;">
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark" style="font-size: 0.75rem;">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary shadow-sm" style="font-size: 0.75rem; padding: 0.4rem 0.6rem;">
                                <i class="fas fa-search me-1"></i>Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
    </div>


        <!-- Products Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-1">
                <h6 class="mb-0 text-dark fw-semibold small">
                    <i class="fas fa-list me-2 text-primary"></i>Products List
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="d-flex justify-content-between align-items-center px-3 py-1 bg-light border-bottom">
                    <span class="text-muted small">
                        <i class="fas fa-box me-1"></i>{{ $products->total() }} products
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 table-sm">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem; width: 150px; min-width: 150px;">
                                    ID (SKU)
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
                                    Quantity
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem;">
                                    Features
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem;">
                                    Description
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem;">
                                    Media
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem;">
                                    Status
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem;">
                                    Published By
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="width: 120px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.85rem;">
                                    Published At
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem;">
                                    Status
                                </th>
                                <th class="py-1 px-2 fw-bold text-dark text-center" style="width: 300px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.85rem;">
                                    Actions
                                </th>
            </tr>
        </thead>
        <tbody>
                            @forelse ($products as $product)
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
                                        <span class="text-dark" style="font-size: 0.7rem;">{{ $product->quantity }}</span>
                                    </td>
                                    <td class="border-end py-1 px-2 align-middle text-center">
                                        <div class="d-flex flex-column gap-1">
                                            @foreach($product->features ?? [] as $feature)
                                                <span class="badge bg-light text-dark border" style="font-size: 0.6rem;">{{ $feature }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="border-end py-1 px-2 align-middle text-center">
                                        <span class="text-muted" style="font-size: 0.7rem;">{{ $product->description ?: 'None' }}</span>
                                    </td>
                                    <td class="border-end py-1 px-2 align-middle text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <span class="badge bg-light text-dark border" style="font-size: 0.7rem;">
                                                <i class="fas fa-image me-1"></i>
                                                {{ count($product->customer_images ?? []) }} img
                                            </span>
                                            <span class="badge bg-light text-dark border" style="font-size: 0.7rem;">
                                                <i class="fas fa-video me-1"></i>
                                                {{ $product->product_video ? '1 vid' : '0 vid' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="border-end py-1 px-2 align-middle text-center">
                                        @if($product->status === 'published')
                                            <span class="badge" style="background-color: #28a745; color: white; font-size: 0.7rem;">
                                                Published
                                            </span>
                                        @else
                                            <span class="badge" style="background-color: #ffc107; color: #212529; font-size: 0.7rem;">
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="border-end py-1 px-2 align-middle text-center">
                                        <span class="text-muted" style="font-size: 0.7rem;">{{ $product->published_by }}</span>
                                    </td>
                                    <td class="border-end py-1 px-2 align-middle text-center" style="width: 150px;">
                                        <span class="text-muted" style="font-size: 0.7rem;">
                                            @if($product->published_at)
                                                {{ $product->published_at->format('M d, Y H:i') }}
                                            @else
                                                Not published
                                            @endif
                                        </span>
                                    </td>
                                    <td class="border-end py-1 px-2 align-middle text-center">
                                        @if($product->product_record && $product->product_record->marketing_description)
                                            <span class="badge bg-success">Complete</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Incomplete</span>
                                        @endif
                                    </td>
                                    <td class="py-1 px-2 align-middle text-center" style="width: 400px;">
                                        <div class="d-flex gap-1 flex-wrap justify-content-center">
                                            <!-- Create button - always show -->
                                            @if($product->product_record)
                                                <a href="{{ route('admin.product-management.enhance', $product->product_record) }}" 
                                                   class="btn btn-sm btn-success">
                                                    <i class="fas fa-plus-circle me-1"></i>Create
                                                </a>
                                            @else
                                                <a href="{{ route('admin.product-management.create', $product->id) }}" 
                                                   class="btn btn-sm btn-success">
                                                    <i class="fas fa-plus-circle me-1"></i>Create
                                                </a>
                                            @endif
                                            
                                            <!-- View button - always show -->
                                            <a href="{{ route('admin.product-management.show', $product->id) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye me-1"></i>View
                                            </a>
                                            
                                            <!-- Edit button - always show -->
                                            @if($product->product_record)
                                                <a href="{{ route('admin.product-management.edit', $product->product_record) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </a>
                                            @else
                                                <a href="{{ route('admin.product-management.create', $product->id) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </a>
                                            @endif
                                            
                                            <!-- Publish/Unpublish buttons - always show -->
                                            @if($product->product_record)
                                                @if($product->status === 'published')
                                                    <form action="{{ route('admin.product-management.unpublish', $product->product_record) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-warning" 
                                                                onclick="return confirm('Are you sure you want to unpublish this product?')"
                                                                title="Unpublish Product">
                                                            <i class="fas fa-arrow-down"></i>Unpublish
                                                        </button>
                                                    </form>
                                                @elseif($product->status === 'issued')
                                                    <button type="button" class="btn btn-sm btn-secondary" disabled
                                                            title="Product has been delisted from inventory. Please republish from inventory module first.">
                                                        <i class="fas fa-ban"></i>Delisted
                                                    </button>
                                                @else
                                                    <form action="{{ route('admin.product-management.publish', $product->product_record) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success" 
                                                                onclick="return confirm('Are you sure you want to publish this product?')"
                                                                title="Publish Product">
                                                            <i class="fas fa-arrow-up"></i>Publish
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                <form action="{{ route('admin.product-management.create', $product->id) }}" method="GET" class="d-inline">
                                                    <button type="submit" class="btn btn-sm btn-success" 
                                                            title="Please create customer information first">
                                                        <i class="fas fa-arrow-up"></i>Publish
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
            @empty
                <tr>
                                    <td colspan="13" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="mb-4">
                                                <div class="empty-state-icon">
                                                    <i class="fas fa-box-open text-muted" style="font-size: 4rem; opacity: 0.5;"></i>
                                                </div>
                                            </div>
                                            <h4 class="text-muted mb-3 fw-bold">No Products Found</h4>
                                            <p class="text-muted mb-4 fs-5">No inventory variations are available at the moment</p>
                                        </div>
                                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="d-flex justify-content-center mt-2">
                <div class="pagination-container" style="padding: 0.8rem 1.2rem;">
                    {{ $products->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.75rem 0;
            margin-bottom: 0.75rem;
            border-radius: 0 0 8px 8px;
            position: relative;
            overflow: hidden;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 50%, rgba(255,255,255,0.1) 100%);
            animation: shimmer 3s infinite;
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
            font-size: 0.75rem;
            min-width: 90px;
            white-space: nowrap;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 0.375rem 0.75rem;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
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
        
        .reviews-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            display: inline-block;
            letter-spacing: 0.5px;
        }
        
        .reviews-btn:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        .reviews-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
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
@endsection

