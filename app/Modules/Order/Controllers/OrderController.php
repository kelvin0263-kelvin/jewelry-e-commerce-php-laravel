<?php

namespace App\Modules\Order\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Modules\Order\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index()
    {
        // Before fetching, update shipped orders older than 1 minute → delivered
        Order::where('status', 'shipped')
            ->where('updated_at', '<=', Carbon::now()->subMinute())
            ->update(['status' => 'delivered']);

        // Fetch orders of this user, sorted by id ascending
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('id', 'asc') // ascending order by ID
            ->get()
            ->groupBy('status');

        return view('order::order', compact('orders'));
    }

    public function createOrder($subtotal, $discount, $shippingCost, $total, Request $request, $paymentInfo)
    {
        return Order::create([
            'user_id' => Auth::id(),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'shipping_cost' => $shippingCost,
            'total_amount' => $total,
            'promo_code' => $request->promocode,
            'payment_method' => $paymentInfo,
            'payment_status' => 'completed',
            'shipping_address' => $request->address,
            'shipping_postal_code' => $request->postal_code,
            'shipping_method' => $request->shipping,
            'status' => 'pending',
        ]);
    }

    public function show($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        $orderItems = $order->items;

        return view('order::orderitem', compact('order', 'orderItems'));
    }

    public function markAsCompleted($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $order->update(['status' => 'completed']);

        return redirect()->route('orders.index')->with('success', 'Order marked as completed!');
    }

    public function markAsRefund($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $order->update(['status' => 'refund']);
        $order->update(['refund_status' => 'refunding']);

        return redirect()->route('orders.index')->with('success', 'Refund request submitted!');
    }

    public function submitRefundReason(Request $request, $id)
    {
        $request->validate([
            'refund_reason' => 'required|string|max:255',
        ]);

        $order = Order::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        // Update the refund_reason in database
        $order->update([
            'refund_reason' => $request->refund_reason
        ]);

        return redirect()->route('orders.index')->with('success', 'Refund reason submitted successfully!');
    }

}

?>