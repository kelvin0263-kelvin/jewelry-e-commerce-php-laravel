@extends('layouts.admin')

@section('title', 'Order Management (Admin)')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <h1 class="text-3xl font-bold text-gray-900 mb-6">📦 Order Management</h1>
            <p class="text-gray-600 mb-8">Manage and ship pending orders.</p>

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse">
                        <thead>
                            <tr class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                                <th class="px-6 py-3 text-left">Order ID</th>
                                <th class="px-6 py-3 text-left">Customer ID</th>
                                <th class="px-6 py-3 text-left">Total Amount</th>
                                <th class="px-6 py-3 text-left">Payment Method</th>
                                <th class="px-6 py-3 text-left">Created At</th>
                                <th class="px-6 py-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($orders as $order)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-3 font-semibold text-gray-900">#{{ $order->id }}</td>
                                    <td class="px-6 py-3 text-gray-700">{{ $order->user_id }}</td>
                                    <td class="px-6 py-3 text-green-600 font-bold">
                                        RM {{ number_format($order->total_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-3 text-gray-700">{{ ucfirst($order->payment_method) }}</td>
                                    <td class="px-6 py-3 text-gray-600">{{ $order->created_at->format('d M Y, h:i A') }}</td>
                                    <td class="px-6 py-3 text-center">
                                        <form action="{{ route('ordermanagement.ship', $order->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md shadow-sm text-sm font-medium">
                                                🚚 Ship Order
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 italic">
                                        No pending orders found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">💰 Refund Orders</h2>
                <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse">
                            <thead>
                                <tr class="bg-gradient-to-r from-orange-500 to-red-500 text-white">
                                    <th class="px-6 py-3 text-left">Order ID</th>
                                    <th class="px-6 py-3 text-left">Customer ID</th>
                                    <th class="px-6 py-3 text-left">Total Amount</th>
                                    <th class="px-6 py-3 text-left">Refund Reason</th>
                                    <th class="px-6 py-3 text-left">Created At</th>
                                    <th class="px-6 py-3 text-center">Refund Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($refundOrders as $order)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-3 font-semibold text-gray-900">#{{ $order->id }}</td>
                                        <td class="px-6 py-3 text-gray-700">{{ $order->user_id }}</td>
                                        <td class="px-6 py-3 text-green-600 font-bold">
                                            RM {{ number_format($order->total_amount, 2) }}
                                        </td>
                                        <td class="px-6 py-3 text-gray-700">{{ $order->refund_reason }}</td>
                                        <td class="px-6 py-3 text-gray-600">{{ $order->created_at->format('d M Y, h:i A') }}
                                        </td>
                                        <td class="px-6 py-3 text-center">
                                            <div class="flex justify-center gap-2">
                                                <!-- Refunded button -->
                                                <form action="{{ route('ordermanagement.updateRefundStatus', $order->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <input type="hidden" name="refund_status" value="refunded">
                                                    <button type="submit"
                                                        class="w-32 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-sm font-semibold">
                                                        💰 Refunded
                                                    </button>
                                                </form>
                                                <!-- Rejected button -->
                                                <form action="{{ route('ordermanagement.updateRefundStatus', $order->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <input type="hidden" name="refund_status" value="rejected">
                                                    <button type="submit"
                                                        class="w-32 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-md text-sm font-semibold">
                                                        ❌ Rejected
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 italic">
                                            No refund orders found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection