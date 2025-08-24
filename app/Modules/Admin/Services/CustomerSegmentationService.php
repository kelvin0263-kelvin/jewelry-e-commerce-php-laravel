<?php
// In app/Services/CustomerSegmentationService.php

namespace App\Modules\Admin\Services;

use App\Modules\User\Models\User;
use Illuminate\Support\Facades\Http; // Import Laravel's HTTP Client
use Illuminate\Support\Facades\Log; // Import Log for debugging
use Illuminate\Support\Carbon;

class CustomerSegmentationService
{
    protected $apiKey;

    public function __construct()
    {
        // Get the API key from the .env file
        $this->apiKey = env('GEMINI_API_KEY');

    }

    public function generateSegments()
    {
        $customers = User::has('orders')->with([
            'orders' => function ($query) {
                $query->select('user_id', 'total_amount', 'created_at');
            }
        ])->get();

        return $customers->map(function ($customer) {
            $orders = $customer->orders;

            $lastOrderDate = $orders->max('created_at');
            $recency = $lastOrderDate ? Carbon::now()->diffInDays($lastOrderDate) : 9999;
            $frequency = $orders->count();
            $monetary = $orders->sum('total_amount');

            // NEW: Get segment from the AI model
            $segment = $this->getSegmentFromAI($recency, $frequency, $monetary);

            return [
                'name' => $customer->name,
                'email' => $customer->email,
                'recency' => $recency,
                'frequency' => $frequency,
                'monetary' => number_format($monetary, 2),
                'segment' => $segment,
            ];
        });
    }

    // This is our new AI-powered method
    protected function getSegmentFromAI($r, $f, $m)
    {
        // If no API key is set, fall back to the old rule-based method
        if (empty($this->apiKey)) {
            Log::warning('Gemini API key is not set. Falling back to rule-based segmentation.');
            return $this->getRuleBasedSegment($r, $f, $m);
        }

        $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $this->apiKey;

        // The "prompt" is our instruction to the AI
        $prompt = "You are an expert marketing analyst. A customer has the following RFM data: Recency (days since last purchase) = {$r}, Frequency (total purchases) = {$f}, Monetary (total spent) = RM {$m}. Based on these values, classify them into one of the following segments: '🏆 Champion', '👥 Loyal Customer', '🌱 Promising', '✨ New Customer', '😟 At Risk', '👻 Lost', or 'Regular'. Return only the name of the segment and nothing else.";

        try {
            $response = Http::post($apiUrl, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ]);

            if ($response->successful()) {
                // Extract the text from the AI's response
                $result = $response->json('candidates.0.content.parts.0.text');
                return trim($result);
            } else {
                // If the API call fails, log the error and fall back
                Log::error('Gemini API call failed: ' . $response->body());
                return $this->getRuleBasedSegment($r, $f, $m);
            }
        } catch (\Exception $e) {
            // If there's a connection error, log it and fall back
            Log::error('Exception during Gemini API call: ' . $e->getMessage());
            return $this->getRuleBasedSegment($r, $f, $m);
        }
    }

    // This is our old method, now used as a fallback
    protected function getRuleBasedSegment($r, $f, $m)
    {
        if ($r <= 30 && $f >= 5 && $m >= 1000)
            return '🏆 Champion';
        if ($r <= 60 && $f >= 3)
            return '👥 Loyal Customer';
        if ($r <= 90 && $f >= 1)
            return '🌱 Promising';
        if ($r <= 30 && $f == 1)
            return '✨ New Customer';
        if ($r > 90 && $r <= 180)
            return '😟 At Risk';
        if ($r > 180)
            return '👻 Lost';
        return 'Regular';
    }
}