<?php

namespace App\Modules\Order\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Models\OrderItem;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OrderApiController extends Controller
{
    /**
     * Get all orders for the authenticated user
     */
    public function index()
    {
        try {
            // Before fetching, update shipped orders older than 1 minute → delivered
            Order::where('status', 'shipped')
                ->where('updated_at', '<=', Carbon::now()->subMinute())
                ->update(['status' => 'delivered']);

            // Fetch orders of this user, sorted by id ascending
            $orders = Order::where('user_id', Auth::id())
                ->with(['items.product'])
                ->orderBy('id', 'asc')
                ->get()
                ->groupBy('status');

            return response()->json([
                'success' => true,
                'data' => [
                    'orders' => $orders,
                    'total_orders' => $orders->flatten()->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific order by ID
     */
    public function show($id)
    {
        try {
            $order = Order::where('id', $id)
                ->where('user_id', Auth::id())
                ->with(['items.product'])
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => [
                    'order' => $order,
                    'order_items' => $order->items
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new order (used by CartApiController)
     */
    public function createOrder($subtotal, $discount, $shippingCost, $total, Request $request, $paymentInfo)
    {
        try {
            $order = Order::create([
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

            return $order;

        } catch (\Exception $e) {
            throw new \Exception('Failed to create order: ' . $e->getMessage());
        }
    }

    /**
     * Create order items (used by CartApiController)
     */
    public function createItems($order, $cartItems)
    {
        try {
            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->discount_price ?? $item->product->selling_price,
                    'subtotal' => ($item->product->discount_price ?? $item->product->selling_price) * $item->quantity,
                ]);
            }
        } catch (\Exception $e) {
            throw new \Exception('Failed to create order items: ' . $e->getMessage());
        }
    }

    /**
     * Mark order as completed
     */
    public function markAsCompleted($id)
    {
        try {
            $order = Order::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $order->update(['status' => 'completed']);

            return response()->json([
                'success' => true,
                'message' => 'Order marked as completed successfully',
                'data' => [
                    'order_id' => $id,
                    'status' => 'completed'
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark order as completed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Request refund for an order
     */
    public function markAsRefund($id)
    {
        try {
            $order = Order::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $order->update([
                'status' => 'refund',
                'refund_status' => 'refunding'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Refund request submitted successfully',
                'data' => [
                    'order_id' => $id,
                    'status' => 'refund',
                    'refund_status' => 'refunding'
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit refund request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit refund reason
     */
    public function submitRefundReason(Request $request, $id)
    {
        try {
            $request->validate([
                'refund_reason' => 'required|string|max:255',
            ]);

            $order = Order::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $order->update([
                'refund_reason' => $request->refund_reason
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Refund reason submitted successfully',
                'data' => [
                    'order_id' => $id,
                    'refund_reason' => $request->refund_reason
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit refund reason',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order items for a specific order
     */
    public function getOrderItems($orderId)
    {
        try {
            $order = Order::where('id', $orderId)
                ->where('user_id', Auth::id())
                ->with(['items.product'])
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => [
                    'order' => $order,
                    'order_items' => $order->items
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve order items',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get orders by status
     */
    public function getOrdersByStatus($status)
    {
        try {
            $validStatuses = ['pending', 'shipped', 'delivered', 'completed', 'refund'];
            
            if (!in_array($status, $validStatuses)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status. Valid statuses are: ' . implode(', ', $validStatuses)
                ], 400);
            }

            $orders = Order::where('user_id', Auth::id())
                ->where('status', $status)
                ->with(['items.product'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'orders' => $orders,
                    'status' => $status,
                    'count' => $orders->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve orders by status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order tracking information
     */
    public function getTracking($id)
    {
        try {
            $order = Order::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $trackingInfo = [
                'order_id' => $order->id,
                'status' => $order->status,
                'tracking_number' => $order->tracking_number,
                'shipping_method' => $order->shipping_method,
                'shipping_address' => $order->shipping_address,
                'shipping_postal_code' => $order->shipping_postal_code,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at
            ];

            return response()->json([
                'success' => true,
                'data' => $trackingInfo
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tracking information',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

