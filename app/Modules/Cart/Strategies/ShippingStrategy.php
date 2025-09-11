<?php

namespace App\Modules\Cart\Strategies;

interface ShippingStrategy
{
    public function getCost(float $subtotal): float;
    public function getEstimate(): string;
}

?>