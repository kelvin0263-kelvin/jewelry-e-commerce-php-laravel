<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SelfServiceController extends Controller
{
    public function index()
    {
        $categories = $this->getSelfServiceCategories();
        return view('self-service.index', compact('categories'));
    }

    public function category($categorySlug)
    {
        $categories = $this->getSelfServiceCategories();
        
        if (!isset($categories[$categorySlug])) {
            abort(404);
        }

        $category = $categories[$categorySlug];
        return view('self-service.category', compact('category', 'categorySlug'));
    }

    public function help(Request $request)
    {
        $issue = $request->input('issue');
        $solution = $this->getSolutionForIssue($issue);
        
        return response()->json([
            'success' => true,
            'solution' => $solution,
            'escalate_available' => true
        ]);
    }

    public function escalate(Request $request)
    {
        $context = [
            'issue_category' => $request->input('category'),
            'attempted_issue' => $request->input('issue'),
            'self_service_attempted' => true
        ];

        session(['chat_escalation_context' => $context]);

        return response()->json([
            'success' => true,
            'message' => 'Escalating to live chat...',
            'redirect' => route('chat.start')
        ]);
    }

    private function getSelfServiceCategories()
    {
        return [
            'orders' => [
                'title' => 'Orders & Payments',
                'icon' => '🛒',
                'description' => 'Track orders, payment issues, cancellations'
            ],
            'products' => [
                'title' => 'Products & Quality', 
                'icon' => '💎',
                'description' => 'Product information, authenticity, care'
            ],
            'returns' => [
                'title' => 'Returns & Exchanges',
                'icon' => '↩️', 
                'description' => 'Return items, exchanges, refunds'
            ],
            'account' => [
                'title' => 'Account & Profile',
                'icon' => '👤',
                'description' => 'Account settings, passwords, profile'
            ]
        ];
    }

    private function getSolutionForIssue($issue)
    {
        $solutions = [
            'track_order' => 'Check your email for tracking info or log into your account.',
            'payment_issue' => 'Verify card details or try a different payment method.',
            'start_return' => 'Returns must be unworn in original packaging within 30 days.',
            'reset_password' => 'Check spam folder. Reset links expire in 60 minutes.'
        ];

        return $solutions[$issue] ?? 'Our support team can help with your specific situation.';
    }
}