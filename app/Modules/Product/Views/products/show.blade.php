@extends('layouts.app')

@section('title', $decoratedProduct->product->name)

@push('styles')
<style>
    /* 完全按照图片设计 */
    .product-page {
        background-color: #faf8f5;
        min-height: 100vh;
        font-family: 'Arial', sans-serif;
        padding: 1rem;
    }
    
    .product-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 40px;
        max-width: 800px;
        margin: 0 auto;
        overflow-wrap: break-word;
        word-wrap: break-word;
    }
    
    @media (min-width: 1024px) {
        .product-layout {
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }
    }
    
    .image-section {
        width: 100%;
    }
    
    .content-section {
        width: 100%;
        padding-left: 0;
        overflow-wrap: break-word;
        word-wrap: break-word;
        word-break: break-all;
        max-width: 100%;
        overflow: hidden;
    }
    
    @media (min-width: 1024px) {
        .content-section {
            padding-left: 0px;
        }
    }
    
    .main-image-container {
        position: relative;
        background-color: #f5f3f0;
        border-radius: 12px;
        overflow: hidden;
        aspect-ratio: 1;
    }
    
    
    .product-title {
        font-size: 1.5rem;
        font-weight: 400;
        color: #2c2c2c;
        margin: 0 0 1rem 0;
        line-height: 1.2;
        font-family: 'Times New Roman', serif;
        letter-spacing: 0.3px;
        word-wrap: break-word;
        overflow-wrap: break-word;
        word-break: break-all;
        hyphens: auto;
        max-width: 100%;
        overflow: hidden;
    }
    
    .title-container {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 1rem;
    }
    
    .starburst-icon {
        width: 18px;
        height: 18px;
        background: #f0f0f0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 8px;
        flex-shrink: 0;
    }
    
    .starburst-icon::before {
        content: '✦';
        font-size: 10px;
        color: #999;
    }
    
    .price-display {
        font-size: 1.3rem;
        font-weight: 600;
        color: #2c2c2c;
        margin: 0 0 1.5rem 0;
    }
    
    .marketing-description {
        background: transparent;
        padding: 0;
        margin-bottom: 1.5rem;
        word-wrap: break-word;
        overflow-wrap: break-word;
        word-break: break-all;
        hyphens: auto;
        overflow: hidden;
        max-width: 100%;
    }
    
    .marketing-description p {
        font-size: 0.85rem;
        color: #666;
        margin-bottom: 0.2rem;
        line-height: 1.3;
        word-wrap: break-word;
        overflow-wrap: break-word;
        word-break: break-all;
        hyphens: auto;
        max-width: 100%;
        white-space: pre-wrap;
        overflow: hidden;
    }
    
    
    .product-features {
        margin-bottom: 1.5rem;
    }
    
    .features-label {
        font-size: 0.85rem;
        color: #666;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    
    .features-list {
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
    }
    
    .feature-item {
        font-size: 0.85rem;
        color: #666;
        line-height: 1.4;
        word-wrap: break-word;
        overflow-wrap: break-word;
        word-break: break-all;
        hyphens: auto;
        max-width: 100%;
        overflow: hidden;
    }
    
    .quantity-selection {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }
    
    .quantity-label {
        font-size: 0.85rem;
        font-weight: 500;
        color: #666;
    }
    
    .quantity-controls {
        display: flex;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 4px;
        overflow: hidden;
    }
    
    .quantity-btn {
        background: #f8f9fa;
        border: none;
        padding: 0.5rem 0.75rem;
        font-size: 1rem;
        font-weight: 600;
        color: #666;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 40px;
    }
    
    .quantity-btn:hover {
        background: #e9ecef;
        color: #d4af37;
    }
    
    .quantity-input {
        border: none;
        padding: 0.5rem;
        text-align: center;
        font-size: 0.85rem;
        font-weight: 500;
        color: #2c2c2c;
        width: 60px;
        outline: none;
    }
    
    .quantity-input::-webkit-outer-spin-button,
    .quantity-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    
    .quantity-input[type=number] {
        -moz-appearance: textfield;
    }
    
    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }
    
    .buy-now-btn {
        background: #d4af37;
        color: white;
        border: none;
        padding: 0.4rem 0.8rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
    }
    
    .buy-now-btn:hover {
        background: #b8941f;
    }
    
    .add-to-bag-btn {
        background: transparent;
        color: #d4af37;
        border: 1px solid #d4af37;
        padding: 0.4rem 0.8rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        text-transform: uppercase;
    }
    
    .add-to-bag-btn:hover {
        background: #d4af37;
        color: white;
    }
    
    .additional-actions {
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
        margin-top: 0rem;
    }
    
    .action-link {
        color: #2c2c2c;
        text-decoration: underline;
        font-size: 0.9rem;
        font-weight: 400;
        cursor: pointer;
        transition: color 0.3s ease;
    }
    
    .action-link:hover {
        color: #d4af37;
        text-decoration: underline;
    }
    
    .wishlist-btn {
        background: transparent;
        color: #d4af37;
        border: 1px solid #d4af37;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        text-transform: uppercase;
    }
    
    .wishlist-btn:hover {
        background: #d4af37;
        color: white;
    }
    
    
    .thumbnail-gallery {
        display: flex;
        gap: 8px;
        margin-top: 1rem;
        position: relative;
        align-items: center;
    }
    
    .thumbnail {
        width: 70px;
        height: 70px;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }
    
    .thumbnail:hover,
    .thumbnail.active {
        border-color: #d4af37;
    }
    
    .thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    
    .shipping-info {
        display: flex;
        gap: 2rem;
        padding: 1rem 0;
        border-top: 1px solid #ddd;
        margin-top: 2rem;
    }
    
    .shipping-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        color: #666;
    }
    
    .shipping-item:not(:last-child)::after {
        content: '|';
        margin-left: 2rem;
        color: #ddd;
    }
    
    .shipping-item i {
        font-size: 14px;
    }
    
    /* Recommendations Section */
    .recommendations-section {
        margin-top: 4rem;
        padding: 2rem 0;
    }
    
    .recommendations-header {
        margin-bottom: 2rem;
    }
    
    .recommendations-title {
        font-size: 1.5rem;
        font-weight: 400;
        color: #2c2c2c;
        font-family: 'Times New Roman', serif;
        margin: 0;
    }
    
    
    .recommendations-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    
    .recommendation-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .recommendation-card:hover {
        transform: translateY(-2px);
    }
    
    .card-image {
        width: 100%;
        height: 200px;
        overflow: hidden;
        background: #f5f3f0;
    }
    
    .card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .card-content {
        padding: 1rem;
    }
    
    .card-title {
        font-size: 1rem;
        font-weight: 500;
        color: #2c2c2c;
        margin: 0 0 0.5rem 0;
        line-height: 1.3;
    }
    
    .card-price {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c2c2c;
        margin-bottom: 1rem;
    }
    
    .card-button {
        width: 100%;
        background: #d4af37;
        color: white;
        border: none;
        padding: 0.6rem 1rem;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }
    
    .card-button:hover {
        background: #b8941f;
        color: white;
        text-decoration: none;
    }
    
    /* Reviews Section */
    .reviews-section {
        margin-top: 3rem;
        padding: 1.5rem 0;
    }
    
    .reviews-header {
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .reviews-title-container {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .reviews-starburst {
        width: 20px;
        height: 20px;
        background: #d4af37;
        clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
    }
    
    .reviews-title {
        font-size: 1.3rem;
        font-weight: 400;
        color: #2c2c2c;
        font-family: 'Times New Roman', serif;
        margin: 0;
    }
    
    .reviews-content {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
        align-items: start;
    }
    
    .reviews-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .no-reviews-message {
        background: #f8f9fa;
        padding: 2rem;
        border-radius: 12px;
        text-align: center;
        color: #666;
        font-style: italic;
    }
    
    .review-card {
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .review-rating {
        margin-bottom: 1rem;
    }
    
    .review-rating .stars {
        color: #d4af37;
        font-size: 1.1rem;
    }
    
    .review-meta {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
        font-size: 0.85rem;
        color: #666;
    }
    
    .reviewer-name {
        font-weight: 500;
    }
    
    .review-headline {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c2c2c;
        margin: 0 0 1rem 0;
    }
    
    .review-text {
        margin-bottom: 1.5rem;
    }
    
    .review-text p {
        font-size: 0.9rem;
        color: #666;
        line-height: 1.5;
        margin: 0;
    }
    
    .write-review-btn {
        background: #d4af37;
        color: white;
        border: 1px solid #d4af37;
        padding: 0.5rem 1.2rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .write-review-btn:hover {
        background: #b8941f;
        border-color: #b8941f;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(212, 175, 55, 0.3);
    }
    
    .reviews-image {
        width: 100%;
        height: 300px;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .reviews-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    /* Modal Styles */
    .modal {
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .modal-content {
        background-color: white;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid #eee;
    }
    
    .modal-header h2 {
        margin: 0;
        font-size: 1.5rem;
        color: #2c2c2c;
    }
    
    .close {
        font-size: 2rem;
        font-weight: bold;
        cursor: pointer;
        color: #999;
        line-height: 1;
    }
    
    .close:hover {
        color: #333;
    }
    
    .modal-body {
        padding: 1.5rem;
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
    .form-group textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 1rem;
        font-family: inherit;
    }
    
    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #d4af37;
        box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2);
    }
    
    .rating-input {
        display: flex;
        gap: 0.25rem;
    }
    
    .star {
        font-size: 2rem;
        color: #ddd;
        cursor: pointer;
        transition: color 0.2s;
    }
    
    .star:hover,
    .star.active {
        color: #d4af37;
    }
    
    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
    }
    
    .btn-cancel,
    .btn-submit {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-cancel {
        background: #f5f5f5;
        color: #666;
    }
    
    .btn-cancel:hover {
        background: #e5e5e5;
    }
    
    .btn-submit {
        background: #d4af37;
        color: white;
    }
    
    .btn-submit:hover {
        background: #b8941f;
    }
    
    .success-modal {
        text-align: center;
    }
    
    .success-icon {
        font-size: 4rem;
        color: #4CAF50;
        margin-bottom: 1rem;
    }
    
    .success-modal h2 {
        color: #2c2c2c;
        margin-bottom: 1rem;
    }
    
    .success-modal p {
        color: #666;
        margin-bottom: 2rem;
    }
    
    .btn-ok {
        background: #d4af37;
        color: white;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-ok:hover {
        background: #b8941f;
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
        .product-title {
            font-size: 1.8rem;
        }
        
        .price-display {
            font-size: 1.5rem;
        }
        
        .shipping-info {
            flex-direction: column;
            gap: 1rem;
        }
        
        .shipping-item:not(:last-child)::after {
            display: none;
        }
    }
</style>
@endpush

@section('content')
<div class="product-page">
    <div class="container mx-auto px-4 py-8">
        @php
            $data = $decoratedProduct->getDecoratedData();
        @endphp
        
        <div class="product-layout">
            <!-- Product Images Section -->
            <div class="image-section">
                   <!-- Main Product Image -->
                   <div class="main-image-container">
                       <img src="{{ $data['main_image'] }}" alt="{{ $data['name'] }}" 
                            class="w-full h-full object-cover" id="mainImage">
                   </div>
                
                <!-- Thumbnail Gallery -->
                @if(count($data['gallery_images']) > 1)
                    <div class="thumbnail-gallery">
                        <div class="flex gap-2 overflow-hidden" id="thumbnailContainer">
                            @foreach($data['gallery_images'] as $index => $image)
                                <div class="thumbnail {{ $index === 0 ? 'active' : '' }}" 
                                     onclick="changeMainImage('{{ $image }}', this)">
                                    <img src="{{ $image }}" alt="{{ $data['name'] }} - Image {{ $index + 1 }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Product Information Section -->
            <div class="content-section">
                <!-- Product Title with Starburst -->
                <div class="title-container">
                    <div class="starburst-icon"></div>
                    <h1 class="product-title">{{ $data['name'] }}</h1>
                </div>
                
                <!-- Price -->
                <div class="price-display">RM {{ number_format($data['display_price'], 2) }}</div>
                
                   <!-- Marketing Description -->
                   <div class="marketing-description">
                       <p>{{ $data['marketing_description'] }}</p>
                   </div>
                
                <!-- Product Features -->
                <div class="product-features">
                    <div class="features-label">Features:</div>
                    <div class="features-list">
                        @if($data['features'] && count($data['features']) > 0)
                            @foreach($data['features'] as $feature)
                                <div class="feature-item">• {{ $feature }}</div>
                            @endforeach
                        @else
                            <div class="feature-item">• Premium Quality</div>
                            <div class="feature-item">• Handcrafted Design</div>
                        @endif
                    </div>
                </div>
                
                <!-- Quantity Selection -->
                <div class="quantity-selection">
                    <div class="quantity-label">Quantity:</div>
                    <div class="quantity-controls">
                        <button class="quantity-btn" onclick="decreaseQuantity()">-</button>
                        <input type="number" id="quantity" class="quantity-input" value="1" min="1" max="99">
                        <button class="quantity-btn" onclick="increaseQuantity()">+</button>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button class="buy-now-btn" onclick="buyNow({{ $decoratedProduct->product->id }})">
                        BUY NOW
                    </button>
                    <button class="add-to-bag-btn" onclick="addToCart({{ $decoratedProduct->product->id }})">
                        ADD TO BAG
                    </button>
                </div>
                
                <div class="additional-actions">
                    <button class="wishlist-btn" onclick="addToWishlist({{ $decoratedProduct->product->id }})">ADD TO WISHLIST</button>
                    <a href="#" class="action-link" onclick="shareHint({{ $decoratedProduct->product->id }})">share a hint</a>
                </div>
                
            </div>
        </div>
        
        <!-- Shipping Information -->
        <div class="shipping-info">
            <div class="shipping-item">
                <i class="fas fa-truck"></i>
                Free Shipping on orders RM75+
            </div>
            <div class="shipping-item">
                <i class="fas fa-calendar-alt"></i>
                Free Extended Returns to 1/30
            </div>
            <div class="shipping-item">
                <i class="fas fa-box"></i>
                Ship To Home: delivered in 2-4 business days
            </div>
        </div>
        
        <!-- You May Also Like Section -->
        <div class="recommendations-section">
            <div class="recommendations-header">
                <h2 class="recommendations-title">You may also like</h2>
            </div>
            
            <div class="recommendations-grid">
                @php
                    use App\Modules\Product\Models\Product;
                    use App\Modules\Product\Decorators\CustomerProductDecorator;
                    
                    // Get random products from database (excluding current product)
                    $recommendations = Product::where('id', '!=', $decoratedProduct->product->id)
                        ->where('is_visible', true)
                        ->inRandomOrder()
                        ->limit(4)
                        ->get()
                        ->map(function($product) {
                            return new CustomerProductDecorator($product);
                        });
                @endphp
                
                @foreach($recommendations as $recommendedProduct)
                    @php
                        $recommendedData = $recommendedProduct->getDecoratedData();
                    @endphp
                    <div class="recommendation-card">
                        <div class="card-image">
                            <img src="{{ $recommendedData['main_image'] ?: '/img/default-product.jpg' }}" alt="{{ $recommendedData['name'] }}">
                        </div>
                        <div class="card-content">
                            <h3 class="card-title">{{ $recommendedData['name'] }}</h3>
                            <div class="card-price">RM {{ number_format($recommendedData['display_price'], 2) }}</div>
                            <a href="{{ route('products.show', $recommendedProduct->product->id) }}" class="card-button">VIEW DETAILS</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- Reviews Section -->
        <div class="reviews-section">
            <div class="reviews-header">
                <div class="reviews-title-container">
                    <div class="reviews-starburst"></div>
                    <h2 class="reviews-title">Reviews</h2>
                </div>
                <button class="write-review-btn" onclick="openReviewModal()">WRITE A REVIEW</button>
            </div>
            
            <div class="reviews-content">
                @if($reviews->count() > 0)
                    <div class="reviews-list">
                        @foreach($reviews as $review)
                            <div class="review-card">
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
                                </div>
                                
                                <div class="review-meta">
                                    <span class="reviewer-name">{{ $review->reviewer_name }}</span>
                                    <span class="review-date">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                
                                <h3 class="review-headline">{{ $review->title }}</h3>
                                
                                <div class="review-text">
                                    <p>{{ $review->content }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="no-reviews-message">
                        <p>No reviews yet. Be the first to review this product!</p>
                    </div>
                @endif
                
                <div class="reviews-image">
                    <img src="https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=400&h=400&fit=crop&crop=center" alt="Charm Bracelet">
                </div>
            </div>
        </div>
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

<!-- Write Review Modal -->
<div id="reviewModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Write a Review</h2>
            <span class="close" onclick="closeReviewModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="reviewForm">
                <div class="form-group">
                    <label for="reviewerName">Your Name</label>
                    <input type="text" id="reviewerName" name="reviewerName" required>
                </div>
                <div class="form-group">
                    <label for="reviewRating">Rating</label>
                    <div class="rating-input">
                        <span class="star" data-rating="1">★</span>
                        <span class="star" data-rating="2">★</span>
                        <span class="star" data-rating="3">★</span>
                        <span class="star" data-rating="4">★</span>
                        <span class="star" data-rating="5">★</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="reviewTitle">Review Title</label>
                    <input type="text" id="reviewTitle" name="reviewTitle" required>
                </div>
                <div class="form-group">
                    <label for="reviewText">Your Review</label>
                    <textarea id="reviewText" name="reviewText" rows="4" required></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" onclick="closeReviewModal()" class="btn-cancel">Cancel</button>
                    <button type="submit" class="btn-submit">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Success Message Modal -->
<div id="successModal" class="modal" style="display: none;">
    <div class="modal-content success-modal">
        <div class="modal-body">
            <div class="success-icon">✓</div>
            <h2>Review Submitted Successfully!</h2>
            <p>Thank you for your review.</p>
            <button onclick="closeSuccessModal()" class="btn-ok">OK</button>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    function changeMainImage(imageSrc, thumbnail) {
        // Update main image
        document.getElementById('mainImage').src = imageSrc;
        
        // Update active thumbnail
        document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
        thumbnail.classList.add('active');
    }
    
    
    
    function increaseQuantity() {
        const input = document.getElementById('quantity');
        const currentValue = parseInt(input.value);
        if (currentValue < 99) {
            input.value = currentValue + 1;
        }
    }
    
    function decreaseQuantity() {
        const input = document.getElementById('quantity');
        const currentValue = parseInt(input.value);
        if (currentValue > 1) {
            input.value = currentValue - 1;
        }
    }
    
    
    function addToCart(productId) {
        const quantity = document.getElementById('quantity').value;
        
        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: parseInt(quantity)
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
    
    function buyNow(productId) {
        // First add to cart, then redirect to checkout
        const quantity = document.getElementById('quantity').value;
        
        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: parseInt(quantity)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/checkout';
            } else {
                alert('Error: ' + (data.message || 'Failed to add product to cart'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
    
    function sendWithSmartGift() {
        alert('SmartGift feature coming soon!');
    }
    
    function addToWishlist(productId) {
        fetch('/wishlist/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Product added to wishlist successfully!');
            } else {
                alert('Error: ' + (data.message || 'Failed to add product to wishlist'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
    
    function shareHint(productId) {
        // TODO: Implement share hint functionality
        if (navigator.share) {
            navigator.share({
                title: 'Check out this product!',
                text: 'I found this amazing product that you might like.',
                url: window.location.href
            });
        } else {
            // Fallback for browsers that don't support Web Share API
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                alert('Product link copied to clipboard!');
            }).catch(() => {
                alert('Product URL: ' + url);
            });
        }
    }
    
    function viewWishlist() {
        // TODO: Implement wishlist page
        alert('Wishlist page coming soon!');
    }
    
    // Review Modal Functions
    function openReviewModal() {
        document.getElementById('reviewModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeReviewModal() {
        document.getElementById('reviewModal').style.display = 'none';
        document.body.style.overflow = 'auto';
        resetReviewForm();
    }
    
    function closeSuccessModal() {
        document.getElementById('successModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    function resetReviewForm() {
        document.getElementById('reviewForm').reset();
        document.querySelectorAll('.star').forEach(star => star.classList.remove('active'));
    }
    
    // Star Rating Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.star');
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.getAttribute('data-rating');
                stars.forEach((s, index) => {
                    if (index < rating) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
        });
        
        // Form Submission
        document.getElementById('reviewForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const rating = document.querySelectorAll('.star.active').length;
            
            if (rating === 0) {
                alert('Please select a rating');
                return;
            }
            
            // Get product ID from the current page
            const productId = {{ $decoratedProduct->product->id }};
            
            // Prepare data for submission
            const reviewData = {
                product_id: productId,
                reviewer_name: document.getElementById('reviewerName').value,
                rating: rating,
                title: document.getElementById('reviewTitle').value,
                content: document.getElementById('reviewText').value,
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            };
            
            // Submit to server
            fetch('/reviews', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(reviewData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeReviewModal();
                    document.getElementById('successModal').style.display = 'flex';
                    // Refresh page after 2 seconds to show the new review
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    alert('Error: ' + (data.message || 'Failed to submit review'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
        
        // WRITE A REVIEW button now uses onclick in HTML
    });
</script>
@endsection