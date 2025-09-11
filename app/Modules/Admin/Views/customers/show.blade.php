@extends('layouts.admin')

@section('title', 'Customer Details')

@section('content')
    <!-- Header -->
    <div class="mb-6 flex items-start justify-between">
        <div class="flex items-center">
            <div class="h-12 w-12 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-semibold text-lg">
                {{ strtoupper(mb_substr($customer->name, 0, 1)) }}
            </div>
            <div class="ml-4">
                <h1 class="text-2xl font-bold text-gray-800">{{ $customer->name }}</h1>
                <div class="text-gray-600">
                    <a href="mailto:{{ $customer->email }}" class="hover:underline">{{ $customer->email }}</a>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.customers.edit', $customer) }}"
                class="inline-flex items-center px-4 py-2 rounded-md bg-gray-800 text-white hover:bg-gray-900 transition-colors">Edit</a>
            <a href="{{ route('admin.customers.index') }}"
               class="inline-flex items-center px-4 py-2 rounded-md bg-gray-200 text-gray-800 hover:bg-gray-300 transition-colors">Back</a>
        </div>
    </div>

    <!-- Info + Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500 mb-1">Registered</div>
            <div class="text-gray-900">{{ $customer->created_at?->format('M j, Y g:i A') ?? '—' }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500 mb-1">Email Status</div>
            @if($customer->email_verified_at)
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Verified
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Unverified
                </span>
            @endif
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500 mb-1">Total Orders</div>
            <div class="text-gray-900 font-semibold">{{ $customer->orders->count() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500 mb-1">Total Spent</div>
            <div class="text-gray-900 font-semibold">RM {{ number_format($customer->orders->sum('total_amount'), 2) }}</div>
        </div>
    </div>

    <!-- Orders -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Order History</h2>
                <p class="text-sm text-gray-600">{{ $customer->orders->count() }} orders found</p>
            </div>
        </div>

        @if($customer->orders->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($customer->orders as $order)
                            @php
                                $itemsCount = $order->products->sum(fn($p) => (int) ($p->pivot->quantity ?? 0));
                                $statusColors = [
                                    'completed' => 'bg-green-100 text-green-700',
                                    'processing' => 'bg-blue-100 text-blue-700',
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                ];
                                $statusClass = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $order->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at?->format('M j, Y') ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                        {{ ucwords(str_replace('_', ' ', $order->status ?? 'unknown')) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $itemsCount }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">RM {{ number_format((float) $order->total_amount, 2) }}</td>
                            </tr>
                            @if($order->products->count() > 0)
                                <tr class="bg-gray-50">
                                    <td colspan="5" class="px-6 py-3">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($order->products as $product)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs bg-white border border-gray-200 text-gray-700">
                                                    {{ $product->name }}
                                                    <span class="ml-2 text-gray-500">x{{ (int) ($product->pivot->quantity ?? 0) }}</span>
                                                    <span class="ml-2 text-gray-500">RM {{ number_format((float) ($product->pivot->price ?? 0), 2) }}</span>
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-10 text-center text-gray-500">
                This customer has not placed any orders yet.
            </div>
        @endif
    </div>
@endsection
