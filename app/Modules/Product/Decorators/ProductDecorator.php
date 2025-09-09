<?php

namespace App\Modules\Product\Decorators;

interface ProductDecorator
{
    public function getDecoratedData(): array;
    public function getStatus(): string;
    public function getDisplayPrice(): float;
    public function getFormattedDescription(): string;
    public function getCategoryDisplay(): string;
    public function getPublishInfo(): array;
}