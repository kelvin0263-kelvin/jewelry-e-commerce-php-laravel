<?php
// app/Modules/Admin/Controllers/Api/ReportController.php
namespace App\Modules\Admin\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Services\CustomerSegmentationService;
use App\Modules\User\Models\User;
use App\Modules\Order\Models\Order;
use App\Modules\Product\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Get customer segmentation data
     */
    public function customerSegments(CustomerSegmentationService $segmentationService): JsonResponse
    {
        try {
            $segments = $segmentationService->segmentCustomersByBehavior();
            
            return response()->json([
                'status' => 'success',
                'data' => $segments,
                'message' => 'Customer segments retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve customer segments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sales report
     */
    public function salesReport(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', Carbon::now()->subMonth());
            $endDate = $request->get('end_date', Carbon::now());
            
            $salesData = Order::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE(created_at) as date, COUNT(*) as orders_count, SUM(total_amount) as total_sales')
                ->groupBy('date')
                ->orderBy('date')
                ->get();
            
            $totalSales = $salesData->sum('total_sales');
            $totalOrders = $salesData->sum('orders_count');
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'sales_by_date' => $salesData,
                    'summary' => [
                        'total_sales' => $totalSales,
                        'total_orders' => $totalOrders,
                        'average_order_value' => $totalOrders > 0 ? $totalSales / $totalOrders : 0
                    ]
                ],
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve sales report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product performance report
     */
    public function productPerformance(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', Carbon::now()->subMonth());
            $endDate = $request->get('end_date', Carbon::now());
            
            $productData = Product::leftJoin('order_product', 'products.id', '=', 'order_product.product_id')
                ->leftJoin('orders', 'order_product.order_id', '=', 'orders.id')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->select(
                    'products.id',
                    'products.name',
                    'products.price',
                    \DB::raw('COALESCE(SUM(order_product.quantity), 0) as total_sold'),
                    \DB::raw('COALESCE(SUM(order_product.quantity * order_product.price), 0) as total_revenue')
                )
                ->groupBy('products.id', 'products.name', 'products.price')
                ->orderBy('total_revenue', 'desc')
                ->limit(50)
                ->get();
            
            return response()->json([
                'status' => 'success',
                'data' => $productData,
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve product performance report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order analytics
     */
    public function orderAnalytics(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', Carbon::now()->subMonth());
            $endDate = $request->get('end_date', Carbon::now());
            
            $orderStats = [
                'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
                'pending_orders' => Order::where('status', 'pending')->whereBetween('created_at', [$startDate, $endDate])->count(),
                'completed_orders' => Order::where('status', 'completed')->whereBetween('created_at', [$startDate, $endDate])->count(),
                'cancelled_orders' => Order::where('status', 'cancelled')->whereBetween('created_at', [$startDate, $endDate])->count(),
            ];
            
            $ordersByStatus = Order::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get();
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'statistics' => $orderStats,
                    'orders_by_status' => $ordersByStatus
                ],
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve order analytics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get revenue report
     */
    public function revenueReport(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', Carbon::now()->subMonth());
            $endDate = $request->get('end_date', Carbon::now());
            
            $revenueData = Order::where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('
                    DATE(created_at) as date,
                    SUM(total_amount) as daily_revenue,
                    COUNT(*) as orders_count
                ')
                ->groupBy('date')
                ->orderBy('date')
                ->get();
            
            $totalRevenue = $revenueData->sum('daily_revenue');
            $averageDailyRevenue = $revenueData->avg('daily_revenue');
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'revenue_by_date' => $revenueData,
                    'summary' => [
                        'total_revenue' => $totalRevenue,
                        'average_daily_revenue' => $averageDailyRevenue,
                        'total_orders' => $revenueData->sum('orders_count')
                    ]
                ],
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve revenue report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export customers data
     */
    public function exportCustomers(Request $request): JsonResponse
    {
        try {
            $customers = User::where('is_admin', false)
                ->with(['orders' => function($query) {
                    $query->select('user_id', \DB::raw('COUNT(*) as orders_count'), \DB::raw('SUM(total_amount) as total_spent'))
                          ->groupBy('user_id');
                }])
                ->get()
                ->map(function($customer) {
                    return [
                        'id' => $customer->id,
                        'name' => $customer->name,
                        'email' => $customer->email,
                        'created_at' => $customer->created_at,
                        'orders_count' => $customer->orders->count(),
                        'total_spent' => $customer->orders->sum('total_amount')
                    ];
                });
            
            return response()->json([
                'status' => 'success',
                'data' => $customers,
                'export_type' => 'customers',
                'generated_at' => now()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to export customers data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export sales data
     */
    public function exportSales(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', Carbon::now()->subMonth());
            $endDate = $request->get('end_date', Carbon::now());
            
            $salesData = Order::with(['user', 'products'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get()
                ->map(function($order) {
                    return [
                        'order_id' => $order->id,
                        'customer_name' => $order->user->name,
                        'customer_email' => $order->user->email,
                        'order_date' => $order->created_at,
                        'status' => $order->status,
                        'total_amount' => $order->total_amount,
                        'products_count' => $order->products->count()
                    ];
                });
            
            return response()->json([
                'status' => 'success',
                'data' => $salesData,
                'export_type' => 'sales',
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ],
                'generated_at' => now()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to export sales data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}