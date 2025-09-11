@extends('layouts.app')

@section('title', $inventory->name)

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
        max-width: 1200px;
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
        width: 100%;
        height: 400px;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 1rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .thumbnail-gallery {
        display: flex;
        gap: 0.5rem;
        overflow-x: auto;
        padding: 0.5rem 0;
    }
    
    .thumbnail {
        width: 80px;
        height: 80px;
        border-radius: 4px;
        overflow: hidden;
        cursor: pointer;
        border: 2px solid transparent;
        transition: border-color 0.3s ease;
        flex-shrink: 0;
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
    
    .product-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #2c2c2c;
        margin: 0 0 1rem 0;
        line-height: 1.3;
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
    
    .sku-selection {
        margin-bottom: 1.5rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }
    
    .sku-selection h4 {
        font-size: 1rem;
        font-weight: 600;
        color: #2c2c2c;
        margin: 0 0 1rem 0;
    }
    
    .sku-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0.75rem;
    }
    
    .sku-option {
        padding: 0.75rem;
        border: 2px solid #e9ecef;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
    }
    
    .sku-option:hover {
        border-color: #d4af37;
        box-shadow: 0 2px 4px rgba(212, 175, 55, 0.2);
    }
    
    .sku-option.selected {
        border-color: #d4af37;
        background: #fffbf0;
        box-shadow: 0 2px 8px rgba(212, 175, 55, 0.3);
    }
    
    .sku-option.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: #f8f9fa;
    }
    
    .sku-details {
        margin-bottom: 0.5rem;
    }
    
    .sku-sku {
        font-weight: 600;
        color: #2c2c2c;
        font-size: 0.9rem;
    }
    
    .sku-features {
        font-size: 0.8rem;
        color: #666;
        margin: 0.25rem 0;
    }
    
    .sku-price {
        font-weight: 600;
        color: #d4af37;
        font-size: 0.9rem;
    }
    
    .sku-stock {
        font-size: 0.75rem;
        color: #28a745;
        margin-top: 0.25rem;
    }
    
    .sku-stock.out-of-stock {
        color: #dc3545;
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
    }
    
    .quantity-btn:disabled {
        background: #f8f9fa;
        color: #ccc;
        cursor: not-allowed;
    }
    
    .quantity-input {
        border: none;
        padding: 0.5rem;
        text-align: center;
        font-size: 1rem;
        font-weight: 600;
        width: 60px;
        background: white;
    }
    
    .quantity-input:focus {
        outline: none;
    }
    
    .action-buttons {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }
    
    .btn-primary {
        background: #d4af37;
        border: none;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-primary:hover {
        background: #b8941f;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(212, 175, 55, 0.3);
    }
    
    .btn-secondary {
        background: transparent;
        border: 2px solid #d4af37;
        color: #d4af37;
        padding: 0.75rem 1.5rem;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-secondary:hover {
        background: #d4af37;
        color: white;
        transform: translateY(-1px);
    }
    
    .reviews-section {
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e9ecef;
    }
    
    .reviews-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #2c2c2c;
        margin-bottom: 1rem;
    }
    
    .review-item {
        background: white;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    
    .review-author {
        font-weight: 600;
        color: #2c2c2c;
    }
    
    .review-rating {
        color: #d4af37;
        font-size: 0.9rem;
    }
    
    .review-comment {
        color: #666;
        line-height: 1.5;
    }
    
    .no-reviews {
        text-align: center;
        color: #666;
        font-style: italic;
        padding: 2rem;
    }
    
    /* SKU Image Selection Styles */
    .sku-image-selection {
        margin-bottom: 1.5rem;
    }
    
    .sku-image-options {
        display: flex;
        gap: 0.75rem;
        overflow-x: auto;
        padding: 0.5rem 0;
        flex-wrap: wrap;
    }
    
    .sku-image-option {
        display: flex;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        border-radius: 8px;
        padding: 0.5rem;
        min-width: 80px;
        flex-shrink: 0;
    }
    
    .sku-image-option:hover {
        border-color: #d4af37;
        background: #fffbf0;
    }
    
    .sku-image-option.selected {
        border-color: #d4af37;
        background: #fffbf0;
        box-shadow: 0 2px 8px rgba(212, 175, 55, 0.3);
    }
    
    .sku-image-option.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .sku-image-container {
        position: relative;
        width: 60px;
        height: 60px;
        border-radius: 6px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }
    
    .sku-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .out-of-stock-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 600;
        text-align: center;
        line-height: 1.2;
    }
    
    .sku-image-label {
        font-size: 0.75rem;
        font-weight: 500;
        color: #666;
        text-align: center;
        word-break: break-all;
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
                                    <img src="{{ $image }}" alt="Thumbnail {{ $index + 1 }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Product Content Section -->
            <div class="content-section">
                <h1 class="product-title">{{ $inventory->name }}</h1>
                
                <div class="price-display" id="currentPrice">
                    RM {{ number_format($data['price'], 2) }}
                    @if(isset($data['has_discount']) && $data['has_discount'] && isset($data['original_price']) && $data['original_price'] > $data['price'])
                        <span style="text-decoration: line-through; color: #999; margin-left: 0.5rem;">
                            RM {{ number_format($data['original_price'], 2) }}
                        </span>
                    @endif
                </div>

                <div class="marketing-description" id="currentMarketingDescription">
                    <p>{{ $data['marketing_description'] }}</p>
                </div>


                <!-- Product Features -->
                <div class="product-features">
                    <div class="features-label">Features:</div>
                    <div class="features-list" id="currentFeatures">
                        @foreach($data['features'] as $feature)
                            <div class="feature-item">{{ $feature }}</div>
                        @endforeach
                    </div>
                </div>

                <!-- SKU Selection with Images -->
                <div class="sku-image-selection">
                    <div class="features-label">Options:</div>
                    <div class="sku-image-options" id="skuImageOptions">
                        @foreach($allVariations as $index => $variation)
                            @php
                                $productData = $variation['productData'];
                                $mainImage = $productData['main_image'] ?? '/images/placeholder.jpg';
                            @endphp
                            <div class="sku-image-option {{ $index === 0 ? 'selected' : '' }} {{ $variation['stock'] <= 0 ? 'disabled' : '' }}"
                                 data-sku="{{ $variation['sku'] }}"
                                 data-product-id="{{ $variation['product']->product->id }}"
                                 data-price="{{ $productData['price'] }}"
                                 data-stock="{{ $variation['stock'] }}"
                                 data-features="{{ json_encode($productData['features']) }}"
                                 data-marketing-description="{{ $productData['marketing_description'] }}"
                                 data-image="{{ $mainImage }}"
                                 data-gallery="{{ json_encode($productData['gallery_images']) }}"
                                 onclick="selectSKUImage(this)">
                                <div class="sku-image-container">
                                    <img src="{{ $mainImage }}" alt="{{ $variation['sku'] }}" class="sku-image">
                                    @if($variation['stock'] <= 0)
                                        <div class="out-of-stock-overlay">Out of Stock</div>
                                    @endif
                                </div>
                                <div class="sku-image-label">{{ $variation['sku'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Quantity Selection -->
                <div class="quantity-selection">
                    <span class="quantity-label">Quantity:</span>
                    <div class="quantity-controls">
                        <button class="quantity-btn" onclick="decreaseQuantity()" id="decreaseBtn">-</button>
                        <input type="number" class="quantity-input" value="1" min="1" max="10" id="quantityInput" onchange="updateQuantity()">
                        <button class="quantity-btn" onclick="increaseQuantity()" id="increaseBtn">+</button>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button class="btn-primary" onclick="addToCart()" id="addToCartBtn">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                    <button class="btn-secondary" onclick="addToWishlist()">
                        <i class="fas fa-heart"></i> Add to Wishlist
                    </button>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        @if($reviews->count() > 0)
            <div class="reviews-section">
                <h3 class="reviews-title">Customer Reviews</h3>
                @foreach($reviews as $review)
                    <div class="review-item">
                        <div class="review-header">
                            <span class="review-author">{{ $review->name }}</span>
                            <span class="review-rating">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        ★
                                    @else
                                        ☆
                                    @endif
                                @endfor
                            </span>
                        </div>
                        <div class="review-comment">{{ $review->comment }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="reviews-section">
                <div class="no-reviews">No reviews yet. Be the first to review this product!</div>
            </div>
        @endif
    </div>
</div>

<script>
let selectedSKU = null;
let currentQuantity = 1;
let maxStock = {{ $allVariations->first()['stock'] }};

// Initialize with default SKU
document.addEventListener('DOMContentLoaded', function() {
    const defaultImageOption = document.querySelector('.sku-image-option.selected');
    
    if (defaultImageOption) {
        selectedSKU = {
            sku: defaultImageOption.dataset.sku,
            productId: defaultImageOption.dataset.productId,
            price: parseFloat(defaultImageOption.dataset.price),
            stock: parseInt(defaultImageOption.dataset.stock),
            features: JSON.parse(defaultImageOption.dataset.features),
            marketingDescription: defaultImageOption.dataset.marketingDescription
        };
        maxStock = selectedSKU.stock;
        updateQuantityControls();
        
        // Update display with initial SKU data
        updatePrice();
        updateFeatures();
        updateMarketingDescription();
    }
});


function selectSKUImage(element) {
    if (element.classList.contains('disabled')) return;
    
    // Remove selected class from all image options
    document.querySelectorAll('.sku-image-option').forEach(option => {
        option.classList.remove('selected');
    });
    
    // Add selected class to clicked option
    element.classList.add('selected');
    
    // Update selected SKU data
    selectedSKU = {
        sku: element.dataset.sku,
        productId: element.dataset.productId,
        price: parseFloat(element.dataset.price),
        stock: parseInt(element.dataset.stock),
        features: JSON.parse(element.dataset.features),
        marketingDescription: element.dataset.marketingDescription
    };
    
    // Update main image
    updateMainImage(element.dataset.image, JSON.parse(element.dataset.gallery));
    
    // Update display
    updatePrice();
    updateFeatures();
    updateMarketingDescription();
    updateStockDisplay();
    maxStock = selectedSKU.stock;
    updateQuantityControls();
}

function updateMainImage(imageSrc, galleryImages) {
    // Update main image
    const mainImage = document.getElementById('mainImage');
    if (mainImage) {
        mainImage.src = imageSrc;
    }
    
    // Update thumbnail gallery
    const thumbnailContainer = document.getElementById('thumbnailContainer');
    if (thumbnailContainer && galleryImages && galleryImages.length > 0) {
        thumbnailContainer.innerHTML = '';
        galleryImages.forEach((image, index) => {
            const thumbnail = document.createElement('div');
            thumbnail.className = `thumbnail ${index === 0 ? 'active' : ''}`;
            thumbnail.onclick = () => changeMainImage(image, thumbnail);
            thumbnail.innerHTML = `<img src="${image}" alt="Thumbnail ${index + 1}">`;
            thumbnailContainer.appendChild(thumbnail);
        });
    }
}


function updatePrice() {
    const priceElement = document.getElementById('currentPrice');
    if (priceElement && selectedSKU) {
        priceElement.innerHTML = `RM ${selectedSKU.price.toFixed(2)}`;
    }
}

function updateFeatures() {
    const featuresElement = document.getElementById('currentFeatures');
    if (featuresElement && selectedSKU) {
        featuresElement.innerHTML = selectedSKU.features.map(feature => 
            `<div class="feature-item">${feature}</div>`
        ).join('');
    }
}

function updateMarketingDescription() {
    const marketingElement = document.getElementById('currentMarketingDescription');
    if (marketingElement && selectedSKU) {
        marketingElement.innerHTML = `<p>${selectedSKU.marketingDescription}</p>`;
    }
}

function updateStockDisplay() {
    const stockElements = document.querySelectorAll('.sku-stock');
    stockElements.forEach(element => {
        const option = element.closest('.sku-option');
        const stock = parseInt(option.dataset.stock);
        if (stock <= 0) {
            element.textContent = 'Out of Stock';
            element.classList.add('out-of-stock');
        } else {
            element.textContent = `In Stock (${stock})`;
            element.classList.remove('out-of-stock');
        }
    });
}

function increaseQuantity() {
    if (currentQuantity < maxStock && currentQuantity < 10) {
        currentQuantity++;
        document.getElementById('quantityInput').value = currentQuantity;
        updateQuantityControls();
    }
}

function decreaseQuantity() {
    if (currentQuantity > 1) {
        currentQuantity--;
        document.getElementById('quantityInput').value = currentQuantity;
        updateQuantityControls();
    }
}

function updateQuantity() {
    const input = document.getElementById('quantityInput');
    const value = parseInt(input.value);
    
    if (value >= 1 && value <= maxStock && value <= 10) {
        currentQuantity = value;
    } else {
        input.value = currentQuantity;
    }
    
    updateQuantityControls();
}

function updateQuantityControls() {
    const decreaseBtn = document.getElementById('decreaseBtn');
    const increaseBtn = document.getElementById('increaseBtn');
    const addToCartBtn = document.getElementById('addToCartBtn');
    
    decreaseBtn.disabled = currentQuantity <= 1;
    increaseBtn.disabled = currentQuantity >= maxStock || currentQuantity >= 10;
    
    if (maxStock <= 0) {
        addToCartBtn.disabled = true;
        addToCartBtn.textContent = 'Out of Stock';
    } else {
        addToCartBtn.disabled = false;
        addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Add to Cart';
    }
}

function changeMainImage(imageSrc, thumbnail) {
    document.getElementById('mainImage').src = imageSrc;
    
    // Update active thumbnail
    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.classList.remove('active');
    });
    thumbnail.classList.add('active');
}

function addToCart() {
    if (!selectedSKU || maxStock <= 0) return;
    
    // TODO: Implement actual cart functionality
    alert(`Added ${currentQuantity} x ${selectedSKU.sku} to cart!`);
}

function addToWishlist() {
    if (!selectedSKU) return;
    
    // TODO: Implement actual wishlist functionality
    alert(`Added ${selectedSKU.sku} to wishlist!`);
}
</script>
@endsection
