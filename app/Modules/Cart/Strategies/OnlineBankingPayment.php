<?php

namespace App\Modules\Cart\Strategies;

class OnlineBankingPayment implements PaymentStrategy
{
    public function pay($amount, $details = [])
    {
        if (
            empty($details['bank_name']) ||
            empty($details['account_name']) ||
            empty($details['account_no'])
        ) {
            return [
                'status' => 'error',
                'message' => 'Bank name, account holder name, and account number are required for online banking.'
            ];
        }

        // Simulate processing payment
        return [
            'status' => 'success',
            'message' => "Paid RM{$amount} via Online Banking ({$details['bank_name']} - {$details['account_name']})."
        ];
    }
}


?>