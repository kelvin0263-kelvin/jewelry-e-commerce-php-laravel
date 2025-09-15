<?php
/**
 * Author: LEE KAI FONG
 * Date: 2025-09-15
 */
namespace App\Modules\Cart\Strategies;

class FastDelivery implements ShippingStrategy
{
    public function getCost(float $subtotal): float
    {
        // RM5 for fast delivery
        return 5.00;
    }
}

?>