@extends('layouts.admin')

@section('title', 'View Product Information')

@push('styles')
    <!-- Bootstrap CSS for buttons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Enhanced Page Header -->
        <div class="page-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h5 mb-1 fw-bold">
                            <i class="fas fa-eye me-1"></i>View Product Information
                        </h1>
                        <p class="mb-0 opacity-75" style="font-size: 0.8rem;">View detailed product information and customer-facing content</p>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.product-management.sku-details', $product->variation->inventory_id) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back to SKU Details
                        </a>
                        @if($product->product)
                            <a href="{{ route('admin.product-management.edit', $product->product) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                        @endif
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

        <div class="row">
            <!-- Left Side: Inventory Information (50%) -->
            <div class="col-md-6">
                <div class="card border h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-box me-2"></i>Inventory Information
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 0.75rem;">
                        <div class="row mb-2" style="font-size: 0.8rem;">
                            <div class="col-sm-4">
                                <strong>SKU:</strong>
                            </div>
                            <div class="col-sm-8">
                                {{ $product->variation->sku }}
                            </div>
                        </div>
                        
                        <div class="row mb-2" style="font-size: 0.8rem;">
                            <div class="col-sm-4">
                                <strong>Product Name:</strong>
                            </div>
                            <div class="col-sm-8">
                                {{ $product->variation->inventory->name }}
                            </div>
                        </div>
                        
                        <div class="row mb-2" style="font-size: 0.8rem;">
                            <div class="col-sm-4">
                                <strong>Type:</strong>
                            </div>
                            <div class="col-sm-8">
                                {{ str_replace('Item', '', $product->variation->inventory->type) }}
                            </div>
                        </div>
                        
                        <div class="row mb-2" style="font-size: 0.8rem;">
                            <div class="col-sm-4">
                                <strong>Quantity:</strong>
                            </div>
                            <div class="col-sm-8">
                                {{ $product->variation->stock }} units
                            </div>
                        </div>
                        
                        <div class="row mb-2" style="font-size: 0.8rem;">
                            <div class="col-sm-4">
                                <strong>Price:</strong>
                            </div>
                            <div class="col-sm-8">
                                RM{{ number_format($product->variation->price, 2) }}
                            </div>
                        </div>
                        
                        <div class="row mb-2" style="font-size: 0.8rem;">
                            <div class="col-sm-4">
                                <strong>Features:</strong>
                            </div>
                            <div class="col-sm-8">
                                @foreach($product->features ?? [] as $feature)
                                    <span class="badge bg-light text-dark border me-1 mb-1" style="font-size: 0.8rem;">{{ $feature }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Product Information (50%) -->
            <div class="col-md-6">
                <div class="card border h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>Product Information
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($product->product)
                            <!-- Product Information (Read-only) -->
                            <div class="mb-4 p-3 bg-light rounded" style="font-size: 0.8rem;">
                                <h6 class="text-muted mb-3" style="font-size: 0.8rem;">Product Information</h6>
                                <div class="row mb-2" style="font-size: 0.8rem;">
                                    <div class="col-sm-4"><strong>SKU:</strong></div>
                                    <div class="col-sm-8">{{ $product->sku }}</div>
                                </div>
                                <div class="row mb-2" style="font-size: 0.8rem;">
                                    <div class="col-sm-4"><strong>Product Name:</strong></div>
                                    <div class="col-sm-8">{{ $product->name }}</div>
                                </div>
                                <div class="row mb-2" style="font-size: 0.8rem;">
                                    <div class="col-sm-4"><strong>Category:</strong></div>
                                    <div class="col-sm-8">{{ ucfirst($product->category) }}</div>
                                </div>
                                <div class="row mb-2" style="font-size: 0.8rem;">
                                    <div class="col-sm-4"><strong>Quantity:</strong></div>
                                    <div class="col-sm-8">{{ $product->quantity }}</div>
                                </div>
                                <div class="row mb-2" style="font-size: 0.8rem;">
                                    <div class="col-sm-4"><strong>Price:</strong></div>
                                    <div class="col-sm-8">RM{{ number_format($product->price, 2) }}</div>
                                </div>
                                <div class="row mb-2" style="font-size: 0.8rem;">
                                    <div class="col-sm-4"><strong>Features:</strong></div>
                                    <div class="col-sm-8">
                                        @foreach($product->features ?? [] as $feature)
                                            <span class="badge bg-light text-dark border me-1 mb-1" style="font-size: 0.8rem;">{{ $feature }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Marketing Information -->
                            <div class="mb-4" style="font-size: 0.8rem;">
                                <h6 class="text-muted mb-3" style="font-size: 0.8rem;">Marketing Information</h6>
                                
                                <div class="mb-3" style="font-size: 0.8rem;">
                                    <strong>Marketing Description:</strong>
                                    <p class="mt-2 p-3 bg-light rounded" style="font-size: 0.8rem;">{{ $product->product->marketing_description ?: 'None' }}</p>
                                </div>

                                @if($product->product->discount_price)
                                <div class="mb-3" style="font-size: 0.8rem;">
                                    <strong>Discounted Price:</strong>
                                    <p class="mt-2 text-success" style="font-size: 0.8rem;">RM{{ number_format($product->product->discount_price, 2) }}</p>
                                </div>
                                @endif

                                <div class="mb-3" style="font-size: 0.8rem;">
                                    <strong>Images:</strong>
                                    <div class="mt-2">
                                        @if($product->product->customer_images && count($product->product->customer_images) > 0)
                                            <div class="row">
                                                @foreach($product->product->customer_images as $image)
                                                    <div class="col-md-4 mb-2">
                                                        <img src="{{ asset('storage/' . $image) }}" alt="Product Image" class="img-thumbnail" style="width: 100%; height: 100px; object-fit: cover;">
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-muted" style="font-size: 0.8rem;">No images uploaded</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-3" style="font-size: 0.8rem;">
                                    <strong>Video:</strong>
                                    <div class="mt-2">
                                        @if($product->product->product_video)
                                            <video controls style="max-width: 100%; height: 200px;">
                                                <source src="{{ asset('storage/' . $product->product->product_video) }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        @else
                                            <p class="text-muted" style="font-size: 0.8rem;">No video uploaded</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-3" style="font-size: 0.8rem;">
                                    <strong>Status:</strong>
                                    <div class="mt-2">
                                        @if($product->status === 'published')
                                            <span class="badge bg-success" style="font-size: 0.8rem;">Published</span>
                                        @else
                                            <span class="badge bg-warning text-dark" style="font-size: 0.8rem;">Pending</span>
                                        @endif
                                    </div>
                                </div>

                                @if($product->published_by && $product->published_by !== 'System')
                                <div class="mb-3" style="font-size: 0.8rem;">
                                    <strong>Published By:</strong>
                                    <p class="mt-2" style="font-size: 0.8rem;">{{ $product->published_by }}</p>
                                </div>
                                @endif

                                @if($product->published_at)
                                <div class="mb-3" style="font-size: 0.8rem;">
                                    <strong>Published At:</strong>
                                    <p class="mt-2" style="font-size: 0.8rem;">{{ $product->published_at->format('M d, Y H:i') }}</p>
                                </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-5" style="font-size: 0.8rem;">
                                <i class="fas fa-exclamation-triangle text-warning mb-3" style="font-size: 3rem;"></i>
                                <h5 class="text-muted" style="font-size: 0.8rem;">No Product Information Available</h5>
                                <p class="text-muted" style="font-size: 0.8rem;">This inventory item has not been converted to a product yet.</p>
                                <a href="{{ route('admin.product-management.create', $product->id) }}" class="btn btn-success" style="font-size: 0.8rem;">
                                    <i class="fas fa-plus me-1"></i>Create Product
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
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
        
        /* Enhanced Cards */
        .card {
            transition: all 0.3s ease;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }
        
        .card-header {
            border-bottom: 1px solid #dee2e6;
            padding: 0.75rem 1rem;
        }
        
        .card-header h5 {
            font-weight: 600;
            margin: 0;
            font-size: 0.9rem;
        }
        
        .card-body {
            padding: 1rem;
        }
        
        .badge {
            font-size: 0.75rem;
            padding: 0.4em 0.6em;
            border-radius: 6px;
        }
        
        .img-thumbnail {
            border: 2px solid #dee2e6;
            border-radius: 6px;
        }
    </style>
    
@endsection
