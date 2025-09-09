<?php

namespace App\Modules\Product\Decorators;

use App\Modules\Product\Models\Product;

class CustomerProductDecorator extends BaseProductDecorator
{
    public function getDecoratedData(): array
    {
        $baseData = parent::getDecoratedData();
        
        return array_merge($baseData, [
            'display_price' => $this->getDisplayPrice(),
            'original_price' => $this->product->price,
            'has_discount' => !is_null($this->product->discount_price),
            'discount_percentage' => $this->getDiscountPercentage(),
            'main_image' => $this->getMainImage(),
            'gallery_images' => $this->getGalleryImages(),
            'marketing_description' => $this->product->marketing_description ?? '',
            'rating' => $this->getRating(),
            'review_count' => $this->getReviewCount(),
            'is_in_wishlist' => $this->isInWishlist(),
            'is_in_cart' => $this->isInCart(),
        ]);
    }

    private function getDiscountPercentage(): ?float
    {
        if (!$this->product->discount_price || !$this->product->price) {
            return null;
        }
        
        return round((($this->product->price - $this->product->discount_price) / $this->product->price) * 100, 1);
    }

    private function getMainImage(): string
    {
        if ($this->product->customer_images && count($this->product->customer_images) > 0) {
            return asset('storage/' . $this->product->customer_images[0]);
        }
        
        return asset('/img/default-product.jpg');
    }

    private function getGalleryImages(): array
    {
        if (!$this->product->customer_images || count($this->product->customer_images) === 0) {
            // Return default jewelry images for demo
            return [
                asset('/img/default-product.jpg'),
                asset('/img/default-product.jpg'),
                asset('/img/default-product.jpg')
            ];
        }
        
        return array_map(function($image) {
            return asset('storage/' . $image);
        }, $this->product->customer_images);
    }

    private function getRating(): float
    {
        // TODO: Implement rating system
        return 5.0; // Default rating for demo
    }

    private function getReviewCount(): int
    {
        // TODO: Implement review system
        return 1; // Default review count for demo
    }

    private function isInWishlist(): bool
    {
        // TODO: Implement wishlist functionality
        return false;
    }

    private function isInCart(): bool
    {
        // TODO: Implement cart functionality
        return false;
    }
}
