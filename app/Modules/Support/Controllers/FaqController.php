<?php

namespace App\Modules\Support\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display the FAQ page
     */
    public function index()
    {
        $faqs = $this->getFaqData();
        return view('support::faq.index', compact('faqs'));
    }

    /**
     * Get FAQ data organized by categories
     */
    private function getFaqData()
    {
        return [
            'ordering' => [
                'title' => 'Ordering & Payment',
                'questions' => [
                    [
                        'question' => 'How do I place an order?',
                        'answer' => 'To place an order, simply browse our jewelry collection, select the items you want, add them to your cart, and proceed to checkout. You\'ll need to create an account or log in to complete your purchase.'
                    ],
                    [
                        'question' => 'What payment methods do you accept?',
                        'answer' => 'We accept all major credit cards (Visa, MasterCard, American Express), PayPal, and bank transfers. All payments are processed securely through our encrypted checkout system.'
                    ],
                    [
                        'question' => 'Can I modify or cancel my order?',
                        'answer' => 'Orders can be modified or cancelled within 24 hours of placement. Please contact our customer service team immediately if you need to make changes to your order.'
                    ],
                    [
                        'question' => 'Do you offer payment plans or financing?',
                        'answer' => 'Yes, we offer flexible payment plans for purchases over $500. You can choose from 3, 6, or 12-month payment options with 0% interest. Contact us for more details.'
                    ]
                ]
            ],
            'shipping' => [
                'title' => 'Shipping & Delivery',
                'questions' => [
                    [
                        'question' => 'How long does shipping take?',
                        'answer' => 'Standard shipping takes 3-5 business days, while express shipping takes 1-2 business days. International orders typically take 7-14 business days depending on the destination.'
                    ],
                    [
                        'question' => 'Do you ship internationally?',
                        'answer' => 'Yes, we ship to most countries worldwide. Shipping costs and delivery times vary by location. International customers are responsible for any customs duties or taxes.'
                    ],
                    [
                        'question' => 'How much does shipping cost?',
                        'answer' => 'We offer free standard shipping on orders over $100. For orders under $100, standard shipping is $9.99. Express shipping is $19.99 regardless of order value.'
                    ],
                    [
                        'question' => 'How can I track my order?',
                        'answer' => 'Once your order ships, you\'ll receive a tracking number via email. You can track your package using this number on our website or the carrier\'s website.'
                    ]
                ]
            ],
            'products' => [
                'title' => 'Products & Quality',
                'questions' => [
                    [
                        'question' => 'Are your jewelry pieces authentic?',
                        'answer' => 'Yes, all our jewelry is 100% authentic. We provide certificates of authenticity for precious metals and gemstones. Each piece is carefully inspected before shipping.'
                    ],
                    [
                        'question' => 'What materials do you use?',
                        'answer' => 'We use only high-quality materials including 14k and 18k gold, sterling silver, platinum, and genuine gemstones. All our pieces are nickel-free and hypoallergenic.'
                    ],
                    [
                        'question' => 'Do you offer custom jewelry?',
                        'answer' => 'Yes, we offer custom jewelry design services. Contact our design team to discuss your vision, and we\'ll create a unique piece just for you. Custom orders typically take 4-6 weeks.'
                    ],
                    [
                        'question' => 'How should I care for my jewelry?',
                        'answer' => 'Store jewelry in a dry place, clean regularly with appropriate cleaners, avoid exposure to chemicals and perfumes, and have pieces professionally cleaned annually.'
                    ]
                ]
            ],
            'returns' => [
                'title' => 'Returns & Exchanges',
                'questions' => [
                    [
                        'question' => 'What is your return policy?',
                        'answer' => 'We offer a 30-day return policy for unworn items in original condition. Items must be returned with original packaging and receipt. Custom pieces are non-returnable.'
                    ],
                    [
                        'question' => 'How do I return an item?',
                        'answer' => 'Contact our customer service to initiate a return. We\'ll provide a prepaid return label. Package the item securely and send it back to us. Refunds are processed within 5-7 business days.'
                    ],
                    [
                        'question' => 'Can I exchange for a different size?',
                        'answer' => 'Yes, we offer free size exchanges within 30 days of purchase. The item must be unworn and in original condition. We\'ll send the new size once we receive the original item.'
                    ],
                    [
                        'question' => 'What if I receive a damaged item?',
                        'answer' => 'If you receive a damaged item, contact us immediately with photos. We\'ll arrange for a replacement or full refund at no cost to you. Customer satisfaction is our priority.'
                    ]
                ]
            ],
            'account' => [
                'title' => 'Account & Support',
                'questions' => [
                    [
                        'question' => 'How do I create an account?',
                        'answer' => 'Click the "Register" button in the top navigation, fill out the required information, and verify your email address. Having an account allows you to track orders and save favorites.'
                    ],
                    [
                        'question' => 'I forgot my password. What should I do?',
                        'answer' => 'Click "Forgot Password" on the login page, enter your email address, and we\'ll send you a password reset link. Follow the instructions in the email to create a new password.'
                    ],
                    [
                        'question' => 'How can I contact customer service?',
                        'answer' => 'You can reach us through our live chat feature, email us at support@jewelrystore.com, or call us at 1-800-JEWELRY. Our team is available Monday-Friday 9AM-6PM EST.'
                    ],
                    [
                        'question' => 'Do you have a warranty on your jewelry?',
                        'answer' => 'Yes, all our jewelry comes with a 1-year warranty against manufacturing defects. This covers issues like loose stones or broken clasps, but not damage from normal wear or accidents.'
                    ]
                ]
            ]
        ];
    }
}