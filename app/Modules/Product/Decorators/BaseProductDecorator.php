<?php

namespace App\Modules\Product\Decorators;

use App\Modules\Product\Models\Product;

abstract class BaseProductDecorator implements ProductDecorator
{
    public $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function getDecoratedData(): array
    {
        return [
            'id' => $this->product->product_id ?? $this->product->id,
            'name' => $this->product->name,
            'description' => $this->getFormattedDescription(),
            'price' => $this->getDisplayPrice(),
            'category' => $this->getCategoryDisplay(),
            'status' => $this->getStatus(),
            'publish_info' => $this->getPublishInfo(),
            'images' => $this->product->customer_images ?? [],
            'features' => $this->product->features ?? [],
        ];
    }

    public function getStatus(): string
    {
        if ($this->product->is_visible && $this->product->published_at) {
            return 'published';
        } else {
            return 'pending';
        }
    }

    public function getDisplayPrice(): float
    {
        return $this->product->discount_price ?? $this->product->price ?? 0;
    }

    public function getFormattedDescription(): string
    {
        return $this->product->marketing_description ?? $this->product->description ?? '';
    }

    public function getCategoryDisplay(): string
    {
        return $this->product->category ?? 'Uncategorized';
    }

    public function getPublishInfo(): array
    {
        return [
            'published_by' => $this->product->publisher->email ?? 'Not published',
            'published_at' => $this->product->published_at ? $this->product->published_at->format('M d, Y') : 'Not published',
        ];
    }
}
