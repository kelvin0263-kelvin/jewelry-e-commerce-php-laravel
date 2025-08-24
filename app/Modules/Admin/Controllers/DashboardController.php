<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Order\Models\Order;
use App\Modules\User\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
     public function index()
    {
        // 1. Key Metrics
        $totalRevenue = Order::sum('total_amount');
        $totalSales = Order::count();
        $newCustomersThisMonth = User::where('created_at', '>=', Carbon::now()->startOfMonth())->count();

        // 2. Data for Sales Trend Chart (last 30 days)
        $salesData = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('sum(total_amount) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Format data for Chart.js
        $labels = $salesData->pluck('date')->map(function ($date) {
            return Carbon::parse($date)->format('M d');
        });
        $data = $salesData->pluck('total');

        // Return JSON response for API calls
        if (request()->expectsJson()) {
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
