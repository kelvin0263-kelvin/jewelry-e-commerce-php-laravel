@extends('layouts.app')

@section('title', 'Shopping Cart')

@push('styles')
<style>
    .cart-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
        background: #f8f9fa;
        min-height: 100vh;
    }
    
    .cart-header {
        text-align: center;
        margin-bottom: 3rem;
    }
    
    .cart-title {
        font-size: 2.5rem;
        font-weight: 300;
        color: #2c2c2c;
        margin-bottom: 1rem;
        font-family: 'Times New Roman', serif;
    }
    
    .cart-subtitle {
        color: #666;
        font-size: 1.1rem;
    }
    
    .cart-content {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    
    .cart-items {
        margin-bottom: 2rem;
    }
    
    .cart-item {
        display: flex;
        align-items: center;
        padding: 1.5rem 0;
        border-bottom: 1px solid #eee;
        gap: 1.5rem;
    }
    
    .cart-item:last-child {
        border-bottom: none;
    }
    
    .item-image {
        width: 120px;
        height: 120px;
        border-radius: 8px;
        object-fit: cover;
        flex-shrink: 0;
    }
    
    .item-details {
        flex: 1;
    }
    
    .item-name {
        font-size: 1.2rem;
        font-weight: 600;
        color: #2c2c2c;
        margin-bottom: 0.5rem;
    }
    
    .item-price {
        font-size: 1.1rem;
        color: #d4af37;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .item-category {
        color: #666;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .item-controls {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .quantity-controls {
        display: flex;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 6px;
        overflow: hidden;
    }
    
    .quantity-btn {
        background: #f8f9fa;
        border: none;
        padding: 0.5rem 0.75rem;
        cursor: pointer;
        font-size: 1.1rem;
        color: #666;
        transition: all 0.3s ease;
    }
    
    .quantity-btn:hover {
        background: #e9ecef;
        color: #d4af37;
    }
    
    .quantity-input {
        border: none;
        width: 60px;
        text-align: center;
        padding: 0.5rem;
        font-size: 1rem;
        font-weight: 600;
    }
    
    .remove-btn {
        background: #dc3545;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }
    
    .remove-btn:hover {
        background: #c82333;
    }
    
    .cart-summary {
        background: #f8f9fa;
        padding: 2rem;
        border-radius: 8px;
        margin-top: 2rem;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
        font-size: 1.1rem;
    }
    
    .summary-total {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c2c2c;
        border-top: 2px solid #d4af37;
        padding-top: 1rem;
        margin-top: 1rem;
    }
    
    .checkout-btn {
        background: #d4af37;
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        width: 100%;
        margin-top: 1.5rem;
        transition: all 0.3s ease;
    }
    
    .checkout-btn:hover {
        background: #b8941f;
        transform: translateY(-2px);
    }
    
    .empty-cart {
        text-align: center;
        padding: 4rem 2rem;
    }
    
    .empty-cart-icon {
        font-size: 4rem;
        color: #ddd;
        margin-bottom: 1rem;
    }
    
    .empty-cart-title {
        font-size: 1.5rem;
        color: #666;
        margin-bottom: 1rem;
    }
    
    .empty-cart-text {
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
        
        .cart-container {
            padding: 1rem;
        }
        
        .cart-item {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }
        
        .item-image {
            width: 200px;
            height: 200px;
        }
        
        .item-controls {
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="cart-container">
    <div class="cart-header">
        <h1 class="cart-title">Shopping Cart</h1>
        <p class="cart-subtitle">Review your items before checkout</p>
    </div>
    
    <div class="cart-content">
        @if($cartItems->count() > 0)
            <div class="cart-items">
                @foreach($cartItems as $item)
                    <div class="cart-item" data-item-id="{{ $item->id }}">
                        <img src="{{ $item->product->customer_images && count($item->product->customer_images) > 0 ? asset('storage/' . $item->product->customer_images[0]) : asset('/img/default-product.jpg') }}" 
                             alt="{{ $item->product->name }}" class="item-image">
                        
                        <div class="item-details">
                            <h3 class="item-name">{{ $item->product->name }}</h3>
                            <div class="item-price">RM {{ number_format($item->price, 2) }}</div>
                            <div class="item-category">{{ ucfirst($item->product->category) }}</div>
                        </div>
                        
                        <div class="item-controls">
                            <div class="quantity-controls">
                                <button class="quantity-btn" onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})">-</button>
                                <input type="number" class="quantity-input" value="{{ $item->quantity }}" 
                                       min="1" max="99" onchange="updateQuantity({{ $item->id }}, this.value)">
                                <button class="quantity-btn" onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})">+</button>
                            </div>
                            
                            <button class="remove-btn" onclick="removeItem({{ $item->id }})">
                                Remove
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="cart-summary">
                <div class="summary-row">
                    <span>Subtotal ({{ $cartItems->sum('quantity') }} items):</span>
                    <span>RM {{ number_format($total, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span>Free</span>
                </div>
                <div class="summary-row summary-total">
                    <span>Total:</span>
                    <span>RM {{ number_format($total, 2) }}</span>
                </div>
                
                <a href="{{ route('checkout.index') }}" class="checkout-btn">
                    Proceed to Checkout
                </a>
            </div>
        @else
            <div class="empty-cart">
                <div class="empty-cart-icon">🛒</div>
                <h2 class="empty-cart-title">Your cart is empty</h2>
                <p class="empty-cart-text">Looks like you haven't added any items to your cart yet.</p>
                <a href="{{ route('products.index') }}" class="continue-shopping-btn">
                    Continue Shopping
                </a>
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
function updateQuantity(itemId, newQuantity) {
    if (newQuantity < 1) {
        removeItem(itemId);
        return;
    }
    
    if (newQuantity > 99) {
        newQuantity = 99;
    }
    
    fetch(`/cart/update/${itemId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ quantity: newQuantity })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating quantity: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

function removeItem(itemId) {
    if (confirm('Are you sure you want to remove this item from your cart?')) {
        fetch(`/cart/remove/${itemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
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
</script>
@endsection
