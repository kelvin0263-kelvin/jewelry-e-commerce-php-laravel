@extends('layouts.app')

@section('title', 'My Cart')

@section('content')
    <h1 class="text-2xl font-bold mb-6">My Shopping Cart</h1>

    @if($cartItems->count() > 0)
        <table class="w-full border-collapse border text-center">
            <thead>
                <tr>
                    <th class="border p-2">Product</th>
                    <th class="border p-2">Price</th>
                    <th class="border p-2">Quantity</th>
                    <th class="border p-2">Total</th>
                    <th class="border p-2">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cartItems as $item)
                    <tr>
                        <td class="border p-2 text-center">{{ $item->product->name }}</td>
                        <td class="border p-2 text-center">RM {{ number_format($item->product->price, 2) }}</td>
                        <td class="border p-2">
                            <div class="flex items-center justify-center space-x-2">
                                <!-- Minus button -->
                                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="quantity" value="{{ max($item->quantity - 1, 0) }}">
                                    <button type="submit" class="bg-gray-300 px-2 py-1 rounded">-</button>
                                </form>

                                <span class="mx-2">{{ $item->quantity }}</span>

                                <!-- Plus button -->
                                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}">
                                    <button type="submit" class="bg-gray-300 px-2 py-1 rounded">+</button>
                                </form>
                            </div>
                        </td>
                        <td class="border p-2 text-center">RM {{ number_format($item->product->price * $item->quantity, 2) }}</td>
                        <td class="border p-2 text-center">
                            <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500">Remove</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 flex justify-center space-x-4">
            <!-- Clear Cart Button -->
            <form action="{{ route('cart.clear') }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                    Clear Cart
                </button>
            </form>

            <!-- Checkout Button -->
            <form action="{{ route('cart.checkout') }}" method="GET">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                    Checkout
                </button>
            </form>
        </div>

    @else
        <p class="text-gray-500 text-center">Your cart is empty.</p>
    @endif
@endsection