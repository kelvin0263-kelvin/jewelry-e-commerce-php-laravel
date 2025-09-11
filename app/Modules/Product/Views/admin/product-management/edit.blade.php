@extends('layouts.admin')

@section('title', 'Edit Product Information')

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
                            <i class="fas fa-edit me-1"></i>Edit Product Information
                        </h1>
                        <p class="mb-0 opacity-75" style="font-size: 0.7rem;">Update product information and customer-facing content</p>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.product-management.sku-details', $product->inventory->id) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
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
        
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Validation Errors:</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Left Side: Inventory Information (50%) -->
            <div class="col-md-6">
                <div class="card border h-100">
                    <div class="card-header bg-primary text-white" style="padding: 0.5rem 0.75rem;">
                        <h5 class="card-title mb-0" style="font-size: 0.85rem;">
                            <i class="fas fa-box me-2"></i>Inventory Information
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 0.75rem;">
                        <div class="row mb-2" style="font-size: 0.8rem;">
                            <div class="col-sm-4">
                                <strong>SKU:</strong>
                            </div>
                            <div class="col-sm-8">
                                {{ $product->sku }}
                            </div>
                        </div>
                        
                        <div class="row mb-2" style="font-size: 0.8rem;">
                            <div class="col-sm-4">
                                <strong>Product Name:</strong>
                            </div>
                            <div class="col-sm-8">
                                {{ $product->inventory->name }}
                            </div>
                        </div>
                        
                        <div class="row mb-2" style="font-size: 0.8rem;">
                            <div class="col-sm-4">
                                <strong>Type:</strong>
                            </div>
                            <div class="col-sm-8">
                                {{ str_replace('Item', '', $product->inventory->type) }}
                            </div>
                        </div>
                        
                        <div class="row mb-2" style="font-size: 0.8rem;">
                            <div class="col-sm-4">
                                <strong>Quantity:</strong>
                            </div>
                            <div class="col-sm-8">
                                {{ $product->inventory->variations->first()->stock ?? 0 }} units
                            </div>
                        </div>
                        
                        <div class="row mb-2" style="font-size: 0.8rem;">
                            <div class="col-sm-4">
                                <strong>Price:</strong>
                            </div>
                            <div class="col-sm-8">
                                RM{{ number_format($product->price, 2) }}
                            </div>
                        </div>
                        
                        <div class="row mb-2" style="font-size: 0.8rem;">
                            <div class="col-sm-4">
                                <strong>Features:</strong>
                            </div>
                            <div class="col-sm-8">
                                @foreach($transformedProduct->features ?? [] as $feature)
                                    <span class="badge bg-light text-dark border me-1 mb-1" style="font-size: 0.65rem;">{{ $feature }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Edit Product Information Form (50%) -->
            <div class="col-md-6">
                <div class="card border h-100">
                    <div class="card-header bg-success text-white" style="padding: 0.5rem 0.75rem;">
                        <h5 class="card-title mb-0" style="font-size: 0.85rem;">
                            <i class="fas fa-edit me-2"></i>Edit Product Information
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 0.75rem;">
                        <!-- Product Information (Read-only) -->
                        <div class="mb-3 p-2 bg-light rounded" style="font-size: 0.8rem;">
                            <h6 class="text-muted mb-2" style="font-size: 0.75rem;">Product Information (Read-only)</h6>
                            <div class="row mb-1" style="font-size: 0.75rem;">
                                <div class="col-sm-4"><strong>SKU:</strong></div>
                                <div class="col-sm-8">{{ $product->sku }}</div>
                            </div>
                            <div class="row mb-1" style="font-size: 0.75rem;">
                                <div class="col-sm-4"><strong>Product Name:</strong></div>
                                <div class="col-sm-8">{{ $product->name }}</div>
                            </div>
                            <div class="row mb-1" style="font-size: 0.75rem;">
                                <div class="col-sm-4"><strong>Category:</strong></div>
                                <div class="col-sm-8">{{ ucfirst($product->category) }}</div>
                            </div>
                            <div class="row mb-1" style="font-size: 0.75rem;">
                                <div class="col-sm-4"><strong>Quantity:</strong></div>
                                <div class="col-sm-8">{{ $product->inventory->variations->first()->stock ?? 0 }}</div>
                            </div>
                            <div class="row mb-1" style="font-size: 0.75rem;">
                                <div class="col-sm-4"><strong>Features:</strong></div>
                                <div class="col-sm-8">
                                    @foreach($transformedProduct->features ?? [] as $feature)
                                        <span class="badge bg-light text-dark border me-1 mb-1" style="font-size: 0.65rem;">{{ $feature }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('admin.product-management.update', $product) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <!-- Hidden fields for required data -->
                            <input type="hidden" name="description" value="{{ $product->description }}">
                            <input type="hidden" name="category" value="{{ $product->category }}">
                            
                            
                            <div class="mb-2" style="font-size: 0.8rem;">
                                <label for="marketing_description" class="form-label" style="font-size: 0.8rem;">
                                    <strong>Marketing Description for SKU: {{ $product->sku }} *</strong>
                                </label>
                                <textarea class="form-control @error('marketing_description') is-invalid @enderror" 
                                          id="marketing_description" name="marketing_description" rows="3" required style="font-size: 0.8rem;">{{ old('marketing_description', $product->marketing_description) }}</textarea>
                                <div class="form-text" style="font-size: 0.7rem;">This description will be shown to customers on the website for this specific SKU only.</div>
                                @error('marketing_description')
                                    <div class="invalid-feedback" style="font-size: 0.7rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-2" style="font-size: 0.8rem;">
                                <label for="selling_price" class="form-label" style="font-size: 0.8rem;">
                                    <strong>Selling Price for SKU: {{ $product->sku }} *</strong>
                                </label>
                                <div class="input-group" style="font-size: 0.8rem;">
                                    <span class="input-group-text" style="font-size: 0.8rem;">RM</span>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('selling_price') is-invalid @enderror" 
                                           id="selling_price" name="selling_price" 
                                           value="{{ old('selling_price', $product->selling_price) }}" required style="font-size: 0.8rem;">
                                </div>
                                <div class="form-text" style="font-size: 0.7rem;">The price customers will pay for this SKU. *Required</div>
                                @error('selling_price')
                                    <div class="invalid-feedback" style="font-size: 0.7rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-2" style="font-size: 0.8rem;">
                                <label for="discount_price" class="form-label" style="font-size: 0.8rem;">
                                    <strong>Discounted Price</strong>
                                </label>
                                <div class="input-group" style="font-size: 0.8rem;">
                                    <span class="input-group-text" style="font-size: 0.8rem;">RM</span>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('discount_price') is-invalid @enderror" 
                                           id="discount_price" name="discount_price" 
                                           value="{{ old('discount_price', $product->discount_price) }}" style="font-size: 0.8rem;">
                                </div>
                                <div class="form-text" style="font-size: 0.7rem;">Optional. Leave empty if no discount.</div>
                                @error('discount_price')
                                    <div class="invalid-feedback" style="font-size: 0.7rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-2" style="font-size: 0.8rem;">
                                <label for="customer_images" class="form-label" style="font-size: 0.8rem;">
                                    <strong>Images for SKU: {{ $product->sku }}</strong>
                                </label>
                                <input type="file" class="form-control @error('customer_images') is-invalid @enderror" 
                                       id="customer_images" name="customer_images[]" multiple accept="image/*" style="font-size: 0.8rem;">
                                <div class="form-text" style="font-size: 0.7rem;">Upload up to 5 images for this specific SKU. Supported formats: JPEG, PNG, JPG, GIF, WebP. Max size: 2MB per image.</div>
                                @error('customer_images')
                                    <div class="invalid-feedback" style="font-size: 0.7rem;">{{ $message }}</div>
                                @enderror
                                
                                @if($product->customer_images && count($product->customer_images) > 0)
                                    <div class="mt-2">
                                        <small class="text-muted">Current images:</small>
                                        <div class="row mt-1">
                                            @foreach($product->customer_images as $image)
                                                <div class="col-md-3 mb-2">
                                                    <img src="{{ asset('storage/' . $image) }}" alt="Current Image" class="img-thumbnail" style="width: 100%; height: 80px; object-fit: cover;">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="mb-2" style="font-size: 0.8rem;">
                                <label for="product_video" class="form-label" style="font-size: 0.8rem;">
                                    <strong>Video</strong>
                                </label>
                                <input type="file" class="form-control @error('product_video') is-invalid @enderror" 
                                       id="product_video" name="product_video" accept="video/*" style="font-size: 0.8rem;">
                                <div class="form-text" style="font-size: 0.7rem;">Upload 1 video. Supported formats: MP4, AVI, MOV. Max size: 10MB.</div>
                                @error('product_video')
                                    <div class="invalid-feedback" style="font-size: 0.7rem;">{{ $message }}</div>
                                @enderror
                                
                                @if($product->product_video)
                                    <div class="mt-2">
                                        <small class="text-muted">Current video:</small>
                                        <div class="mt-1">
                                            <video controls style="max-width: 200px; height: 120px;">
                                                <source src="{{ asset('storage/' . $product->product_video) }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success" style="font-size: 0.8rem;" onclick="console.log('Form submitted');">
                                    <i class="fas fa-save me-1"></i>Update Product Information
                                </button>
                                <a href="{{ route('admin.product-management.sku-details', $product->inventory->id) }}" class="btn btn-secondary" style="font-size: 0.8rem;">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </a>
                            </div>
                        </form>
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
        
        /* Enhanced Form Controls */
        .form-control, .form-select {
            border-radius: 8px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        /* Enhanced Buttons */
        .btn {
            border-radius: 5px;
            font-weight: 500;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-1px);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
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