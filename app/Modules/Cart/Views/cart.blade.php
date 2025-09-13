@extends('layouts.app')

@section('title', 'My Shopping Bag')


@section('content')
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <strong class="font-bold">Oops!</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="py-12">
        <div class="max-w-6xl mx-auto px-6 lg:px-8">

            <!-- Page Title -->
            <h1 class="text-3xl font-bold text-gray-900 mb-8 text-center">👜 My Shopping Bag</h1>

            @if($cartItems->count() > 0)
                <div class="overflow-x-auto bg-white shadow-xl rounded-2xl p-6 border border-gray-200">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gradient-to-r from-pink-500 to-red-500 text-white text-sm uppercase tracking-wider">
                                <th class="p-4 rounded-tl-2xl">Product</th>
                                <th class="p-4">Price</th>
                                <th class="p-4 text-center">Quantity</th>
                                <th class="p-4">Subtotal</th>
                                <th class="p-4 rounded-tr-2xl text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($cartItems as $item)
                                <tr class="hover:bg-gray-50 transition duration-200">
                                    <td class="p-4 flex items-center space-x-4">
                                        <span class="font-medium text-gray-900">{{ $item->product->name }}</span>
                                    </td>
                                    @php
                                        $unitPrice = $item->product->discount_price ?? $item->product->selling_price;
                                    @endphp
                                    <td class="p-4 text-gray-700">
                                        RM {{ number_format($unitPrice, 2) }}
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center justify-center space-x-3">
                                            <!-- Minus -->
                                            <form action="{{ route('cart.update', $item->id) }}" method="POST"onsubmit="return {{ $item->quantity == 1 ? 'confirm(\'Quantity is 1. Do you want to remove this product?\')' : 'true' }}">
                                                @csrf
                                                <input type="hidden" name="quantity" value="{{ max($item->quantity - 1, 0) }}">
                                                <button type="submit"
                                                    class="w-8 h-8 flex items-center justify-center bg-gray-200 hover:bg-gray-300 rounded-lg font-bold text-gray-700">
                                                    -
                                                </button>
                                            </form>

                                            <span class="font-semibold text-gray-800">{{ $item->quantity }}</span>

                                            <!-- Plus -->
                                            @php
                                                // ✅ Check variation-specific stock first, then fallback to inventory stock
                                                if ($item->product->variation) {
                                                    $availableStock = $item->product->variation->stock ?? 0;
                                                } elseif ($item->product->inventory) {
                                                    $availableStock = $item->product->inventory->quantity ?? 0;
                                                } else {
                                                    $availableStock = 0;
                                                }
                                            @endphp
                                            <form action="{{ route('cart.update', $item->id) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}">
                                                <button type="submit" @if($availableStock !== null && $item->quantity >= $availableStock) disabled @endif
                                                    class="w-8 h-8 flex items-center justify-center bg-gray-200 hover:bg-gray-300 rounded-lg font-bold text-gray-700
                                                                                @if($availableStock !== null && $item->quantity >= $availableStock) opacity-50 cursor-not-allowed hover:bg-gray-200 @endif">
                                                    +
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                    <td class="p-4 font-semibold text-green-600">
                                        RM {{ number_format($unitPrice * $item->quantity, 2) }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <form action="{{ route('cart.remove', $item->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to remove this product?')">
                                            @csrf
                                            <button type="submit" class="text-red-500 hover:text-red-700 font-medium">
                                                ✖ Remove
                                            </button>
                                        </form>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Bag Actions -->
                <div class="mt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <!-- Clear Bag -->
                    <form action="{{ route('cart.clear') }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to empty your bag?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-xl shadow-md transition">
                            🗑️ Empty Bag
                        </button>
                    </form>

                    <!-- Checkout -->
                    <form action="{{ route('cart.checkout') }}" method="GET" onsubmit="return confirm('Proceed to checkout?')">
                        <button type="submit"
                            class="bg-pink-500 hover:bg-pink-600 text-white px-8 py-3 rounded-xl shadow-md font-semibold transition">
                            🛍️ Proceed to Checkout
                        </button>
                    </form>

                </div>

            @else
                <!-- Empty Bag -->
                <div class="text-center bg-white shadow-lg rounded-2xl p-12">
                    <p class="text-gray-500 text-lg">Your shopping bag is empty 👜</p>
                    <a href="{{ url('/products') }}"
                        class="inline-block mt-6 bg-pink-500 hover:bg-pink-600 text-white px-6 py-3 rounded-xl shadow-md transition">
                        Browse Products
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection