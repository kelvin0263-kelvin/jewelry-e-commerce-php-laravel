@extends('layouts.app')

@section('title', 'Checkout')

@push('styles')
<style>
    .checkout-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
        background: #f8f9fa;
        min-height: 100vh;
    }
    
    .checkout-header {
        text-align: center;
        margin-bottom: 3rem;
    }
    
    .checkout-title {
        font-size: 2.5rem;
        font-weight: 300;
        color: #2c2c2c;
        margin-bottom: 1rem;
        font-family: 'Times New Roman', serif;
    }
    
    .checkout-subtitle {
        color: #666;
        font-size: 1.1rem;
    }
    
    .checkout-content {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 2rem;
    }
    
    .checkout-form {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    
    .form-section {
        margin-bottom: 2rem;
    }
    
    .section-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #2c2c2c;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #d4af37;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #2c2c2c;
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 1rem;
        font-family: inherit;
    }
    
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #d4af37;
        box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2);
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
    .order-summary {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        height: fit-content;
    }
    
    .order-items {
        margin-bottom: 2rem;
    }
    
    .order-item {
        display: flex;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid #eee;
        gap: 1rem;
    }
    
    .order-item:last-child {
        border-bottom: none;
    }
    
    .order-item-image {
        width: 60px;
        height: 60px;
        border-radius: 6px;
        object-fit: cover;
        flex-shrink: 0;
    }
    
    .order-item-details {
        flex: 1;
    }
    
    .order-item-name {
        font-size: 0.9rem;
        font-weight: 600;
        color: #2c2c2c;
        margin-bottom: 0.25rem;
    }
    
    .order-item-quantity {
        font-size: 0.8rem;
        color: #666;
    }
    
    .order-item-price {
        font-size: 0.9rem;
        color: #d4af37;
        font-weight: 600;
    }
    
    .order-totals {
        border-top: 2px solid #f0f0f0;
        padding-top: 1rem;
    }
    
    .total-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.75rem;
        font-size: 1rem;
    }
    
    .total-final {
        font-size: 1.3rem;
        font-weight: 700;
        color: #2c2c2c;
        border-top: 2px solid #d4af37;
        padding-top: 0.75rem;
        margin-top: 0.75rem;
    }
    
    .place-order-btn {
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
    
    .place-order-btn:hover {
        background: #b8941f;
        transform: translateY(-2px);
    }
    
    .place-order-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
    }
    
    @media (max-width: 768px) {
        .checkout-container {
            padding: 1rem;
        }
        
        .checkout-content {
            grid-template-columns: 1fr;
        }
        
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="checkout-container">
    <div class="checkout-header">
        <h1 class="checkout-title">Checkout</h1>
        <p class="checkout-subtitle">Complete your purchase</p>
    </div>
    
    <div class="checkout-content">
        <div class="checkout-form">
            <form id="checkoutForm">
                <div class="form-section">
                    <h2 class="section-title">Shipping Information</h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name *</label>
                            <input type="text" id="first_name" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number *</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address *</label>
                        <textarea id="address" name="address" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">City *</label>
                            <input type="text" id="city" name="city" required>
                        </div>
                        <div class="form-group">
                            <label for="postal_code">Postal Code *</label>
                            <input type="text" id="postal_code" name="postal_code" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="state">State *</label>
                        <select id="state" name="state" required>
                            <option value="">Select State</option>
                            <option value="Kuala Lumpur">Kuala Lumpur</option>
                            <option value="Selangor">Selangor</option>
                            <option value="Penang">Penang</option>
                            <option value="Johor">Johor</option>
                            <option value="Sabah">Sabah</option>
                            <option value="Sarawak">Sarawak</option>
                            <option value="Perak">Perak</option>
                            <option value="Kedah">Kedah</option>
                            <option value="Kelantan">Kelantan</option>
                            <option value="Terengganu">Terengganu</option>
                            <option value="Pahang">Pahang</option>
                            <option value="Negeri Sembilan">Negeri Sembilan</option>
                            <option value="Melaka">Melaka</option>
                            <option value="Perlis">Perlis</option>
                            <option value="Putrajaya">Putrajaya</option>
                            <option value="Labuan">Labuan</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-section">
                    <h2 class="section-title">Payment Information</h2>
                    
                    <div class="form-group">
                        <label for="payment_method">Payment Method *</label>
                        <select id="payment_method" name="payment_method" required>
                            <option value="">Select Payment Method</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cod">Cash on Delivery</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Order Notes (Optional)</label>
                        <textarea id="notes" name="notes" rows="3" placeholder="Any special instructions for your order..."></textarea>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="order-summary">
            <h2 class="section-title">Order Summary</h2>
            
            <div class="order-items">
                @foreach($cartItems as $item)
                    <div class="order-item">
                        <img src="{{ $item->product->customer_images && count($item->product->customer_images) > 0 ? asset('storage/' . $item->product->customer_images[0]) : asset('/img/default-product.jpg') }}" 
                             alt="{{ $item->product->name }}" class="order-item-image">
                        
                        <div class="order-item-details">
                            <div class="order-item-name">{{ $item->product->name }}</div>
                            <div class="order-item-quantity">Qty: {{ $item->quantity }}</div>
                        </div>
                        
                        <div class="order-item-price">RM {{ number_format($item->price * $item->quantity, 2) }}</div>
                    </div>
                @endforeach
            </div>
            
            <div class="order-totals">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>RM {{ number_format($total, 2) }}</span>
                </div>
                <div class="total-row">
                    <span>Shipping:</span>
                    <span>Free</span>
                </div>
                <div class="total-row total-final">
                    <span>Total:</span>
                    <span>RM {{ number_format($total, 2) }}</span>
                </div>
            </div>
            
            <button type="submit" form="checkoutForm" class="place-order-btn">
                Place Order
            </button>
        </div>
    </div>
</div>

<script>
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Show loading state
    const submitBtn = document.querySelector('.place-order-btn');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Processing...';
    
    // Simulate order processing
    setTimeout(() => {
        alert('Order placed successfully! Thank you for your purchase.');
        // In a real application, you would redirect to a success page
        // window.location.href = '/order-success';
    }, 2000);
});
</script>
@endsection
