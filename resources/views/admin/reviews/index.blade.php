@extends('layouts.admin')

@section('title', 'Customer Reviews Management')

@push('styles')
<style>
    .reviews-container {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 1rem 0;
    }
    
    .reviews-header {
        background: white;
        border-radius: 8px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .reviews-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #2c2c2c;
        margin-bottom: 0.25rem;
    }
    
    .reviews-subtitle {
        color: #666;
        font-size: 0.95rem;
    }
    
    .back-button {
        background: #6c757d;
        color: white;
        border: 1px solid #6c757d;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    
    .back-button:hover {
        background: #5a6268;
        border-color: #5a6268;
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    
    .filter-section {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .filter-form {
        display: flex;
        gap: 0.75rem;
        align-items: end;
        flex-wrap: wrap;
    }
    
    .form-group {
        flex: 1;
        min-width: 180px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.3rem;
        font-weight: 600;
        color: #2c2c2c;
        font-size: 0.8rem;
    }
    
    .form-group input,
    .form-group select {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 0.9rem;
    }
    
    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.2);
    }
    
    .filter-btn {
        background: #007bff;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        height: fit-content;
    }
    
    .filter-btn:hover {
        background: #0056b3;
        transform: translateY(-1px);
    }
    
    .clear-btn {
        background: transparent;
        color: #666;
        border: 1px solid #ddd;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        height: fit-content;
        text-decoration: none;
        display: inline-block;
    }
    
    .clear-btn:hover {
        background: #f8f9fa;
        color: #333;
        text-decoration: none;
    }
    
    .reviews-list {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .review-item {
        border-bottom: 1px solid #eee;
        padding: 1rem;
        transition: all 0.3s ease;
    }
    
    .review-item:hover {
        background: #f8f9fa;
    }
    
    .review-item:last-child {
        border-bottom: none;
    }
    
    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.75rem;
    }
    
    .review-info {
        flex: 1;
    }
    
    .reviewer-name {
        font-size: 1rem;
        font-weight: 600;
        color: #2c2c2c;
        margin-bottom: 0.2rem;
    }
    
    .product-info {
        color: #666;
        font-size: 0.8rem;
        margin-bottom: 0.4rem;
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
        gap: 0.4rem;
    }
    
    .stars {
        color: #ffc107;
        font-size: 0.9rem;
    }
    
    .rating-text {
        color: #666;
        font-size: 0.8rem;
    }
    
    .review-status {
        padding: 0.3rem 0.6rem;
        border-radius: 15px;
        font-size: 0.75rem;
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
        margin-bottom: 0.75rem;
    }
    
    .review-title {
        font-size: 1rem;
        font-weight: 600;
        color: #2c2c2c;
        margin-bottom: 0.4rem;
    }
    
    .review-text {
        color: #555;
        line-height: 1.5;
        margin-bottom: 0;
        font-size: 0.9rem;
    }
    
    .review-actions {
        display: flex;
        gap: 0.4rem;
        flex-wrap: wrap;
    }
    
    .action-btn {
        padding: 0.4rem 0.8rem;
        border: none;
        border-radius: 3px;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.2rem;
    }
    
    .btn-approve {
        background: #28a745;
        color: white;
    }
    
    .btn-approve:hover {
        background: #218838;
        color: white;
        text-decoration: none;
    }
    
    .btn-reject {
        background: #dc3545;
        color: white;
    }
    
    .btn-reject:hover {
        background: #c82333;
        color: white;
        text-decoration: none;
    }
    
    .btn-delete {
        background: #6c757d;
        color: white;
    }
    
    .btn-delete:hover {
        background: #5a6268;
        color: white;
        text-decoration: none;
    }
    
    .btn-view {
        background: #007bff;
        color: white;
    }
    
    .btn-view:hover {
        background: #0056b3;
        color: white;
        text-decoration: none;
    }
    
    .review-meta {
        color: #999;
        font-size: 0.7rem;
        margin-top: 0.4rem;
    }
    
    .no-reviews {
        text-align: center;
        padding: 2rem 1.5rem;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .no-reviews-icon {
        font-size: 3rem;
        color: #ddd;
        margin-bottom: 0.75rem;
    }
    
    .no-reviews-title {
        font-size: 1.2rem;
        color: #666;
        margin-bottom: 0.75rem;
    }
    
    .no-reviews-text {
        color: #999;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
    }
    
    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 1.5rem;
    }
    
    .pagination {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .pagination li {
        display: inline-block;
    }
    
    .pagination a,
    .pagination span {
        display: inline-block;
        padding: 0.5rem 0.75rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        color: #666;
        text-decoration: none;
        transition: all 0.3s ease;
        background: white;
    }
    
    .pagination a:hover {
        background: #007bff;
        color: white;
        border-color: #007bff;
        text-decoration: none;
    }
    
    .pagination .active span {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }
    
    .pagination .disabled span {
        background: #f8f9fa;
        color: #6c757d;
        border-color: #dee2e6;
        cursor: not-allowed;
    }
    
    @media (max-width: 768px) {
        .reviews-container {
            padding: 1rem 0;
        }
        
        .filter-form {
            flex-direction: column;
            align-items: stretch;
        }
        
        .form-group {
            min-width: auto;
        }
        
        .review-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .review-actions {
            width: 100%;
            justify-content: flex-start;
        }
    }
</style>
@endpush

@section('content')
<div class="reviews-container">
    <div class="container-fluid">
        <div class="reviews-header">
            <div class="d-flex align-items-center mb-3">
                <a href="{{ route('admin.product-management.index') }}" class="back-button">
                    <i class="fas fa-arrow-left"></i>Back to Products
                </a>
            </div>
            <h1 class="reviews-title">Customer Reviews Management</h1>
            <p class="reviews-subtitle">Manage and moderate customer reviews for your products</p>
        </div>
        
        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="{{ route('admin.reviews.index') }}" class="filter-form">
                <div class="form-group">
                    <label for="search">Search Reviews</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Search by reviewer name, title, or content...">
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="">All Reviews</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="product_id">Product</label>
                    <select id="product_id" name="product_id">
                        <option value="">All Products</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <button type="submit" class="filter-btn">
                    <i class="fas fa-search"></i> Filter
                </button>
                
                <a href="{{ route('admin.reviews.index') }}" class="clear-btn">
                    <i class="fas fa-times"></i> Clear
                </a>
            </form>
        </div>
        
        <!-- Reviews List -->
        @if($reviews->count() > 0)
            <div class="reviews-list">
                @foreach($reviews as $review)
                    <div class="review-item">
                        <div class="review-header">
                            <div class="review-info">
                                <div class="reviewer-name">{{ $review->reviewer_name }}</div>
                                <div class="product-info">
                                    Review for: 
                                    <a href="{{ route('admin.product-management.show', $review->product) }}" class="product-link">
                                        {{ $review->product->name }}
                                    </a>
                                </div>
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
                            
                            <div class="review-status {{ $review->is_approved ? 'status-approved' : 'status-pending' }}">
                                {{ $review->is_approved ? 'Approved' : 'Pending' }}
                            </div>
                        </div>
                        
                        <div class="review-content">
                            <div class="review-title">{{ $review->title }}</div>
                            <div class="review-text">{{ $review->content }}</div>
                        </div>
                        
                        <div class="review-actions">
                            <a href="{{ route('admin.reviews.show', $review) }}" class="action-btn btn-view">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            
                            @if(!$review->is_approved)
                                <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="action-btn btn-approve" onclick="return confirm('Are you sure you want to approve this review?')">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.reviews.reject', $review) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="action-btn btn-reject" onclick="return confirm('Are you sure you want to reject this review?')">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </form>
                            @endif
                            
                            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to delete this review? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn btn-delete">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                        
                        <div class="review-meta">
                            Submitted on {{ $review->created_at->format('M d, Y \a\t g:i A') }}
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            @if($reviews->hasPages())
                <div class="pagination-container">
                    {{ $reviews->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="no-reviews">
                <div class="no-reviews-icon">💬</div>
                <h2 class="no-reviews-title">No reviews found</h2>
                <p class="no-reviews-text">
                    @if(request('search') || request('status') || request('product_id'))
                        Try adjusting your filter criteria or view all reviews.
                    @else
                        No customer reviews have been submitted yet.
                    @endif
                </p>
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-primary">
                    View All Reviews
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
