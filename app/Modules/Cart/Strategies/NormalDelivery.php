<?php

namespace App\Modules\Cart\Strategies;

class NormalDelivery implements ShippingStrategy
{
    public function getCost(float $subtotal): float
    {
        // Free if subtotal > RM100, otherwise RM8
        return 2.50;
    }

    public function getEstimate(): string
    {
        return "3-5 business days";
    }
}


?>