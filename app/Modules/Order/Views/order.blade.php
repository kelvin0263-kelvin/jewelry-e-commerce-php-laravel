@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">
            <div class="mb-10">
                <h1 class="text-3xl font-bold text-gray-900">Orders</h1>
                <p class="text-gray-600">Here you can track all your orders by status.</p>
            </div>

            @foreach (['pending', 'processing', 'completed', 'refunded'] as $status)
                <div class="bg-white border border-gray-300 shadow-md sm:rounded-lg mb-6">
                    <div class="p-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3 capitalize">
                            {{ ucfirst($status) }} Orders
                        </h3>

                        @if(isset($orders[$status]) && $orders[$status]->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full border-collapse">
                                    <thead>
                                        <tr class="bg-gradient-to-r from-blue-600 to-purple-600 text-white">
                                            <th class="px-4 py-3 text-left">Order ID</th>
                                            <th class="px-4 py-3 text-left">Tracking No.</th>
                                            <th class="px-4 py-3 text-left">Total Amount</th>
                                            <th class="px-4 py-3 text-left">Payment Method</th>
                                            <th class="px-4 py-3 text-left">Payment Status</th>
                                            <th class="px-4 py-3 text-left">Created At</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($orders[$status] as $order)
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-4 py-3 text-gray-900 font-medium">#{{ $order->id }}</td>
                                                <td class="px-4 py-3 text-gray-700">{{ $order->tracking_number ?? 'N/A' }}</td>
                                                <td class="px-4 py-3 text-green-600 font-semibold">RM
                                                    {{ number_format($order->total_amount, 2) }}</td>
                                                <td class="px-4 py-3 text-gray-700">{{ ucfirst($order->payment_method) }}</td>
                                                <td class="px-4 py-3">
                                                    <span class="px-2 py-1 rounded-lg text-xs font-medium
                                                                    @if($order->payment_status === 'completed') bg-green-100 text-green-700
                                                                    @elseif($order->payment_status === 'pending') bg-yellow-100 text-yellow-700
                                                                    @elseif($order->payment_status === 'failed') bg-red-100 text-red-700
                                                                    @else bg-gray-100 text-gray-700 @endif">
                                                        {{ ucfirst($order->payment_status) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-gray-600">{{ $order->created_at->format('d M Y, h:i A') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500 italic">No {{ $status }} orders found.</p>
                        @endif
                    </div>
                </div>
            @endforeach

        </div>
    </div>
@endsection