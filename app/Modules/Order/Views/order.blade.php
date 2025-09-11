@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">
            <div class="mb-10">
                <h1 class="text-3xl font-bold text-gray-900">Orders</h1>
                <p class="text-gray-600">Here you can track all your orders by status.</p>
            </div>

            @foreach (['pending', 'shipped', 'delivered', 'completed', 'refund'] as $status)
                @php
                    $colors = [
                        'pending' => 'from-yellow-400 to-yellow-600 border-yellow-400',
                        'shipped' => 'from-blue-400 to-blue-600 border-blue-400',
                        'delivered' => 'from-purple-400 to-purple-600 border-purple-400',
                        'completed' => 'from-green-400 to-green-600 border-green-400',
                        'refund' => 'from-orange-400 to-orange-600 border-red-400',
                    ];
                @endphp

                <div class="bg-white border {{ $colors[$status] }} shadow-lg sm:rounded-lg mb-6 overflow-hidden">
                    <div class="bg-gradient-to-r {{ $colors[$status] }} px-6 py-4">
                        <h3 class="text-lg font-bold text-white capitalize tracking-wide">
                            {{ ucfirst($status) }} Orders
                        </h3>
                    </div>

                    <div class="p-8">
                        @if(isset($orders[$status]) && $orders[$status]->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full border-collapse">
                                    <thead>
                                        <tr class="bg-gray-100 text-gray-700">
                                            <th class="px-4 py-3 text-left">Order ID</th>
                                            <th class="px-4 py-3 text-left">Tracking No.</th>
                                            <th class="px-4 py-3 text-left">Total Amount</th>
                                            <th class="px-4 py-3 text-left">Payment Method</th>
                                            <th class="px-4 py-3 text-left">Payment Status</th>
                                            <th class="px-4 py-3 text-left">Created At</th>
                                            @if($status === 'delivered')
                                                <th class="px-4 py-3 text-left">Receive?</th>
                                            @elseif($status === 'refund')
                                                <th class="px-4 py-3 text-left">Refund Status</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($orders[$status] as $order)
                                            <tr class="hover:bg-gray-50 hover:shadow-md transition cursor-pointer"onclick="window.location='{{ route('orders.show', $order->id) }}'">

                                                <td class="px-4 py-3 text-gray-900 font-semibold">#{{ $order->id }}</td>
                                                <td class="px-4 py-3 text-indigo-600 font-medium">
                                                    {{ $order->tracking_number ?? 'N/A' }}
                                                </td>
                                                <td class="px-4 py-3 text-green-600 font-bold">
                                                    RM {{ number_format($order->total_amount, 2) }}
                                                </td>
                                                <td class="px-4 py-3 text-gray-700">{{ ucfirst($order->payment_method) }}</td>
                                                <td class="px-4 py-3">
                                                    <span
                                                        class="px-3 py-1 rounded-full text-xs font-semibold shadow-sm
                                                        @if($order->payment_status === 'completed') bg-green-100 text-green-800
                                                        @elseif($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                                        @elseif($order->payment_status === 'failed') bg-red-100 text-red-800
                                                        @else bg-gray-100 text-gray-800 @endif">
                                                        {{ ucfirst($order->payment_status) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-gray-600">
                                                    {{ $order->created_at->format('d M Y, h:i A') }}
                                                </td>

                                                @if($status === 'delivered')
                                                    <td class="px-4 py-3">
                                                        <form action="{{ route('orders.complete', $order->id) }}" method="POST"
                                                            class="inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="w-full px-5 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-md text-sm font-semibold">
                                                                ✅ Complete Order
                                                            </button>
                                                        </form>
                                                        <br /><br />
                                                        <form action="{{ route('orders.refund', $order->id) }}" method="POST"
                                                            class="inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="w-full px-5 py-2.5 bg-orange-400 hover:bg-orange-600 text-white rounded-md text-sm font-semibold">
                                                                💰 Make Refund
                                                            </button>
                                                        </form>
                                                    </td>
                                                @elseif($status === 'refund')
                                                    <td class="px-4 py-3 text-gray-700 font-semibold" onclick="event.stopPropagation()">
                                                        @if($order->refund_status === 'refunding')
                                                            <div>
                                                                <button type="button"
                                                                    class="w-full px-5 py-2.5 rounded-full text-sm font-semibold shadow-sm bg-yellow-100 text-yellow-600 hover:bg-yellow-200"
                                                                    onclick="handleRefundClick('{{ $order->id }}', '{{ $order->refund_reason }}')">
                                                                    {{ ucfirst($order->refund_status) }}
                                                                </button>

                                                                <div id="refund-form-{{ $order->id }}" class="mt-2 hidden">
                                                                    <form action="{{ route('orders.submitRefundReason', $order->id) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        <div class="flex flex-col space-y-2">
                                                                            <!-- Radio options -->
                                                                            <label class="inline-flex items-center">
                                                                                <input type="radio" name="refund_reason_option"
                                                                                    value="Wrong Item" class="form-radio text-green-600"
                                                                                    onclick="
                                                                                                                var textarea = document.getElementById('refund-reason-textarea-{{ $order->id }}');
                                                                                                                textarea.classList.add('hidden');
                                                                                                                textarea.value = this.value;
                                                                                                            " required>
                                                                                <span class="ml-2">Wrong Item</span>
                                                                            </label>
                                                                            <label class="inline-flex items-center">
                                                                                <input type="radio" name="refund_reason_option"
                                                                                    value="Damaged Item" class="form-radio text-green-600"
                                                                                    onclick="
                                                                                                                var textarea = document.getElementById('refund-reason-textarea-{{ $order->id }}');
                                                                                                                textarea.classList.add('hidden');
                                                                                                                textarea.value = this.value;
                                                                                                            " required>
                                                                                <span class="ml-2">Damaged Item</span>
                                                                            </label>
                                                                            <label class="inline-flex items-center">
                                                                                <input type="radio" name="refund_reason_option" value="Other"
                                                                                    class="form-radio text-green-600" onclick="
                                                                                                                var textarea = document.getElementById('refund-reason-textarea-{{ $order->id }}');
                                                                                                                textarea.classList.remove('hidden');
                                                                                                                textarea.value = '';
                                                                                                            " required>
                                                                                <span class="ml-2">Other</span>
                                                                            </label>
                                                                            <!-- Textarea for "Other" -->
                                                                            <textarea id="refund-reason-textarea-{{ $order->id }}"
                                                                                name="refund_reason" rows="2"
                                                                                class="w-full p-2 border rounded-md mt-2 hidden"
                                                                                placeholder="Enter refund reason...">{{ old('refund_reason', $order->refund_reason) }}</textarea>
                                                                        </div>
                                                                        <button type="submit"
                                                                            class="mt-2 w-full px-5 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-md text-sm font-semibold">
                                                                            Submit Reason
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <span class="w-full px-5 py-2.5 rounded-full text-sm font-semibold shadow-sm
                                                                                        @if($order->refund_status === 'rejected') bg-red-100 text-red-600 
                                                                                        @elseif($order->refund_status === 'refunded') bg-green-100 text-green-600 
                                                                                        @else bg-gray-100 text-gray-800 @endif">
                                                                {{ ucfirst($order->refund_status) }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                @endif
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

    <script>
        function handleRefundClick(orderId, refundReason) {
            if (!refundReason) {
                // Show the refund reason form if refund_reason is empty
                document.getElementById('refund-form-' + orderId).classList.toggle('hidden');
            } else {
                // Otherwise, show alert
                alert('The refund is processing.');
            }
        }
    </script>
@endsection