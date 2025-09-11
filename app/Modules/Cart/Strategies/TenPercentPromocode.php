<?php

namespace App\Modules\Cart\Strategies;

class TenPercentPromocode implements PromocodeStrategy
{
    public function discount(float $subtotal, ?string $code): float
    {
        if (empty($code)) {
            throw new \Exception("Please enter promo code.");
        }
        if ($code !== 'NewUser') {
            throw new \Exception("Promo code is invalid.");
        }

        return round(0.1 * $subtotal, 2);
    }
}

?>
