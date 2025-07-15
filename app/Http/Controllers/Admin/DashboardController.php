<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

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

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalSales',
            'newCustomersThisMonth',
            'labels',
            'data'
        ));
    }
}
