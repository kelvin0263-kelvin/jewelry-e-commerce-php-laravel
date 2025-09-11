@extends('layouts.admin')

@section('title', 'Edit Customer')

@section('content')
    <!-- Header -->
    <div class="mb-6 flex items-start justify-between">
        <div class="flex items-center">
            <div class="h-12 w-12 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-semibold text-lg">
                {{ strtoupper(mb_substr($customer->name, 0, 1)) }}
            </div>
            <div class="ml-4">
                <h1 class="text-2xl font-bold text-gray-800">Edit Customer</h1>
                <p class="text-gray-600">{{ $customer->name }} · <a href="mailto:{{ $customer->email }}" class="hover:underline">{{ $customer->email }}</a></p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.customers.show', $customer) }}"
               class="inline-flex items-center px-4 py-2 rounded-md bg-gray-200 text-gray-800 hover:bg-gray-300 transition-colors">Cancel</a>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <strong class="text-red-800">Whoops! Something went wrong.</strong>
                    <div class="mt-1">
                        @foreach ($errors->all() as $error)
                            <p class="text-red-800">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <form action="{{ route('admin.customers.update', $customer) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}" required
                           class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}" required
                           class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.customers.show', $customer) }}"
                   class="px-4 py-2 rounded-md bg-gray-200 text-gray-800 hover:bg-gray-300 transition-colors">Cancel</a>
                <button type="submit"
                        class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700 transition-colors">Save Changes</button>
            </div>
        </form>
    </div>
@endsection
