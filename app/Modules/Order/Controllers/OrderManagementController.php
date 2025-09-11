<?php

namespace App\Modules\Order\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Order\Models\Order;
use Illuminate\Http\Request;

class OrderManagementController extends Controller
{
    /**
     * Show all pending orders for admin.
     */
    public function index()
    {
        // Pending orders
        $orders = Order::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // Refund orders: refunding + refund_reason not empty
        $refundOrders = Order::where('status', 'refund')
            ->where('refund_status', 'refunding')
            ->whereNotNull('refund_reason')
            ->where('refund_reason', '!=', '') // avoid empty strings
            ->orderBy('created_at', 'desc')
            ->get();

        return view('order::ordermanagement', compact('orders', 'refundOrders'));
    }



    /**
     * Update order status to shipped and assign tracking number.
     */
    public function ship($id)
    {
        $order = Order::where('id', $id)->where('status', 'pending')->firstOrFail();

        // Generate tracking number: TCK + 8 random digits
        $trackingNumber = 'TCK' . str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);

        $order->update([
            'status' => 'shipped',
            'tracking_number' => $trackingNumber,
        ]);

        return redirect()->route('ordermanagement.index')
            ->with('success', "Order #{$order->id} has been shipped with tracking number {$trackingNumber}");

    }

    public function updateRefundStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'refund_status' => 'required|in:refunded,rejected',
        ]);

        $order->update([
            'refund_status' => $request->refund_status,
        ]);

        return redirect()->back()
            ->with('success', "Refund status for Order #{$order->id} updated to {$request->refund_status}");
    }

}
