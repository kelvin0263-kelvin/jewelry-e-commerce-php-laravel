@extends('layouts.app')

@section('title', 'Wishlist')

@push('styles')
<style>
    .wishlist-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
        background: #f8f9fa;
        min-height: 100vh;
    }
    
    .wishlist-header {
        text-align: center;
        margin-bottom: 3rem;
    }
    
    .wishlist-title {
        font-size: 2.5rem;
        font-weight: 300;
        color: #2c2c2c;
        margin-bottom: 1rem;
        font-family: 'Times New Roman', serif;
    }
    
    .wishlist-subtitle {
        color: #666;
        font-size: 1.1rem;
    }
    
    .wishlist-content {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    
    .wishlist-items {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 2rem;
    }
    
    .wishlist-item {
        border: 1px solid #eee;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .wishlist-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .item-image {
        width: 100%;
        height: 250px;
        object-fit: cover;
        background: #f8f9fa;
    }
    
    .item-content {
        padding: 1.5rem;
    }
    
    .item-name {
        font-size: 1.2rem;
        font-weight: 600;
        color: #2c2c2c;
        margin-bottom: 0.5rem;
        line-height: 1.3;
    }
    
    .item-category {
        color: #666;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.75rem;
    }
    
    .item-price {
        font-size: 1.3rem;
        color: #d4af37;
        font-weight: 700;
        margin-bottom: 1rem;
    }
    
    .item-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    
    .add-to-cart-btn {
        background: #d4af37;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        flex: 1;
        min-width: 120px;
    }
    
    .add-to-cart-btn:hover {
        background: #b8941f;
        transform: translateY(-1px);
    }
    
    .view-details-btn {
        background: transparent;
        color: #d4af37;
        border: 1px solid #d4af37;
        padding: 0.75rem 1.5rem;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        text-align: center;
        flex: 1;
        min-width: 120px;
    }
    
    .view-details-btn:hover {
        background: #d4af37;
        color: white;
        text-decoration: none;
    }
    
    .remove-btn {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: rgba(220, 53, 69, 0.9);
        color: white;
        border: none;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        transition: all 0.3s ease;
        opacity: 0;
    }
    
    .wishlist-item:hover .remove-btn {
        opacity: 1;
    }
    
    .remove-btn:hover {
        background: #dc3545;
        transform: scale(1.1);
    }
    
    .empty-wishlist {
        text-align: center;
        padding: 4rem 2rem;
    }
    
    .empty-wishlist-icon {
        font-size: 4rem;
        color: #ddd;
        margin-bottom: 1rem;
    }
    
    .empty-wishlist-title {
        font-size: 1.5rem;
        color: #666;
        margin-bottom: 1rem;
    }
    
    .empty-wishlist-text {
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
        color: white;
        text-decoration: none;
    }
    
    .floating-btn i {
        font-size: 1.1rem;
    }
    
    .floating-btn span {
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    
    /* Mobile responsiveness for floating buttons */
    @media (max-width: 768px) {
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
        
        .floating-btn i {
            font-size: 1rem;
        }
    }
    
    @media (max-width: 768px) {
        .wishlist-container {
            padding: 1rem;
        }
        
        .wishlist-items {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        .item-actions {
            flex-direction: column;
        }
        
        .add-to-cart-btn,
        .view-details-btn {
            flex: none;
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="wishlist-container">
    <div class="wishlist-header">
        <h1 class="wishlist-title">Wishlist</h1>
        <p class="wishlist-subtitle">Your favorite items saved for later</p>
    </div>
    
    <div class="wishlist-content">
        @if($wishlistItems->count() > 0)
            <div class="wishlist-items">
                @foreach($wishlistItems as $item)
                    <div class="wishlist-item" data-item-id="{{ $item->id }}">
                        <img src="{{ $item->product->customer_images && count($item->product->customer_images) > 0 ? asset('storage/' . $item->product->customer_images[0]) : asset('/img/default-product.jpg') }}" 
                             alt="{{ $item->product->name }}" class="item-image">
                        
                        <button class="remove-btn" onclick="removeFromWishlist({{ $item->id }})" title="Remove from wishlist">
                            ×
                        </button>
                        
                        <div class="item-content">
                            <h3 class="item-name">{{ $item->product->name }}</h3>
                            <div class="item-category">{{ ucfirst($item->product->category) }}</div>
                            <div class="item-price">RM {{ number_format($item->product->discount_price ?? $item->product->price, 2) }}</div>
                            
                            <div class="item-actions">
                                <button class="add-to-cart-btn" onclick="addToCart({{ $item->product->id }})">
                                    Add to Cart
                                </button>
                                <a href="{{ route('products.show', $item->product->id) }}" class="view-details-btn">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-wishlist">
                <div class="empty-wishlist-icon">💖</div>
                <h2 class="empty-wishlist-title">Your wishlist is empty</h2>
                <p class="empty-wishlist-text">Start adding items you love to your wishlist!</p>
                <button onclick="history.back()" class="continue-shopping-btn">
                    Continue Shopping
                </button>
            </div>
        @endif
    </div>
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
function removeFromWishlist(itemId) {
    if (confirm('Are you sure you want to remove this item from your wishlist?')) {
        fetch(`/wishlist/remove/${itemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the item from the page
                const item = document.querySelector(`[data-item-id="${itemId}"]`);
                if (item) {
                    item.remove();
                }
                
                // Check if wishlist is now empty
                const remainingItems = document.querySelectorAll('.wishlist-item');
                if (remainingItems.length === 0) {
                    location.reload();
                }
            } else {
                alert('Error removing item: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
}

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
