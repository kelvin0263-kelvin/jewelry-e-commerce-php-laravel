@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
    <div class="py-12">
        <div class="max-w-6xl mx-auto px-6 lg:px-8">

            <!-- Page Title -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4 text-center">
                📦 Order #{{ $order->id }} Details
            </h1>

            <!-- Order Status -->
            <!-- Order Status -->
            <div class="text-center mb-10">
                <span class="inline-block px-6 py-3 rounded-2xl text-lg font-bold shadow-md tracking-wide
            @if($order->status === 'pending') bg-yellow-500 text-white
            @elseif($order->status === 'shipped') bg-blue-500 text-white
            @elseif($order->status === 'delivered') bg-purple-600 text-white
            @elseif($order->status === 'completed') bg-green-600 text-white
            @elseif($order->status === 'refund') bg-orange-600 text-white
            @else bg-gray-500 text-white @endif">
                    🚀 {{ strtoupper($order->status) }}
                </span>
            </div>


            <div class="overflow-x-auto bg-white shadow-xl rounded-2xl p-6 border border-gray-200">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm uppercase tracking-wider">
                            <th class="p-4 rounded-tl-2xl">Product</th>
                            <th class="p-4">Price</th>
                            <th class="p-4 text-center">Quantity</th>
                            <th class="p-4 rounded-tr-2xl">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($orderItems as $item)
                            <tr class="hover:bg-gray-50 transition duration-200">
                                <td class="p-4 font-medium text-gray-900">
                                    {{ $item->product->name ?? 'Unknown Product' }}
                                </td>
                                <td class="p-4 text-gray-700">
                                    RM {{ number_format($item->price, 2) }}
                                </td>
                                <td class="p-4 text-center font-semibold text-gray-800">
                                    {{ $item->quantity }}
                                </td>
                                <td class="p-4 font-semibold text-green-600">
                                    RM {{ number_format($item->subtotal, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Order Summary -->
            <div class="mt-8 bg-white shadow-lg rounded-2xl p-6 border border-gray-200 space-y-3">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">🧾 Order Summary</h2>
                <p><span class="font-semibold">Subtotal:</span> RM {{ number_format($order->subtotal, 2) }}</p>
                <p><span class="font-semibold">Discount:</span> -RM {{ number_format($order->discount, 2) }}</p>
                <p><span class="font-semibold">Shipping:</span> RM {{ number_format($order->shipping_cost, 2) }}</p>
                <p class="text-lg font-bold text-gray-900">Total: RM {{ number_format($order->total_amount, 2) }}</p>
            </div>

            <!-- Back Button -->
            <div class="mt-8 text-center">
                <a href="{{ url('/orders') }}"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl shadow-md transition">
                    🔙 Back to Orders
                </a>
            </div>

        </div>
    </div>
@endsection