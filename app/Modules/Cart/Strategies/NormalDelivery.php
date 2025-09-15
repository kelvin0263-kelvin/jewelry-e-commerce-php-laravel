<?php
/**
 * Author: LEE KAI FONG
 * Date: 2025-09-15
 */
namespace App\Modules\Cart\Strategies;

class NormalDelivery implements ShippingStrategy
{
    public function getCost(float $subtotal): float
    {
        // Free if subtotal > RM100, otherwise RM8
        return 2.50;
    }

}


?>