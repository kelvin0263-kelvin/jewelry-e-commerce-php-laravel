<?php

namespace App\Modules\Support\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Modules\Order\Models\Order;
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
     * Track an order from the self-service page.
     *
     * Uses the existing Order API controller. If the request includes
     * a `use_api` query param (truthy), it will attempt to consume the
     * REST endpoint via the internal HTTP client with auth; otherwise it
     * falls back to internal service consumption to avoid adding anything
     * to the Order module.
     */
    public function trackOrder(Request $request)
    {
        $request->validate([
            'order_number' => ['required', 'string'],
            'email' => ['required', 'email'],
        ]);

        $tracking = null;
        $error = null;

        try {
            $orderIdentifier = trim($request->input('order_number'));
            $email = trim($request->input('email'));

            // Resolve order by numeric id or by tracking_number
            $query = Order::query();
            if (ctype_digit($orderIdentifier)) {
                $query->where('id', (int) $orderIdentifier);
            } else {
                $query->where('tracking_number', $orderIdentifier);
            }

            // Ensure the order belongs to the provided email
            $order = $query->whereHas('user', function ($q) use ($email) {
                $q->where('email', $email);
            })->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found for the provided details.'
                ], 404);
            }

            $useApi = filter_var($request->query('use_api', false), FILTER_VALIDATE_BOOLEAN);

            if ($useApi) {
                // Try consuming the existing REST API endpoint with Sanctum token of the current web user
                if (!Auth::check() || Auth::id() !== (int) $order->user_id) {
                    // For security, only allow API consumption when the current user owns the order
                    throw new \Exception('Unauthorized to access order via API');
                }

                // Create a short-lived token and call the API
                $token = Auth::user()->createToken('self-service-tracking')->plainTextToken;
                $response = Http::withToken($token)
                    ->timeout(10)
                    ->get(url("/api/orders/{$order->id}/tracking"));

                if ($response->failed()) {
                    throw new \Exception('Failed to fetch tracking via API');
                }

                $tracking = $response->json('data');
            } else {
                // Internal service consumption (no API change to Order module)
                // Reuse existing OrderApiController logic to keep response parity
                $controller = app(OrderApiController::class);

                // Implicitly uses current Auth user; temporarily set acting user if matches
                if (Auth::check() && Auth::id() === (int) $order->user_id) {
                    $apiResponse = $controller->getTracking($order->id);
                    if (method_exists($apiResponse, 'getStatusCode') && $apiResponse->getStatusCode() !== 200) {
                        throw new \Exception('Failed to retrieve tracking information');
                    }
                    $payload = json_decode($apiResponse->getContent(), true);
                    $tracking = $payload['data'] ?? null;
                } else {
                    // If not authenticated as the order owner, derive minimal tracking details directly
                    $tracking = [
                        'order_id' => $order->id,
                        'status' => $order->status,
                        'tracking_number' => $order->tracking_number,
                        'shipping_method' => $order->shipping_method,
                        'shipping_address' => $order->shipping_address,
                        'shipping_postal_code' => $order->shipping_postal_code,
                        'created_at' => $order->created_at,
                        'updated_at' => $order->updated_at,
                    ];
                }
            }

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

    // refundStatus removed per new requirements

    /**
     * Check product/item availability by inventory ID.
     * If ?use_api=1, calls external published check; otherwise uses internal models.
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

                        $results[] = [
                            'product_id' => $product->id,
                            'name' => $product->name,
                            'inventory_id' => $relatedInventoryId,
                            'published' => $published,
                            'available' => $published && $totalStock > 0,
                            'total_stock' => $totalStock,
                            'url' => $published ? route('products.show', $relatedInventoryId) : null,
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

                        $results[] = [
                            'product_id' => $product->id,
                            'name' => $product->name,
                            'inventory_id' => $relatedInventoryId,
                            'published' => $publishedVariations,
                            'available' => $available,
                            'total_stock' => $totalStock,
                            'url' => $publishedVariations ? route('products.show', $relatedInventoryId) : null,
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

                    return response()->json([
                        'success' => true,
                        'data' => [[
                            'inventory_id' => $inventoryId,
                            'published' => $published,
                            'available' => $published,
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

                return response()->json([
                    'success' => true,
                    'data' => [[
                        'inventory_id' => $inventoryId,
                        'published' => $publishedVariations,
                        'available' => $available,
                        'total_stock' => $totalStock,
                        'url' => $publishedVariations ? route('products.show', $inventoryId) : null,
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
