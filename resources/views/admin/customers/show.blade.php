@extends('layouts.admin')

@section('title', 'Customer Details')

@section('content')
    <h1>Customer Details: {{ $customer->name }}</h1>

    <div>
        <p><strong>Email:</strong> {{ $customer->email }}</p>
        <p><strong>Registered:</strong> {{ $customer->created_at->format('F j, Y, g:i a') }}</p>
    </div>

    <hr>

    <h2>Order History</h2>
    @forelse($customer->orders as $order)
        <div style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px;">
            <h4>Order #{{ $order->id }} - {{ $order->created_at->format('Y-m-d') }}</h4>
            <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
            <p><strong>Total:</strong> RM {{ number_format($order->total_amount, 2) }}</p>
            <strong>Products:</strong>
            <ul>
                @foreach($order->products as $product)
                    <li>{{ $product->name }} ({{ $product->pivot->quantity }} x RM {{ number_format($product->pivot->price, 2) }})</li>
                @endforeach
            </ul>
        </div>
    @empty
        <p>This customer has not placed any orders yet.</p>
    @endforelse
@endsection