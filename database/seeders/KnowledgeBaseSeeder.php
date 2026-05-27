<?php

namespace Database\Seeders;

use App\Modules\Support\Models\KnowledgeDocument;
use App\Modules\Support\Services\RagSearchService;
use Illuminate\Database\Seeder;

class KnowledgeBaseSeeder extends Seeder
{
    public function run(): void
    {
        $documents = [
            [
                'title' => 'Shipping Policy',
                'source_type' => 'policy',
                'content' => "Shipping Policy\n\nStandard delivery within Malaysia usually takes 3 to 7 business days after payment is confirmed. International delivery time depends on destination and courier availability.\n\nCustomers will receive tracking information when the order is shipped. If an order has not arrived after the estimated delivery period, customers should provide their order number so support can check the courier status.",
            ],
            [
                'title' => 'Refund and Return Policy',
                'source_type' => 'policy',
                'content' => "Refund and Return Policy\n\nCustomers may request a return within 7 days after receiving the item if the item is damaged, incorrect, or not as described. The item should be unused and returned with its packaging where possible.\n\nRefunds are reviewed after the returned item is inspected. For custom or personalized jewelry, returns may not be accepted unless there is a defect or fulfilment mistake.",
            ],
            [
                'title' => 'Payment Methods',
                'source_type' => 'policy',
                'content' => "Payment Methods\n\nAccepted payment methods include online bank transfer, card payment, and supported e-wallet options shown during checkout. Orders are processed only after payment is confirmed.\n\nIf payment succeeded but the order still appears unpaid, customers should provide the order number and payment reference for verification.",
            ],
            [
                'title' => 'Jewelry Care Guide',
                'source_type' => 'guide',
                'content' => "Jewelry Care Guide\n\nKeep jewelry away from perfume, lotion, sweat, and harsh chemicals. Store pieces separately in a dry pouch or box to reduce scratches and tarnishing.\n\nClean gently with a soft dry cloth. Avoid wearing jewelry while swimming, showering, exercising, or sleeping.",
            ],
            [
                'title' => 'Warranty and Product Issues',
                'source_type' => 'policy',
                'content' => "Warranty and Product Issues\n\nIf a customer receives a damaged or defective item, they should contact support with the order number, clear photos, and a short description of the issue.\n\nWarranty support depends on the item condition and issue type. Damage caused by misuse, chemicals, impact, or normal wear may not be covered.",
            ],
            [
                'title' => 'Store Contact and Live Chat',
                'source_type' => 'faq',
                'content' => "Store Contact and Live Chat\n\nCustomers can continue sending messages while waiting in the live chat queue. An admin will join when available.\n\nFor order-specific questions, customers should provide the order number, email used for checkout, and a short description of the request.",
            ],
        ];

        foreach ($documents as $document) {
            KnowledgeDocument::updateOrCreate(
                ['title' => $document['title'], 'source_type' => $document['source_type']],
                [
                    'content' => $document['content'],
                    'is_active' => true,
                    'metadata' => ['seeded' => true],
                ]
            );
        }

        if (config('rag.seed.rebuild_chunks')) {
            app(RagSearchService::class)->rebuild();
        }
    }
}
