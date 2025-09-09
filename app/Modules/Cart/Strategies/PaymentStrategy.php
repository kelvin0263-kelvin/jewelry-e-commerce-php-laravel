<?php

namespace App\Modules\Cart\Strategies;

interface PaymentStrategy
{
    public function pay(float $amount, array $details);
}



?>