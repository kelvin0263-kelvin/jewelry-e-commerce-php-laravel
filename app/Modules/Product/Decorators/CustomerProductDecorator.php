<?php

namespace App\Modules\Product\Decorators;

use App\Modules\Product\Models\Product;
use Illuminate\Support\Facades\Storage;

class CustomerProductDecorator extends BaseProductDecorator
{
    public function getDecoratedData(): array
    {
        $baseData = parent::getDecoratedData();
        
        return array_merge($baseData, [
            'display_price' => $this->getDisplayPrice(),
            'original_price' => $this->product->price,
            'selling_price' => $this->product->selling_price,
            'discount_price' => $this->product->discount_price,
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
            $path = (string) $this->product->customer_images[0];
            // Normalize stored path (remove leading slashes and optional "public/" prefix)
            $path = ltrim($path, '/\\');
            if (strpos($path, 'public/') === 0) {
                $path = substr($path, 7);
            }

            // Only display if the file exists on the public disk
            if (Storage::disk('public')->exists($path)) {
                // Use a relative asset URL to avoid APP_URL port mismatches
                return asset('storage/' . $path);
            }
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

        $urls = [];
        foreach ((array) $this->product->customer_images as $image) {
            $path = (string) $image;
            $path = ltrim($path, '/\\');
            if (strpos($path, 'public/') === 0) {
                $path = substr($path, 7);
            }

            if (Storage::disk('public')->exists($path)) {
                $urls[] = asset('storage/' . $path);
            } else {
                $urls[] = asset('/img/default-product.jpg');
            }
        }

        return $urls;
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
