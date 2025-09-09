<?php

namespace App\Modules\Cart\Strategies;

class NoPromocode implements PromocodeStrategy
{
    public function discount(float $subtotal, ?string $code): float
    {
        if (!empty($code)) {
            throw new \Exception("You selected 'No Promo' but entered a code.");
        }
        return 0;
    }
}


?>