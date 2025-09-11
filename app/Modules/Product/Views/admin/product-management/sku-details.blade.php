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
                            <i class="fas fa-gem me-1"></i>SKU Details - {{ $inventory->name }}
                        </h1>
                        <p class="mb-0 opacity-75" style="font-size: 0.7rem;">Manage SKU variations for this inventory item</p>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.product-management.index') }}" class="custom-back-btn">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
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

    @if(session('inventory_changes'))
        <div class="alert alert-info alert-dismissible fade show auto-dismiss" role="alert" data-dismiss-delay="5000">
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
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem; width: 100px; min-width: 100px;">
                                    ID (SKU)
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem; width: 120px; min-width: 120px;">
                                    Product Name
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem; width: 100px; min-width: 100px;">
                                    Cost
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem; width: 100px; min-width: 100px;">
                                    Selling Price
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem; width: 120px; min-width: 120px;">
                                    Discounted Price
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem; width: 80px; min-width: 80px;">
                                    Category
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem; width: 80px; min-width: 80px;">
                                    Quantity
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem; width: 150px; min-width: 150px;">
                                    Features
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem; width: 120px; min-width: 120px;">
                                    Description
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem; width: 100px; min-width: 100px;">
                                    Media
                                </th>
                                <th class="border-end py-1 px-2 fw-bold text-dark text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem; width: 100px; min-width: 100px;">
                                    Status
                                </th>
                                <th class="py-1 px-2 fw-bold text-dark text-center" style="width: 200px; min-width: 200px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); font-size: 0.75rem;">
                                    Actions
                                </th>
            </tr>
        </thead>
        <tbody>
                            @forelse ($products as $product)
                                <tr class="border-bottom hover-row">
                                    <td class="border-end py-1 px-2 align-middle text-center" style="width: 100px; min-width: 100px;">
                                        <span class="text-dark" style="font-size: 0.7rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;" title="{{ $product->sku }}">{{ $product->sku }}</span>
                                    </td>
                                    <td class="border-end py-1 px-2 align-middle text-center" style="width: 120px; min-width: 120px;">
                                        <span class="text-dark" style="font-size: 0.7rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;" title="{{ $product->name }}">{{ $product->name }}</span>
                                    </td>
                                    <td class="border-end py-1 px-2 align-middle text-center">
                                        <span class="text-dark" style="font-size: 0.7rem;">RM{{ number_format($product->price, 2) }}</span>
                                    </td>
                                    <td class="border-end py-1 px-2 align-middle text-center">
                                        <span class="text-dark" style="font-size: 0.7rem;">
                                            @if($product->selling_price)
                                                RM{{ number_format($product->selling_price, 2) }}
                                            @else
                                                None
                                            @endif
                                        </span>
                                    </td>
                                    <td class="border-end py-1 px-2 align-middle text-center">
                                        <span class="text-dark" style="font-size: 0.7rem;">
                                            @if($product->discount_price)
                                                RM{{ number_format($product->discount_price, 2) }}
                                            @else
                                                None
                                            @endif
                                        </span>
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
                                                <span class="badge bg-light text-dark border" style="font-size: 0.7rem;">{{ $feature }}</span>
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
                                        @if($product->product_record && $product->product_record->marketing_description && $product->product_record->marketing_description !== 'None')
                                            <span class="badge bg-success" style="font-size: 0.7rem;">Complete</span>
                                        @else
                                            <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">Incomplete</span>
                                        @endif
                                    </td>
                                    <td class="py-1 px-2 align-middle text-center" style="width: 200px; min-width: 200px;">
                                        <div class="d-flex flex-column gap-1 justify-content-center align-items-center">
                                            <!-- Create Information button - always show -->
                                            @if($product->product_record)
                                                <a href="{{ route('admin.product-management.enhance', $product->product_record) }}" 
                                                   class="btn btn-sm btn-success">
                                                    <i class="fas fa-plus-circle me-1"></i>Create Info
                                                </a>
                                            @else
                                                <a href="{{ route('admin.product-management.create-info', $product->id) }}" 
                                                   class="btn btn-sm btn-success">
                                                    <i class="fas fa-plus-circle me-1"></i>Create Info
                                                </a>
                                            @endif
                                            
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
                                            
                                        </div>
                                    </td>
                                </tr>
            @empty
                <tr>
                                    <td colspan="9" class="text-center py-5">
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
        
        /* Custom Back Button - Completely isolated styling with maximum specificity */
        body .d-flex.gap-1 .custom-back-btn,
        .custom-back-btn {
            background: white !important;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            color: #2d3748 !important;
            font-weight: 500 !important;
            padding: 0.4rem 0.8rem !important;
            border-radius: 6px !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
            font-size: 0.75rem !important;
            line-height: 1.2 !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
            margin: 0 !important;
            width: auto !important;
            height: auto !important;
            min-width: auto !important;
            max-width: none !important;
        }
        
        body .d-flex.gap-1 .custom-back-btn:hover,
        .custom-back-btn:hover {
            background: #f7fafc !important;
            border-color: rgba(0, 0, 0, 0.12) !important;
            color: #2d3748 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08) !important;
            text-decoration: none !important;
        }
        
        .custom-back-btn:active {
            transform: translateY(0) !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
        }
        
        .custom-back-btn:focus {
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1) !important;
        }
        
        .custom-back-btn i {
            font-size: 0.7rem !important;
            margin-right: 0.3rem !important;
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
    </script>
@endsection

