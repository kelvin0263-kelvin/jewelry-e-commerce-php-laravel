@extends('layouts.admin')

@section('title', 'Customer List')

@section('content')


    <h1>Customer Management</h1>

    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Registered On</th>
                <th>Total Orders</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($customers as $customer)
                <tr>
                    <td>{{ $customer->id }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->created_at->format('Y-m-d') }}</td>
                    <td>{{ $customer->orders_count }}</td>
                    <td>
                        <a href="{{ route('admin.customers.show', $customer->id) }}">View</a> |
                        <a href="{{ route('admin.customers.edit', $customer->id) }}">Edit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No customers found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $customers->links() }}
    </div>
@endsection