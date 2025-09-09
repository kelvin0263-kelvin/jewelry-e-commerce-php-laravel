@extends('layouts.admin')

@section('title', 'Review Details')

@push('styles')
<style>
    .review-details-container {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 2rem 0;
    }
    
    .review-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    
    .review-header {
        border-bottom: 1px solid #eee;
        padding-bottom: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .review-title {
        font-size: 1.8rem;
        font-weight: 600;
        color: #2c2c2c;
        margin-bottom: 1rem;
    }
    
    .review-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        align-items: center;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #666;
    }
    
    .meta-label {
        font-weight: 600;
        color: #2c2c2c;
    }
    
    .reviewer-name {
        font-size: 1.2rem;
        font-weight: 600;
        color: #007bff;
    }
    
    .product-link {
        color: #007bff;
        text-decoration: none;
        font-weight: 600;
    }
    
    .product-link:hover {
        text-decoration: underline;
    }
    
    .review-rating {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .stars {
        color: #ffc107;
        font-size: 1.2rem;
    }
    
    .rating-text {
        font-weight: 600;
        color: #2c2c2c;
    }
    
    .review-status {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .status-approved {
        background: #d4edda;
        color: #155724;
    }
    
    .status-pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .review-content {
        margin-bottom: 2rem;
    }
    
    .content-section {
        margin-bottom: 1.5rem;
    }
    
    .content-label {
        font-size: 1rem;
        font-weight: 600;
        color: #2c2c2c;
        margin-bottom: 0.5rem;
    }
    
    .content-text {
        color: #555;
        line-height: 1.6;
        font-size: 1rem;
    }
    
    .review-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #eee;
    }
    
    .action-btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-approve {
        background: #28a745;
        color: white;
    }
    
    .btn-approve:hover {
        background: #218838;
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
    }
    
    .btn-reject {
        background: #dc3545;
        color: white;
    }
    
    .btn-reject:hover {
        background: #c82333;
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
    }
    
    .btn-delete {
        background: #6c757d;
        color: white;
    }
    
    .btn-delete:hover {
        background: #5a6268;
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
    }
    
    .btn-back {
        background: #6c757d;
        color: white;
    }
    
    .btn-back:hover {
        background: #5a6268;
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
    }
    
    .product-info {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .product-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #2c2c2c;
        margin-bottom: 1rem;
    }
    
    .product-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
    
    .product-detail {
        display: flex;
        flex-direction: column;
    }
    
    .detail-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #666;
        margin-bottom: 0.25rem;
    }
    
    .detail-value {
        color: #2c2c2c;
        font-weight: 500;
    }
    
    @media (max-width: 768px) {
        .review-details-container {
            padding: 1rem 0;
        }
        
        .review-card {
            padding: 1.5rem;
        }
        
        .review-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .review-actions {
            flex-direction: column;
        }
        
        .action-btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="review-details-container">
    <div class="container-fluid">
        <div class="review-card">
            <div class="review-header">
                <h1 class="review-title">{{ $review->title }}</h1>
                
                <div class="review-meta">
                    <div class="meta-item">
                        <span class="meta-label">Reviewer:</span>
                        <span class="reviewer-name">{{ $review->reviewer_name }}</span>
                    </div>
                    
                    <div class="meta-item">
                        <span class="meta-label">Product:</span>
                        <a href="{{ route('admin.product-management.show', $review->product) }}" class="product-link">
                            {{ $review->product->name }}
                        </a>
                    </div>
                    
                    <div class="meta-item">
                        <span class="meta-label">Rating:</span>
                        <div class="review-rating">
                            <div class="stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="rating-text">{{ $review->rating }}/5</span>
                        </div>
                    </div>
                    
                    <div class="meta-item">
                        <span class="meta-label">Status:</span>
                        <span class="review-status {{ $review->is_approved ? 'status-approved' : 'status-pending' }}">
                            {{ $review->is_approved ? 'Approved' : 'Pending' }}
                        </span>
                    </div>
                    
                    <div class="meta-item">
                        <span class="meta-label">Submitted:</span>
                        <span>{{ $review->created_at->format('M d, Y \a\t g:i A') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Product Information -->
            <div class="product-info">
                <div class="product-title">Product Information</div>
                <div class="product-details">
                    <div class="product-detail">
                        <span class="detail-label">Product Name</span>
                        <span class="detail-value">{{ $review->product->name }}</span>
                    </div>
                    <div class="product-detail">
                        <span class="detail-label">Category</span>
                        <span class="detail-value">{{ ucfirst($review->product->category) }}</span>
                    </div>
                    <div class="product-detail">
                        <span class="detail-label">Price</span>
                        <span class="detail-value">RM {{ number_format($review->product->price, 2) }}</span>
                    </div>
                    <div class="product-detail">
                        <span class="detail-label">Status</span>
                        <span class="detail-value">
                            @if($review->product->is_visible && $review->product->published_at)
                                Published
                            @else
                                Pending
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Review Content -->
            <div class="review-content">
                <div class="content-section">
                    <div class="content-label">Review Title</div>
                    <div class="content-text">{{ $review->title }}</div>
                </div>
                
                <div class="content-section">
                    <div class="content-label">Review Content</div>
                    <div class="content-text">{{ $review->content }}</div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="review-actions">
                <a href="{{ route('admin.reviews.index') }}" class="action-btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Reviews
                </a>
                
                @if(!$review->is_approved)
                    <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="action-btn btn-approve" onclick="return confirm('Are you sure you want to approve this review?')">
                            <i class="fas fa-check"></i> Approve Review
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.reviews.reject', $review) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="action-btn btn-reject" onclick="return confirm('Are you sure you want to reject this review?')">
                            <i class="fas fa-times"></i> Reject Review
                        </button>
                    </form>
                @endif
                
                <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Are you sure you want to delete this review? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-btn btn-delete">
                        <i class="fas fa-trash"></i> Delete Review
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
