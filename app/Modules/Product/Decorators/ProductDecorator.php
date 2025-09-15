<?php
/**
 * Author: SIA XIAO HUI
 * Date: 2025-09-15
 */

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