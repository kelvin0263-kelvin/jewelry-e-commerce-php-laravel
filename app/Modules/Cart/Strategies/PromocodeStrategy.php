<?php

namespace App\Modules\Cart\Strategies;

interface PromocodeStrategy
{
    /**
     * Apply discount based on subtotal and code
     */
    public function discount(float $subtotal, ?string $code): float;
}

?>