<?php
/**
 * Author: LEE KAI FONG
 * Date: 2025-09-15
 */
namespace App\Modules\Cart\Strategies;

interface ShippingStrategy
{
    public function getCost(float $subtotal): float;
}

?>