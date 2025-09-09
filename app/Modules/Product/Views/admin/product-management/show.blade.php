@extends('layouts.admin')

@section('title', 'Product Details')

@push('styles')
    <!-- Bootstrap CSS for buttons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* Enhanced Card Design */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }
        
        .card-header {
            background-color: #f8f9fa;
            color: #495057;
            border-bottom: 1px solid #dee2e6;
            padding: 0.4rem 0.6rem;
        }
        
        .card-header h5 {
            font-weight: 600;
            margin: 0;
            font-size: 0.8rem;
        }
        
        .card-body {
            padding: 0.6rem;
        }
        
        /* Enhanced Image Thumbnails */
        .img-thumbnail {
            transition: all 0.3s ease;
            border: 3px solid #e9ecef;
            border-radius: 10px;
            position: relative;
            overflow: hidden;
        }
        
        .img-thumbnail::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }
        
        .img-thumbnail:hover::before {
            transform: translateX(100%);
        }
        
        .img-thumbnail:hover {
            transform: scale(1.08) rotate(1deg);
            border-color: #667eea;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        /* Enhanced Video Container */
        .video-container {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 15px;
            border: 3px solid #e9ecef;
            position: relative;
            overflow: hidden;
        }
        
        .video-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent 30%, rgba(102, 126, 234, 0.1) 50%, transparent 70%);
            animation: shimmer 3s infinite;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }
        
        .video-container video {
            border-radius: 8px;
            position: relative;
            z-index: 1;
        }
        
        /* Enhanced Modal */
        .modal-content {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 1.25rem 1.5rem;
        }
        
        .modal-body img {
            border-radius: 10px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.2);
        }
        
        /* Enhanced Badges */
        .badge {
            font-size: 0.65rem;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-weight: 500;
        }
        
        /* Enhanced Buttons */
        .btn {
            border-radius: 5px;
            font-weight: 500;
            font-size: 0.75rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
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
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .page-header .container-fluid {
            position: relative;
            z-index: 1;
        }
        
        /* Enhanced Product Info */
        .product-info-row {
            padding: 0.4rem 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .product-info-row:last-child {
            border-bottom: none;
        }
        
        /* Enhanced Status Badges */
        .status-published {
            background-color: #28a745;
            color: white;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }
        
        .status-pending {
            background-color: #ffc107;
            color: #212529;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }
        
        /* Enhanced Actions Card */
        .actions-card {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
        }
        
        .actions-card .card-header {
            background-color: #f8f9fa;
            color: #495057;
            border-bottom: 1px solid #dee2e6;
        }
        
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .page-header {
                padding: 1rem 0;
                margin-bottom: 1rem;
            }
            
            .card-body {
                padding: 0.75rem;
            }
            
        }
    </style>
@endpush

@section('content')
    <!-- Enhanced Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h5 mb-1 fw-bold">
                        <i class="fas fa-gem me-1"></i>Product Details
                    </h1>
                    <p class="mb-0 opacity-75" style="font-size: 0.7rem;">View detailed information about the product</p>
                </div>
                <div class="d-flex gap-1">
                    <a href="{{ route('admin.product-management.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to Products
                    </a>
                    <a href="{{ route('admin.product-management.edit', $product) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-1"></i> Edit Product
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="card border">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0">Product Information</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $data = $decoratedProduct->getDecoratedData();
                        @endphp
                        
                        <div class="row mb-2">
                            <div class="col-sm-3">
                                <strong>Product ID:</strong>
                            </div>
                            <div class="col-sm-9">
                                #{{ $data['id'] }}
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-sm-3">
                                <strong>Product Name:</strong>
                            </div>
                            <div class="col-sm-9">
                                {{ $data['name'] }}
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-sm-3">
                                <strong>SKU:</strong>
                            </div>
                            <div class="col-sm-9">
                                {{ $product->sku }}
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-sm-3">
                                <strong>Category:</strong>
                            </div>
                            <div class="col-sm-9">
                                <span class="badge bg-light text-dark border">{{ ucfirst($data['category']) }}</span>
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-sm-3">
                                <strong>Status:</strong>
                            </div>
                            <div class="col-sm-9">
                                @if($data['status'] === 'published')
                                    <span class="status-published">Published</span>
                                @else
                                    <span class="status-pending">Pending</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-sm-3">
                                <strong>Price:</strong>
                            </div>
                            <div class="col-sm-9">
                                RM{{ number_format($product->price, 2) }}
                                @if($product->discount_price)
                                    <span class="text-success ms-2">
                                        (Discounted: RM{{ number_format($product->discount_price, 2) }})
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-sm-3">
                                <strong>Description:</strong>
                            </div>
                            <div class="col-sm-9">
                                {{ $product->description }}
                            </div>
                        </div>
                        
                        @if($product->marketing_description)
                        <div class="row mb-2">
                            <div class="col-sm-3">
                                <strong>Marketing Description:</strong>
                            </div>
                            <div class="col-sm-9">
                                {{ $product->marketing_description }}
                            </div>
                        </div>
                        @endif
                        
                        @if($product->features && count($product->features) > 0)
                        <div class="row mb-2">
                            <div class="col-sm-3">
                                <strong>Features:</strong>
                            </div>
                            <div class="col-sm-9">
                                <ul class="list-unstyled">
                                    @foreach($product->features as $feature)
                                        <li><i class="fas fa-check text-success me-2"></i>{{ $feature }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Product Media Section -->
                        <div class="row mb-2">
                            <div class="col-sm-3">
                                <strong>Media:</strong>
                            </div>
                            <div class="col-sm-9">
                                <!-- Product Images -->
                                @if($product->customer_images && count($product->customer_images) > 0)
                                    <div class="mb-2">
                                            <h6 class="text-muted mb-1">
                                            <i class="fas fa-images me-1"></i>Product Images ({{ count($product->customer_images) }})
                                        </h6>
                                        <div class="row">
                                            @foreach($product->customer_images as $index => $image)
                                                <div class="col-md-3 col-sm-4 col-6 mb-2">
                                                    <div class="position-relative">
                                                        <img src="{{ asset('storage/' . $image) }}" 
                                                             alt="Product Image {{ $index + 1 }}" 
                                                             class="img-thumbnail w-100" 
                                                             style="height: 100px; object-fit: cover; cursor: pointer;"
                                                             data-bs-toggle="modal" 
                                                             data-bs-target="#imageModal{{ $index }}">
                                                        <div class="position-absolute top-0 end-0">
                                                            <span class="badge bg-primary">{{ $index + 1 }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="mb-2">
                                            <h6 class="text-muted mb-1">
                                            <i class="fas fa-images me-1"></i>Product Images
                                        </h6>
                                        <p class="text-muted small">No images uploaded</p>
                                    </div>
                                @endif
                                
                                <!-- Product Video -->
                                @if($product->product_video)
                                    <div class="mb-2">
                                            <h6 class="text-muted mb-1">
                                            <i class="fas fa-video me-1"></i>Product Video
                                        </h6>
                                        <div class="video-container">
                                            <video controls class="w-100" style="max-height: 250px; border-radius: 8px;">
                                                <source src="{{ asset('storage/' . $product->product_video) }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    </div>
                                @else
                                    <div class="mb-2">
                                            <h6 class="text-muted mb-1">
                                            <i class="fas fa-video me-1"></i>Product Video
                                        </h6>
                                        <p class="text-muted small">No video uploaded</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border mb-1">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0">Publishing Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-1">
                            <div class="mb-1">
                                <strong>Published By:</strong>
                            </div>
                            <div>
                                {{ $data['publish_info']['published_by'] }}
                            </div>
                        </div>
                        
                        <div class="mb-1">
                            <div class="mb-1">
                                <strong>Published At:</strong>
                            </div>
                            <div>
                                @if($data['publish_info']['published_at'] !== 'Not published')
                                    {{ $product->published_at ? $product->published_at->format('M d, Y H:i') : 'Not published' }}
                                @else
                                    Not published
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card border mt-1">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-1">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-info w-100" target="_blank">
                                <i class="fas fa-eye me-1"></i> View Product
                            </a>
                            
                            @if($data['can_publish'])
                                <form action="{{ route('admin.product-management.publish', $product) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100" 
                                            onclick="return confirm('Are you sure you want to publish this product?')">
                                        <i class="fas fa-upload me-1"></i> Publish Product
                                    </button>
                                </form>
                            @endif
                            
                            @if($data['can_unpublish'])
                                <form action="{{ route('admin.product-management.unpublish', $product) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-warning w-100" 
                                            onclick="return confirm('Are you sure you want to unpublish this product?')">
                                        <i class="fas fa-eye-slash me-1"></i> Unpublish Product
                                    </button>
                                </form>
                            @endif
                            
                            <form action="{{ route('admin.product-management.destroy', $product) }}" method="POST"
                                  onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-trash me-1"></i> Delete Product
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Image Modals -->
    @if($product->customer_images && count($product->customer_images) > 0)
        @foreach($product->customer_images as $index => $image)
            <div class="modal fade" id="imageModal{{ $index }}" tabindex="-1" aria-labelledby="imageModalLabel{{ $index }}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="imageModalLabel{{ $index }}">
                                Product Image {{ $index + 1 }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="{{ asset('storage/' . $image) }}" 
                                 alt="Product Image {{ $index + 1 }}" 
                                 class="img-fluid" 
                                 style="max-height: 70vh;">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <a href="{{ asset('storage/' . $image) }}" 
                               class="btn btn-primary" 
                               download="product-image-{{ $index + 1 }}">
                                <i class="fas fa-download me-1"></i> Download
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <!-- Bootstrap JS for alerts and modals -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
        
        // Add smooth scrolling for better UX
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
@endsection
