@extends('layouts.admin')

@section('title', 'Edit Customer')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1>Customer Details: {{ $customer->name }}</h1>
        <a href="{{ route('admin.customers.edit', $customer->id) }}"
            style="background-color: #4a5568; color: white; padding: 8px 16px; border-radius: 5px; text-decoration: none;">Edit
            Customer</a>
    </div>
    <h1>Edit Customer: {{ $customer->name }}</h1>

    @if ($errors->any())
        <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 20px;">
            <strong>Whoops! Something went wrong.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 15px;">
            <label for="name">Name:</label><br>
            <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}" required
                style="width: 300px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}" required
                style="width: 300px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label>
                <input type="checkbox" name="is_admin" value="1" {{ old('is_admin', $customer->is_admin) ? 'checked' : '' }}>
                Set as Administrator
            </label>
        </div>

        <button type="submit">Update Customer</button>
    </form>
@endsection