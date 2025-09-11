<?php

namespace App\Modules\Cart\Strategies;

class CreditCardPayment implements PaymentStrategy
{
    public function pay($amount, $details = [])
    {
        if (
            empty($details['card_number']) ||
            empty($details['name_on_card']) ||
            empty($details['expiry_date']) ||
            empty($details['cvv'])
        ) {
            return [
                'status'  => 'error',
                'message' => 'Credit card details are required (number, expiry, CVV, name).'
            ];
        }

        return [
            'status'  => 'success',
            'message' => "Paid RM{$amount} via Credit Card ending with " . substr($details['card_number'], -4)
        ];
    }
}

?>