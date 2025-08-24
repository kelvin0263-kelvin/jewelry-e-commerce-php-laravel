@extends('layouts.admin')

@section('title', 'Product Performance Report')

@section('content')
    <h1>Product Performance Report</h1>
    <p>Analysis of product sales and revenue, sorted by most units sold.</p>

    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Current Selling Price (RM)</th>
                <th>Total Units Sold</th>
                <th>Total Revenue (RM)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ number_format($product->price, 2) }}</td>
                    <td>{{ $product->total_sold }}</td>
                    <td>{{ number_format($product->total_revenue, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No sales data found to generate a report.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection