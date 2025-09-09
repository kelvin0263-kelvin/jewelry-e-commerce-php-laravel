@extends('layouts.app')

@section('title', 'Products')

@push('styles')
<style>
    .products-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
        background: #f8f9fa;
        min-height: 100vh;
    }
    
    .products-header {
        text-align: center;
        margin-bottom: 3rem;
    }
    
    .products-title {
        font-size: 2.5rem;
        font-weight: 300;
        color: #2c2c2c;
        margin-bottom: 1rem;
        font-family: 'Times New Roman', serif;
    }
    
    .products-subtitle {
        color: #666;
        font-size: 1.1rem;
    }
    
    .search-filter-section {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    
    .search-filter-form {
        display: flex;
        gap: 1rem;
        align-items: end;
        flex-wrap: wrap;
    }
    
    .form-group {
        flex: 1;
        min-width: 200px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #2c2c2c;
        font-size: 0.9rem;
    }
    
    .form-group input,
    .form-group select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 1rem;
        font-family: inherit;
    }
    
    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: #d4af37;
        box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2);
    }
    
    .search-btn {
        background: #d4af37;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        height: fit-content;
    }
    
    .search-btn:hover {
        background: #b8941f;
        transform: translateY(-1px);
    }
    
    .clear-btn {
        background: transparent;
        color: #666;
        border: 1px solid #ddd;
        padding: 0.75rem 1.5rem;
        border-radius: 6px;
        font-size: 1rem;
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
    
    .products-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 2rem;
        margin-bottom: 3rem;
    }
    
    .product-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        position: relative;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .product-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        background: #f8f9fa;
    }
    
    .product-content {
        padding: 1.5rem;
    }
    
    .product-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c2c2c;
        margin-bottom: 0.5rem;
        line-height: 1.3;
        height: 2.6rem;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    
    .product-category {
        color: #666;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.75rem;
    }
    
    .product-price {
        font-size: 1.2rem;
        color: #d4af37;
        font-weight: 700;
        margin-bottom: 1rem;
    }
    
    .product-actions {
        display: flex;
        gap: 0.5rem;
        flex-direction: column;
    }
    
    .view-details-btn {
        background: #d4af37;
        color: white;
        border: none;
        padding: 0.6rem 1rem;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        text-align: center;
        text-transform: uppercase;
    }
    
    .view-details-btn:hover {
        background: #b8941f;
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
    }
    
    .add-to-cart-btn {
        background: transparent;
        color: #d4af37;
        border: 1px solid #d4af37;
        padding: 0.6rem 1rem;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
    }
    
    .add-to-cart-btn:hover {
        background: #d4af37;
        color: white;
        transform: translateY(-1px);
    }
    
    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 3rem;
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
        background: #d4af37;
        color: white;
        border-color: #d4af37;
        text-decoration: none;
    }
    
    .pagination .active span {
        background: #d4af37;
        color: white;
        border-color: #d4af37;
    }
    
    .pagination .disabled span {
        background: #f8f9fa;
        color: #6c757d;
        border-color: #dee2e6;
        cursor: not-allowed;
    }
    
    .no-products {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    
    .no-products-icon {
        font-size: 4rem;
        color: #ddd;
        margin-bottom: 1rem;
    }
    
    .no-products-title {
        font-size: 1.5rem;
        color: #666;
        margin-bottom: 1rem;
    }
    
    .no-products-text {
        color: #999;
        margin-bottom: 2rem;
    }
    
    .continue-shopping-btn {
        background: #d4af37;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 6px;
        text-decoration: none;
        display: inline-block;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .continue-shopping-btn:hover {
        background: #b8941f;
        color: white;
        text-decoration: none;
    }
    
    /* Floating Action Buttons */
    .floating-buttons {
        position: fixed;
        bottom: 30px;
        left: 30px;
        display: flex;
        flex-direction: column;
        gap: 15px;
        z-index: 1000;
    }
    
    .floating-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        border: none;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        min-width: 120px;
        justify-content: center;
        text-decoration: none;
    }
    
    .wishlist-btn {
        background: #fff;
        color: #d4af37;
        border: 2px solid #d4af37;
    }
    
    .wishlist-btn:hover {
        background: #d4af37;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
        text-decoration: none;
    }
    
    .bag-btn {
        background: #d4af37;
        color: white;
        border: 2px solid #d4af37;
    }
    
    .bag-btn:hover {
        background: #b8941f;
        border-color: #b8941f;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
        text-decoration: none;
    }
    
    .floating-btn i {
        font-size: 1.1rem;
    }
    
    .floating-btn span {
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    
    @media (max-width: 1200px) {
        .products-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }
    
    @media (max-width: 900px) {
        .products-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .products-container {
            padding: 1rem;
        }
        
        .products-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }
        
        .search-filter-form {
            flex-direction: column;
            align-items: stretch;
        }
        
        .form-group {
            min-width: auto;
        }
        
        .floating-buttons {
            bottom: 20px;
            left: 20px;
            gap: 12px;
        }
        
        .floating-btn {
            padding: 10px 16px;
            font-size: 0.8rem;
            min-width: 100px;
        }
    }
    
    @media (max-width: 480px) {
        .products-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="products-container">
    <div class="products-header">
        <h1 class="products-title">Our Products</h1>
        <p class="products-subtitle">Discover our beautiful collection of jewelry</p>
    </div>
    
    <!-- Search and Filter Section -->
    <div class="search-filter-section">
        <form method="GET" action="{{ route('products.index') }}" class="search-filter-form">
            <div class="form-group">
                <label for="search">Search Products</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Search by name, description...">
            </div>
            
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category">
                    <option value="all" {{ request('category') == 'all' || !request('category') ? 'selected' : '' }}>All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                            {{ ucfirst($category) }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <button type="submit" class="search-btn">
                <i class="fas fa-search"></i> Search
            </button>
            
            <a href="{{ route('products.index') }}" class="clear-btn">
                <i class="fas fa-times"></i> Clear
            </a>
        </form>
    </div>
    
    <!-- Products Grid -->
    @if($products->count() > 0)
        <!-- Results Info -->
        <div class="results-info" style="margin-bottom: 2rem; text-align: center; color: #666; font-size: 0.9rem;">
            Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} products
            @if(request('search') || request('category') != 'all')
                (filtered)
                    @endif
        </div>
        
        <div class="products-grid">
            @foreach($products as $product)
                @php
                    $data = $product->getDecoratedData();
                @endphp
                <div class="product-card">
                    <img src="{{ $data['main_image'] }}" alt="{{ $data['name'] }}" class="product-image">
                    
                    <div class="product-content">
                        <h3 class="product-name">{{ $data['name'] }}</h3>
                        <div class="product-category">{{ ucfirst($data['category']) }}</div>
                        <div class="product-price">RM {{ number_format($data['display_price'], 2) }}</div>
                        
                        <div class="product-actions">
                            <a href="{{ route('products.show', $product->product->id) }}" class="view-details-btn">
                                View Details
                            </a>
                            <button class="add-to-cart-btn" onclick="addToCart({{ $product->product->id }})">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        @if($products->hasPages())
            <div class="pagination-container">
                {{ $products->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
            </div>
        @endif
    @else
        <div class="no-products">
            <div class="no-products-icon">🔍</div>
            <h2 class="no-products-title">No products found</h2>
            <p class="no-products-text">
                @if(request('search') || request('category') != 'all')
                    Try adjusting your search criteria or browse all products.
                @else
                    No products are available at the moment.
                @endif
            </p>
            <a href="{{ route('products.index') }}" class="continue-shopping-btn">
                Browse All Products
            </a>
        </div>
    @endif
    </div>

<!-- Floating Action Buttons -->
<div class="floating-buttons">
    <a href="{{ route('wishlist.index') }}" class="floating-btn wishlist-btn" title="View Wishlist">
        <i class="fas fa-heart"></i>
        <span>Wishlist</span>
    </a>
    <a href="{{ route('cart.index') }}" class="floating-btn bag-btn" title="View Cart">
        <i class="fas fa-shopping-bag"></i>
        <span>BAG</span>
    </a>
    </div>

<script>
function addToCart(productId) {
    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Product added to cart successfully!');
        } else {
            alert('Error: ' + (data.message || 'Failed to add product to cart'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}
</script>
@endsection
