<?php

namespace App\Modules\Product\Decorators;

use App\Modules\Product\Models\Product;

class AdminProductDecorator extends BaseProductDecorator
{
    public function getDecoratedData(): array
    {
        $baseData = parent::getDecoratedData();
        
        return array_merge($baseData, [
            'sku' => $this->product->sku ?? 'N/A',
            'inventory_id' => $this->product->inventory_id,
            'original_price' => $this->product->price,
            'discount_price' => $this->product->discount_price,
            'has_discount' => !is_null($this->product->discount_price),
            'discount_percentage' => $this->getDiscountPercentage(),
            'image_count' => count($this->product->customer_images ?? []),
            'video_count' => $this->product->product_video ? 1 : 0,
            'images_status' => $this->getImagesStatus(),
            'videos_status' => $this->getVideosStatus(),
            'can_publish' => $this->canPublish(),
            'can_unpublish' => $this->canUnpublish(),
            'can_delete' => $this->canDelete(),
        ]);
    }

    public function getStatus(): string
    {
        if ($this->product->is_visible && $this->product->published_at) {
            return 'published';
        } else {
            return 'pending';
        }
    }

    private function getDiscountPercentage(): ?float
    {
        if (!$this->product->discount_price || !$this->product->price) {
            return null;
        }
        
        return round((($this->product->price - $this->product->discount_price) / $this->product->price) * 100, 1);
    }

    private function canPublish(): bool
    {
        return !$this->product->is_visible || !$this->product->published_at;
    }

    private function canUnpublish(): bool
    {
        return $this->product->is_visible && $this->product->published_at;
    }

    private function canDelete(): bool
    {
        return true; // Admin can always delete products
    }

    private function getImagesStatus(): string
    {
        $imageCount = count($this->product->customer_images ?? []);
        return $imageCount > 0 ? 'Added' : 'None';
    }

    private function getVideosStatus(): string
    {
        return $this->product->product_video ? 'Added' : 'None';
    }
}
