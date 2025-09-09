@extends('layouts.admin')

@section('title', 'Create New Product')

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
                            <i class="fas fa-plus-circle me-1"></i>Create New Product
                        </h1>
                        <p class="mb-0 opacity-75" style="font-size: 0.7rem;">Add a new product to your inventory</p>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.product-management.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back to Products
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

        <form action="{{ route('admin.product-management.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
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
                                               id="name" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label">Product ID</label>
                                        <div class="form-control-plaintext bg-light p-2 rounded">
                                            Will be automatically generated
                                        </div>
                                        <div class="form-text">A unique Product ID will be automatically generated after creation.</div>
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
                                            <option value="earring" {{ old('category') == 'earring' ? 'selected' : '' }}>Earring</option>
                                            <option value="bracelet" {{ old('category') == 'bracelet' ? 'selected' : '' }}>Bracelet</option>
                                            <option value="necklace" {{ old('category') == 'necklace' ? 'selected' : '' }}>Necklace</option>
                                            <option value="ring" {{ old('category') == 'ring' ? 'selected' : '' }}>Ring</option>
                                        </select>
                                        @error('category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label for="description" class="form-label">Description *</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-2">
                                <label for="marketing_description" class="form-label">Marketing Description *</label>
                                <textarea class="form-control @error('marketing_description') is-invalid @enderror" 
                                          id="marketing_description" name="marketing_description" rows="4" required>{{ old('marketing_description') }}</textarea>
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
                                                   id="price" name="price" value="{{ old('price') }}" required>
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
                                                   id="discount_price" name="discount_price" value="{{ old('discount_price') }}">
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
                                @if(old('features') && count(old('features')) > 0)
                                    @foreach(old('features') as $index => $feature)
                                        <div class="mb-2 feature-input">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="features[]" placeholder="Enter a feature" value="{{ $feature }}">
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
                                
                                <!-- Image Preview Container -->
                                <div id="image-preview-container" class="mt-2" style="display: none;">
                                    <div class="row" id="image-previews"></div>
                                </div>
                                
                                <!-- Image Status Indicator -->
                                <div id="image-status" class="mt-2" style="display: none;">
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>
                                        <span id="image-count">0</span> image(s) selected
                                    </span>
                                </div>
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
                                
                                <!-- Video Preview Container -->
                                <div id="video-preview-container" class="mt-2" style="display: none;">
                                    <div class="video-preview">
                                        <video id="video-preview" controls style="max-width: 200px; max-height: 150px;">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                </div>
                                
                                <!-- Video Status Indicator -->
                                <div id="video-status" class="mt-2" style="display: none;">
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>
                                        Video selected
                                    </span>
                                </div>
                            </div>
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
                            <i class="fas fa-save me-1"></i>Create Product
                        </button>
                        <a href="{{ route('admin.product-management.index') }}" class="btn btn-secondary">
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
        .form-control, .form-select {
            border-radius: 8px;
        }
        
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
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .input-group-text {
            border-radius: 8px 0 0 8px;
        }
        
        /* File preview styles */
        #image-preview-container, #video-preview-container {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 15px;
            background-color: #f8f9fa;
        }
        
        .img-thumbnail {
            border: 2px solid #dee2e6;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .img-thumbnail:hover {
            border-color: #007bff;
            transform: scale(1.05);
        }
        
        #video-preview {
            border-radius: 8px;
            border: 2px solid #dee2e6;
        }
        
        .video-preview {
            text-align: center;
        }
        
        /* Status indicators */
        #image-status, #video-status {
            animation: fadeIn 0.5s ease-in;
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

        // File upload handling and preview
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('customer_images');
            const videoInput = document.getElementById('product_video');
            const imagePreviewContainer = document.getElementById('image-preview-container');
            const imagePreviews = document.getElementById('image-previews');
            const imageStatus = document.getElementById('image-status');
            const imageCount = document.getElementById('image-count');
            const videoPreviewContainer = document.getElementById('video-preview-container');
            const videoPreview = document.getElementById('video-preview');
            const videoStatus = document.getElementById('video-status');

            // Check if there are validation errors and show helpful messages
            const hasValidationErrors = document.querySelector('.is-invalid') !== null;
            if (hasValidationErrors) {
                // Show a helpful message about file persistence
                const mediaCard = document.querySelector('.card .card-header h5:contains("Product Media")').closest('.card');
                if (mediaCard) {
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-info alert-dismissible fade show mt-2';
                    alertDiv.innerHTML = `
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> Due to browser security restrictions, file uploads need to be reselected after validation errors. 
                        However, all other form data (text fields, features, etc.) will be preserved.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    mediaCard.querySelector('.card-body').insertBefore(alertDiv, mediaCard.querySelector('.card-body').firstChild);
                }
            }

            // Handle image selection
            imageInput.addEventListener('change', function() {
                const files = Array.from(this.files);
                
                // Clear previous previews
                imagePreviews.innerHTML = '';
                
                if (files.length > 0) {
                    // Store file info in localStorage for persistence
                    const fileInfo = files.map(file => ({
                        name: file.name,
                        size: file.size,
                        type: file.type
                    }));
                    localStorage.setItem('product_images_info', JSON.stringify(fileInfo));
                    
                    // Show preview container and status
                    imagePreviewContainer.style.display = 'block';
                    imageStatus.style.display = 'block';
                    imageCount.textContent = files.length;
                    
                    // Create previews for each image
                    files.forEach((file, index) => {
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const col = document.createElement('div');
                                col.className = 'col-3 mb-2';
                                col.innerHTML = `
                                    <div class="position-relative">
                                        <img src="${e.target.result}" class="img-thumbnail" style="width: 100%; height: 80px; object-fit: cover;">
                                        <div class="position-absolute top-0 end-0">
                                            <span class="badge bg-primary">${index + 1}</span>
                                        </div>
                                    </div>
                                `;
                                imagePreviews.appendChild(col);
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                } else {
                    // Clear localStorage when no files selected
                    localStorage.removeItem('product_images_info');
                    // Hide preview container and status
                    imagePreviewContainer.style.display = 'none';
                    imageStatus.style.display = 'none';
                }
            });

            // Handle video selection
            videoInput.addEventListener('change', function() {
                const file = this.files[0];
                
                if (file && file.type.startsWith('video/')) {
                    // Store video info in localStorage for persistence
                    const videoInfo = {
                        name: file.name,
                        size: file.size,
                        type: file.type
                    };
                    localStorage.setItem('product_video_info', JSON.stringify(videoInfo));
                    
                    // Show preview container and status
                    videoPreviewContainer.style.display = 'block';
                    videoStatus.style.display = 'block';
                    
                    // Create video preview
                    const url = URL.createObjectURL(file);
                    videoPreview.src = url;
                } else {
                    // Clear localStorage when no video selected
                    localStorage.removeItem('product_video_info');
                    // Hide preview container and status
                    videoPreviewContainer.style.display = 'none';
                    videoStatus.style.display = 'none';
                }
            });

            // Restore file information from localStorage on page load
            function restoreFileInfo() {
                // Restore images info
                const imagesInfo = localStorage.getItem('product_images_info');
                if (imagesInfo) {
                    try {
                        const files = JSON.parse(imagesInfo);
                        if (files.length > 0) {
                            imageStatus.style.display = 'block';
                            imageCount.textContent = files.length;
                            
                            // Show file names as placeholders
                            imagePreviewContainer.style.display = 'block';
                            imagePreviews.innerHTML = files.map((file, index) => `
                                <div class="col-3 mb-2">
                                    <div class="position-relative">
                                        <div class="img-thumbnail d-flex align-items-center justify-content-center" style="width: 100%; height: 80px; background-color: #f8f9fa; border: 2px dashed #dee2e6;">
                                            <div class="text-center">
                                                <i class="fas fa-image text-muted mb-1"></i>
                                                <div class="small text-muted">${file.name}</div>
                                            </div>
                                        </div>
                                        <div class="position-absolute top-0 end-0">
                                            <span class="badge bg-warning">${index + 1}</span>
                                        </div>
                                    </div>
                                </div>
                            `).join('');
                        }
                    } catch (e) {
                        console.error('Error parsing images info:', e);
                    }
                }
                
                // Restore video info
                const videoInfo = localStorage.getItem('product_video_info');
                if (videoInfo) {
                    try {
                        const file = JSON.parse(videoInfo);
                        videoStatus.style.display = 'block';
                        videoPreviewContainer.style.display = 'block';
                        videoPreviewContainer.innerHTML = `
                            <div class="video-preview">
                                <div class="d-flex align-items-center justify-content-center" style="width: 200px; height: 150px; background-color: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 8px;">
                                    <div class="text-center">
                                        <i class="fas fa-video text-muted mb-2" style="font-size: 2rem;"></i>
                                        <div class="small text-muted">${file.name}</div>
                                    </div>
                                </div>
                            </div>
                        `;
                    } catch (e) {
                        console.error('Error parsing video info:', e);
                    }
                }
            }
            
            // Call restore function on page load
            restoreFileInfo();
            
            // Clear localStorage when form is successfully submitted
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Form validation before submission
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
                    
                    // Clear file info from localStorage on successful submission
                    localStorage.removeItem('product_images_info');
                    localStorage.removeItem('product_video_info');
                });
            }

            // Store file inputs to prevent clearing on validation errors
            const fileInputs = document.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                input.addEventListener('change', function() {
                    // Store the files in a data attribute for potential restoration
                    if (this.files.length > 0) {
                        this.setAttribute('data-has-files', 'true');
                    }
                });
            });
        });
    </script>
@endsection
