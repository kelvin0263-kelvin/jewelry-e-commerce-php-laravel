<?php

namespace App\Modules\Product\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Decorators\CustomerProductDecorator;
use App\Modules\Product\Decorators\AdminProductDecorator;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Inventory\Models\InventoryVariation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductApiController extends Controller
{
    /**
     * Display a listing of products
     * GET /api/products
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['variation.inventory', 'reviews']);

        // Filter by visibility status
        if ($request->has('visible')) {
            $query->where('is_visible', $request->boolean('visible'));
        }

        // Filter by published status
        if ($request->has('published')) {
            if ($request->boolean('published')) {
                $query->whereNotNull('published_at');
            } else {
                $query->whereNull('published_at');
            }
        }

        // Filter by category
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('marketing_description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('sku', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('selling_price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('selling_price', '<=', $request->max_price);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Ordering
        $orderBy = $request->get('order_by', 'created_at');
        $orderDirection = $request->get('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage);

        // Decorate products based on user type
        $decoratedProducts = $products->getCollection()->map(function($product) {
            if (Auth::check() && Auth::user()->is_admin) {
                return new AdminProductDecorator($product);
            }
            return new CustomerProductDecorator($product);
        });

        $products->setCollection($decoratedProducts);

        return response()->json([
            'data' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ],
            'filters' => [
                'categories' => Product::distinct()->pluck('category')->filter()->values(),
                'statuses' => Product::distinct()->pluck('status')->filter()->values(),
            ]
        ]);
    }

    /**
     * Store a newly created product
     * POST /api/products
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'inventory_variation_id' => 'required|exists:inventory_variations,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'marketing_description' => 'nullable|string',
            'selling_price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'category' => 'required|string|max:100',
            'status' => 'required|in:draft,pending,published,issued',
            'is_visible' => 'boolean',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'string|max:500',
            'main_image' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['created_by'] = Auth::id();
        $data['sku'] = $this->generateSku();

        $product = Product::create($data);

        return response()->json([
            'message' => 'Product created successfully',
            'data' => new AdminProductDecorator($product->load(['variation.inventory', 'reviews']))
        ], 201);
    }

    /**
     * Display the specified product
     * GET /api/products/{id}
     */
    public function show($id): JsonResponse
    {
        $product = Product::with(['variation.inventory', 'reviews', 'publisher', 'issuer'])
            ->find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // Check visibility for non-admin users
        if (!Auth::check() || !Auth::user()->is_admin) {
            if (!$product->is_visible || !$product->published_at) {
                return response()->json(['error' => 'Product not available'], 404);
            }
        }

        $decorator = Auth::check() && Auth::user()->is_admin 
            ? new AdminProductDecorator($product)
            : new CustomerProductDecorator($product);

        return response()->json([
            'data' => $decorator->getDecoratedData()
        ]);
    }

    /**
     * Update the specified product
     * PUT /api/products/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'marketing_description' => 'nullable|string',
            'selling_price' => 'sometimes|required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'category' => 'sometimes|required|string|max:100',
            'status' => 'sometimes|required|in:draft,pending,published,issued',
            'is_visible' => 'boolean',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'string|max:500',
            'main_image' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['updated_by'] = Auth::id();

        // Handle status changes
        if (isset($data['status']) && $data['status'] === 'published' && !$product->published_at) {
            $data['published_at'] = now();
            $data['published_by'] = Auth::id();
        }

        $product->update($data);

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => new AdminProductDecorator($product->load(['variation.inventory', 'reviews']))
        ]);
    }

    /**
     * Remove the specified product
     * DELETE /api/products/{id}
     */
    public function destroy($id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // Soft delete or hard delete based on business logic
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }

    /**
     * Get products by inventory
     * GET /api/products/inventory/{inventoryId}
     */
    public function getByInventory($inventoryId): JsonResponse
    {
        $inventory = Inventory::with(['variations.product'])->find($inventoryId);

        if (!$inventory) {
            return response()->json(['error' => 'Inventory not found'], 404);
        }

        $products = $inventory->variations()
            ->whereHas('product')
            ->with('product')
            ->get()
            ->pluck('product')
            ->map(function($product) {
                return Auth::check() && Auth::user()->is_admin 
                    ? new AdminProductDecorator($product)
                    : new CustomerProductDecorator($product);
            });

        return response()->json([
            'data' => $products,
            'inventory' => [
                'id' => $inventory->id,
                'name' => $inventory->name,
                'type' => $inventory->type,
            ]
        ]);
    }

    /**
     * Get product statistics
     * GET /api/products/stats
     */
    public function getStats(): JsonResponse
    {
        $stats = [
            'total_products' => Product::count(),
            'published_products' => Product::where('is_visible', true)
                ->whereNotNull('published_at')
                ->count(),
            'draft_products' => Product::where('status', 'draft')->count(),
            'pending_products' => Product::where('status', 'pending')->count(),
            'issued_products' => Product::whereIn('status', ['issued', 'rejected'])->count(),
            'categories' => Product::distinct()->pluck('category')->filter()->count(),
            'average_price' => Product::where('is_visible', true)
                ->whereNotNull('published_at')
                ->avg('selling_price'),
        ];

        return response()->json(['data' => $stats]);
    }

    /**
     * Search products
     * GET /api/products/search
     */
    public function search(Request $request): JsonResponse
    {
        $query = Product::with(['variation.inventory', 'reviews']);

        // Only show visible products for non-admin users
        if (!Auth::check() || !Auth::user()->is_admin) {
            $query->where('is_visible', true)->whereNotNull('published_at');
        }

        if ($request->has('q') && $request->q) {
            $searchTerm = $request->q;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('marketing_description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('sku', 'like', '%' . $searchTerm . '%')
                  ->orWhere('category', 'like', '%' . $searchTerm . '%');
            });
        }

        $products = $query->limit(20)->get();

        $decoratedProducts = $products->map(function($product) {
            return Auth::check() && Auth::user()->is_admin 
                ? new AdminProductDecorator($product)
                : new CustomerProductDecorator($product);
        });

        return response()->json([
            'data' => $decoratedProducts,
            'query' => $request->get('q', ''),
            'count' => $products->count()
        ]);
    }

    /**
     * Generate unique SKU
     */
    private function generateSku(): string
    {
        do {
            $sku = 'PROD-' . strtoupper(substr(md5(uniqid()), 0, 8));
        } while (Product::where('sku', $sku)->exists());

        return $sku;
    }
}
