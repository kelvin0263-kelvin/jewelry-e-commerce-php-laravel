<?php

namespace App\Modules\Order\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Modules\Order\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('status'); // group by pending, processing, completed, refunded

        return view('order::order', compact('orders'));
    }
}

?>