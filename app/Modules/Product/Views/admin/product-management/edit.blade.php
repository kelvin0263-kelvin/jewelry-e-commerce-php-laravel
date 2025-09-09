@extends('layouts.admin')

@section('title', 'Edit Product - ' . $product->name)

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
                            <i class="fas fa-edit me-1"></i>Edit Product Details
                        </h1>
                        <p class="mb-0 opacity-75" style="font-size: 0.7rem;">Update product information and settings</p>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.product-management.show', $product) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Product Details
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

        <form action="{{ route('admin.product-management.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <!-- Basic Information -->
                <div class="col-md-8">
                    <div class="card border mb-1">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="card-title mb-0">Basic Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="name" class="form-label">Product Name *</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label">Product ID</label>
                                        <div class="form-control-plaintext bg-light p-2 rounded">
                                            {{ $product->id }}
                                        </div>
                                        <div class="form-text">Product ID is auto-generated and cannot be changed.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="category" class="form-label">Category *</label>
                                        <select class="form-select @error('category') is-invalid @enderror" 
                                                id="category" name="category" required>
                                            <option value="">Select Category</option>
                                            <option value="earring" {{ old('category', $product->category) == 'earring' ? 'selected' : '' }}>Earring</option>
                                            <option value="bracelet" {{ old('category', $product->category) == 'bracelet' ? 'selected' : '' }}>Bracelet</option>
                                            <option value="necklace" {{ old('category', $product->category) == 'necklace' ? 'selected' : '' }}>Necklace</option>
                                            <option value="ring" {{ old('category', $product->category) == 'ring' ? 'selected' : '' }}>Ring</option>
                                        </select>
                                        @error('category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-2">
                                <label for="marketing_description" class="form-label">Marketing Description *</label>
                                <textarea class="form-control @error('marketing_description') is-invalid @enderror" 
                                          id="marketing_description" name="marketing_description" rows="4" required>{{ old('marketing_description', $product->marketing_description) }}</textarea>
                                @error('marketing_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="card border mb-1">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="card-title mb-0">Pricing</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="price" class="form-label">Regular Price *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">RM</span>
                                            <input type="number" step="0.01" min="0" 
                                                   class="form-control @error('price') is-invalid @enderror" 
                                                   id="price" name="price" value="{{ old('price', $product->price) }}" required>
                                        </div>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="discount_price" class="form-label">Discount Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">RM</span>
                                            <input type="number" step="0.01" min="0" 
                                                   class="form-control @error('discount_price') is-invalid @enderror" 
                                                   id="discount_price" name="discount_price" value="{{ old('discount_price', $product->discount_price) }}">
                                        </div>
                                        @error('discount_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Features -->
                    <div class="card border mb-1">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="card-title mb-0">Features</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    At least one feature is required. Add features to describe your product.
                                </small>
                            </div>
                            <div id="features-container">
                                @if(old('features'))
                                    @foreach(old('features') as $index => $feature)
                                        <div class="mb-2 feature-input">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="features[]" value="{{ $feature }}">
                                                <button type="button" class="btn btn-outline-danger remove-feature">Remove</button>
                                            </div>
                                        </div>
                                    @endforeach
                                @elseif($product->features)
                                    @foreach($product->features as $feature)
                                        <div class="mb-2 feature-input">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="features[]" value="{{ $feature }}">
                                                <button type="button" class="btn btn-outline-danger remove-feature">Remove</button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="mb-2 feature-input">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="features[]" placeholder="Enter a feature">
                                            <button type="button" class="btn btn-outline-danger remove-feature">Remove</button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-outline-primary" id="add-feature">Add Feature</button>
                        </div>
                    </div>
                </div>

                <!-- Media -->
                <div class="col-md-4">
                    <div class="card border mb-1">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="card-title mb-0">Product Media</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <label for="customer_images" class="form-label">
                                    <i class="fas fa-images me-1"></i>Product Images
                                </label>
                                <input type="file" class="form-control @error('customer_images') is-invalid @enderror" 
                                       id="customer_images" name="customer_images[]" multiple accept="image/*">
                                @error('customer_images')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Upload up to 5 images. Supported formats: JPEG, PNG, JPG, GIF, WebP. Max size: 2MB per image.</div>
                            </div>
                            
                            <div class="mb-2">
                                <label for="product_video" class="form-label">
                                    <i class="fas fa-video me-1"></i>Product Video
                                </label>
                                <input type="file" class="form-control @error('product_video') is-invalid @enderror" 
                                       id="product_video" name="product_video" accept="video/*">
                                @error('product_video')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Upload 1 video. Supported formats: MP4, AVI, MOV. Max size: 10MB.</div>
                            </div>

                            @if($product->customer_images && count($product->customer_images) > 0)
                                <div class="mb-2">
                                    <label class="form-label">Current Images</label>
                                    <div class="row">
                                        @foreach($product->customer_images as $index => $image)
                                            <div class="col-6 mb-2">
                                                <div class="position-relative">
                                                    <img src="{{ asset('storage/' . $image) }}" alt="Product Image" class="img-thumbnail" style="width: 100%; height: 100px; object-fit: cover;">
                                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-image" 
                                                            data-image="{{ $image }}" style="transform: translate(50%, -50%);">
                                                        ×
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card border mt-1">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-1">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Product
                        </button>
                        <a href="{{ route('admin.product-management.show', $product) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS for alerts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Styles -->
    <style>
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
        
        /* Enhanced Form Controls */
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
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
            transition: left 0.5s;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn:hover {
            transform: translateY(-1px);
        }
        
        .feature-input {
            animation: fadeIn 0.3s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <!-- JavaScript -->
    <script>
        // Add feature functionality
        document.getElementById('add-feature').addEventListener('click', function() {
            const container = document.getElementById('features-container');
            const featureInput = document.createElement('div');
            featureInput.className = 'mb-2 feature-input';
            featureInput.innerHTML = `
                <div class="input-group">
                    <input type="text" class="form-control" name="features[]" placeholder="Enter a feature">
                    <button type="button" class="btn btn-outline-danger remove-feature">Remove</button>
                </div>
            `;
            container.appendChild(featureInput);
        });

        // Remove feature functionality
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-feature')) {
                e.target.closest('.feature-input').remove();
            }
        });

        // Remove image functionality
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-image')) {
                const imagePath = e.target.getAttribute('data-image');
                // Add hidden input to track removed images
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'removed_images[]';
                hiddenInput.value = imagePath;
                document.querySelector('form').appendChild(hiddenInput);
                
                // Remove the image display
                e.target.closest('.col-6').remove();
            }
        });

        // Form validation before submission
        document.querySelector('form').addEventListener('submit', function(e) {
            const requiredFields = [
                { id: 'name', name: 'Product Name' },
                { id: 'category', name: 'Category' },
                { id: 'description', name: 'Description' },
                { id: 'marketing_description', name: 'Marketing Description' },
                { id: 'price', name: 'Price' }
            ];
            
            const missingFields = [];
            
            // Check if at least one feature is provided
            const featureInputs = document.querySelectorAll('input[name="features[]"]');
            const hasFeatures = Array.from(featureInputs).some(input => input.value.trim() !== '');
            
            if (!hasFeatures) {
                missingFields.push('At least one Feature');
            }
            
            requiredFields.forEach(field => {
                const element = document.getElementById(field.id);
                if (!element || !element.value.trim()) {
                    missingFields.push(field.name);
                    if (element) {
                        element.classList.add('is-invalid');
                    }
                } else {
                    if (element) {
                        element.classList.remove('is-invalid');
                    }
                }
            });
            
            if (missingFields.length > 0) {
                e.preventDefault();
                
                // Show alert
                alert('Please fill in the following required fields:\n\n• ' + missingFields.join('\n• '));
                
                // Scroll to first missing field
                const firstMissingField = document.getElementById(requiredFields.find(f => missingFields.includes(f.name)).id);
                if (firstMissingField) {
                    firstMissingField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstMissingField.focus();
                }
                
                return false;
            }
            
            console.log('Form is being submitted...');
            console.log('Form action:', this.action);
            console.log('Form method:', this.method);
        });
    </script>
@endsection
