<?php
/**
 * Author: TAN CHUN KEAT
 * Date: 2025-09-15
 */
namespace App\Modules\Support\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Modules\User\Models\User;
use App\Modules\Order\Controllers\OrderApiController;
use App\Modules\Inventory\Models\Inventory;

class SelfServiceController extends Controller
{
    public function index()
    {
        $categories = $this->getSelfServiceCategories();
        return view('support::self-service.index', compact('categories'));
    }

    // category view removed; single-page self-service

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

    /**
     * Track an order from the self-service page using order ID only.
     */
    public function trackOrder(Request $request)
    {
        
        $request->validate([
            'order_number' => ['required', 'integer'],
        ]);

        try {
            $orderId = (int) trim($request->input('order_number'));
            $useApi = filter_var($request->query('use_api', false), FILTER_VALIDATE_BOOLEAN);

            if ($useApi) {
                if (!Auth::check()) {
                    throw new \Exception('Unauthorized to access order via API');
                }

                $token = Auth::user()->createToken('self-service-tracking')->plainTextToken;
                $response = Http::withToken($token)
                    ->timeout(10)
                    ->get(url("/api/orders/{$orderId}/tracking"));

                if ($response->failed()) {
                    throw new \Exception('Failed to fetch tracking via API');
                }

                $tracking = $response->json('data');
                return response()->json([
                    'success' => true,
                    'data' => $tracking,
                ]);
            }

            // Internal consumption of the API controller
            $controller = app(OrderApiController::class);
            $apiResponse = $controller->getTracking($orderId);

            if (method_exists($apiResponse, 'getStatusCode') && $apiResponse->getStatusCode() !== 200) {
                $payload = json_decode($apiResponse->getContent(), true);
                $message = $payload['message'] ?? 'Failed to retrieve tracking information';
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], $apiResponse->getStatusCode());
            }

            $payload = json_decode($apiResponse->getContent(), true);
            $tracking = $payload['data'] ?? null;

            return response()->json([
                'success' => true,
                'data' => $tracking,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to track order',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Check product/item availability by inventory ID.
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'inventory_id' => ['nullable', 'integer'],
            'product_name' => ['nullable', 'string', 'max:255']
        ]);

        try {
            $useApi = filter_var($request->query('use_api', false), FILTER_VALIDATE_BOOLEAN);

            $inventoryId = $request->filled('inventory_id') ? (int) $request->input('inventory_id') : null;
            $productName = $request->filled('product_name') ? trim($request->input('product_name')) : null;

            $results = [];

            if ($productName && !$inventoryId) {
                // Search products by name (visible + published)
                $products = \App\Modules\Product\Models\Product::where('is_visible', true)
                    ->whereNotNull('published_at')
                    ->where('name', 'like', '%' . str_replace('%', '', $productName) . '%')
                    ->limit(5)
                    ->get();

                if ($products->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No matching products found'
                    ], 404);
                }

                foreach ($products as $product) {
                    // Resolve related inventory id
                    $relatedInventoryId = $product->inventory_id;
                    if (!$relatedInventoryId && $product->variation) {
                        $relatedInventoryId = $product->variation->inventory_id;
                    }

                    if (!$relatedInventoryId) {
                        $results[] = [
                            'product_id' => $product->id,
                            'name' => $product->name,
                            'published' => (bool) $product->published_at,
                            'available' => false,
                            'total_stock' => 0,
                            'url' => null,
                        ];
                        continue;
                    }

                    if ($useApi) {
                        // External API published check for each match
                        $response = Http::timeout(10)
                            ->get(url("/api/products/check-published/{$relatedInventoryId}"));
                        $published = $response->ok() ? (bool) ($response->json('published') ?? false) : false;

                        $inventory = Inventory::find($relatedInventoryId);
                        $totalStock = (int) ($inventory->total_stock ?? 0);

                        // Resolve product URL using inventory -> first published product
                        $firstPublishedProductId = null;
                        if ($published && $inventory) {
                            $firstPublishedProductId = optional(
                                $inventory->variations()
                                    ->whereHas('product', function ($query) {
                                        $query->where('is_visible', true)
                                              ->whereNotNull('published_at');
                                    })
                                    ->with('product')
                                    ->first()
                            )->product->id ?? null;
                        }

                        $results[] = [
                            'product_id' => $product->id,
                            'name' => $product->name,
                            'inventory_id' => $relatedInventoryId,
                            'published' => $published,
                            'available' => $published && $totalStock > 0,
                            'total_stock' => $totalStock,
                            'url' => ($published && $firstPublishedProductId) ? route('products.show', $firstPublishedProductId) : null,
                        ];
                    } else {
                        // Internal availability using inventory stock and published variations
                        $inventory = Inventory::with(['variations.product'])->find($relatedInventoryId);
                        $publishedVariations = $inventory ? $inventory->variations()
                            ->whereHas('product', function ($query) {
                                $query->where('is_visible', true)
                                      ->whereNotNull('published_at');
                            })
                            ->exists() : false;

                        $totalStock = (int) ($inventory->total_stock ?? 0);
                        $available = $publishedVariations && $totalStock > 0;

                        // Resolve first published product for this inventory to build correct URL
                        $firstPublishedProductId = null;
                        if ($publishedVariations && $inventory) {
                            $firstPublishedProductId = optional(
                                $inventory->variations()
                                    ->whereHas('product', function ($query) {
                                        $query->where('is_visible', true)
                                              ->whereNotNull('published_at');
                                    })
                                    ->with('product')
                                    ->first()
                            )->product->id ?? null;
                        }

                        $results[] = [
                            'product_id' => $product->id,
                            'name' => $product->name,
                            'inventory_id' => $relatedInventoryId,
                            'published' => $publishedVariations,
                            'available' => $available,
                            'total_stock' => $totalStock,
                            'url' => $firstPublishedProductId ? route('products.show', $firstPublishedProductId) : null,
                        ];
                    }
                }

                return response()->json([
                    'success' => true,
                    'data' => $results,
                ]);
            }

            // Direct inventory check path (kept for flexibility)
            if ($inventoryId) {
                if ($useApi) {
                    $response = Http::timeout(10)
                        ->get(url("/api/products/check-published/{$inventoryId}"));

                    if ($response->failed()) {
                        throw new \Exception('Failed to check availability via API');
                    }

                    $published = (bool) ($response->json('published') ?? false);
                    $url = $response->json('url');

                    // Fallback: resolve product ID from inventory when API URL is missing
                    $firstPublishedProductId = null;
                    $totalStock = null;
                    if ($published) {
                        $inventory = Inventory::with(['variations.product'])->find($inventoryId);
                        if ($inventory) {
                            $totalStock = (int) ($inventory->total_stock ?? 0);
                            $firstPublishedProductId = optional(
                                $inventory->variations()
                                    ->whereHas('product', function ($query) {
                                        $query->where('is_visible', true)
                                              ->whereNotNull('published_at');
                                    })
                                    ->with('product')
                                    ->first()
                            )->product->id ?? null;
                            if (!$url && $firstPublishedProductId) {
                                $url = route('products.show', $firstPublishedProductId);
                            }
                        }
                    }

                    return response()->json([
                        'success' => true,
                        'data' => [[
                            'inventory_id' => $inventoryId,
                            'published' => $published,
                            'available' => $published,
                            'total_stock' => $totalStock,
                            'product_id' => $firstPublishedProductId,
                            'url' => $url,
                        ]],
                    ]);
                }

                $inventory = Inventory::with(['variations.product'])->findOrFail($inventoryId);
                $publishedVariations = $inventory->variations()
                    ->whereHas('product', function ($query) {
                        $query->where('is_visible', true)
                              ->whereNotNull('published_at');
                    })
                    ->exists();

                $totalStock = (int) ($inventory->total_stock ?? 0);
                $available = $publishedVariations && $totalStock > 0;

                // Resolve first published product for this inventory to build correct URL
                $firstPublishedProductId = null;
                if ($publishedVariations) {
                    $firstPublishedProductId = optional(
                        $inventory->variations()
                            ->whereHas('product', function ($query) {
                                $query->where('is_visible', true)
                                      ->whereNotNull('published_at');
                            })
                            ->with('product')
                            ->first()
                    )->product->id ?? null;
                }

                return response()->json([
                    'success' => true,
                    'data' => [[
                        'inventory_id' => $inventoryId,
                        'published' => $publishedVariations,
                        'available' => $available,
                        'total_stock' => $totalStock,
                        'product_id' => $firstPublishedProductId,
                        'url' => $firstPublishedProductId ? route('products.show', $firstPublishedProductId) : null,
                    ]],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Please provide a product name',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to check item availability',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    private function getSelfServiceCategories()
    {
        return [
            'orders' => [
                'title' => 'Orders & Payments',
                'icon' => '🛒',
                'description' => 'Track orders, refunds, and payment issues'
            ],
            'availability' => [
                'title' => 'Product Availability',
                'icon' => '📦',
                'description' => 'Check if an item is published and in stock'
            ]
        ];
    }

    private function getSolutionForIssue($issue)
    {
        $solutions = [
            'track_order' => 'Check your email for tracking info or log into your account.',
            'payment_issue' => 'Verify card details or try a different payment method.',
            // 'refund_status' removed per new requirements
            'reset_password' => 'Check spam folder. Reset links expire in 60 minutes.'
        ];

        return $solutions[$issue] ?? 'Our support team can help with your specific situation.';
    }
}
