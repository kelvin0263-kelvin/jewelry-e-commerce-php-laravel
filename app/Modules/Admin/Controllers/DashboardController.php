<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Order\Models\Order;
use App\Modules\User\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
     public function index(Request $request)
    {
        // Defaults
        $totalRevenue = 0;
        $totalSales = 0;
        $totalCustomers = User::where('is_admin', false)->count();
        $newCustomersThisMonth = User::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
        $labels = collect([]);
        $data = collect([]);

        try {
            $useApi = (bool) $request->query('use_api', false);

            if ($useApi) {
                // Prefer Admin stats API for complete metrics, fallback to orders API
                $token = $request->user() ? $request->user()->createToken('dashboard-fetch')->plainTextToken : null;
                $pending = Http::timeout(10);
                if (!empty($token)) {
                    $pending = $pending->withToken($token);
                }

                $statsResponse = $pending->get(url('/api/admin/dashboard/stats'));

                if ($statsResponse->ok()) {
                    $stats = $statsResponse->json('data');
                    $metrics = data_get($stats, 'metrics', []);
                    $totalRevenue = (float) data_get($metrics, 'total_revenue', 0);
                    $totalSales = (int) data_get($metrics, 'total_sales', 0);
                    $totalCustomers = (int) data_get($metrics, 'total_customers', 0);
                    $newCustomersThisMonth = (int) data_get($metrics, 'new_customers_this_month', 0);

                    $trend = collect(data_get($stats, 'revenue_trend', []));
                    // trend items: [ {date: 'YYYY-MM-DD', total: number, orders_count: number}, ... ]
                    $labels = $trend->pluck('date')->map(function ($date) {
                        return Carbon::parse($date)->format('M d');
                    });
                    $data   = $trend->pluck('total');
                } else {
                    // Fallback to Orders API to derive revenue + totals (user-scoped)
                    $ordersResponse = $pending->get(url('/api/orders'));
                    if ($ordersResponse->failed()) {
                        throw new \Exception('Failed to fetch orders from API');
                    }

                    $payload = $ordersResponse->json();
                    $groupedOrders = data_get($payload, 'data.orders', []);
                    $orders = collect($groupedOrders)->reduce(function ($carry, $items) {
                        return $carry->merge($items);
                    }, collect());

                    if ($orders->isEmpty()) {
                        throw new \Exception('No orders returned from API');
                    }

                    $cutoff = Carbon::now()->subDays(30);
                    $recentOrders = $orders->filter(function ($order) use ($cutoff) {
                        $createdAt = isset($order['created_at']) ? Carbon::parse($order['created_at']) : null;
                        return $createdAt && $createdAt->gte($cutoff);
                    })->values();

                    $totalRevenue = $orders->sum(function ($order) { return (float) ($order['total_amount'] ?? 0); });
                    $totalSales = $orders->count();

                    $salesByDate = $recentOrders
                        ->groupBy(function ($order) { return Carbon::parse($order['created_at'])->toDateString(); })
                        ->map(function ($items) { return collect($items)->sum(function ($o) { return (float) ($o['total_amount'] ?? 0); }); })
                        ->sortKeys();

                    $labels = $salesByDate->keys()->map(function ($date) { return Carbon::parse($date)->format('M d'); })->values();
                    $data = $salesByDate->values();
                }
            } else {
                // Internal service (direct DB reads)
                $totalRevenue = Order::sum('total_amount');
                $totalSales = Order::count();
                $totalCustomers = User::where('is_admin', false)->count();

                $salesData = Order::select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('sum(total_amount) as total')
                    )
                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                    ->groupBy('date')
                    ->orderBy('date', 'asc')
                    ->get();

                $labels = $salesData->pluck('date')->map(function ($date) {
                    return Carbon::parse($date)->format('M d');
                });
                $data = $salesData->pluck('total');
            }
        } catch (\Exception $e) {
            // Graceful degradation: keep empty chart data; expose error in JSON mode
        }

        // Return JSON response for API calls
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'metrics' => [
                        'total_revenue' => $totalRevenue,
                        'total_sales' => $totalSales,
                        'new_customers_this_month' => $newCustomersThisMonth
                    ],
                    'sales_chart' => [
                        'labels' => $labels,
                        'data' => $data
                    ]
                ]
            ]);
        }

        return view('admin::dashboard', compact(
            'totalRevenue',
            'totalSales',
            'totalCustomers',
            'newCustomersThisMonth',
            'labels',
            'data'
        ));
    }

    /**
     * API: Get dashboard statistics
     */
    public function getStats(): JsonResponse
    {
        try {
            // Key Metrics
            $totalRevenue = Order::sum('total_amount');
            $totalSales = Order::count();
            $totalCustomers = User::where('is_admin', false)->count();
            $newCustomersThisMonth = User::where('is_admin', false)
                ->where('created_at', '>=', Carbon::now()->startOfMonth())
                ->count();

            // Recent Orders
            $recentOrders = Order::with(['user'])
                ->latest()
                ->limit(5)
                ->get()
                ->map(function($order) {
                    return [
                        'id' => $order->id,
                        'customer_name' => $order->user->name,
                        'total_amount' => $order->total_amount,
                        'status' => $order->status,
                        'created_at' => $order->created_at->format('Y-m-d H:i:s')
                    ];
                });

            // Revenue trend (last 7 days)
            $revenueTrend = Order::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(total_amount) as total'),
                    DB::raw('COUNT(*) as orders_count')
                )
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();

            // Order status distribution
            $orderStatusStats = Order::select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get()
                ->mapWithKeys(function($item) {
                    return [$item['status'] => $item['count']];
                });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'metrics' => [
                        'total_revenue' => $totalRevenue,
                        'total_sales' => $totalSales,
                        'total_customers' => $totalCustomers,
                        'new_customers_this_month' => $newCustomersThisMonth
                    ],
                    'recent_orders' => $recentOrders,
                    'revenue_trend' => $revenueTrend,
                    'order_status_stats' => $orderStatusStats,
                    'generated_at' => now()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve dashboard statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
