@extends('layouts.app')

@section('title', 'Our Collection')

@section('content')
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Our Collection</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse ($products as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <a href="{{ route('products.show', $product) }}">
                    <img src="{{ $product->image_path ? asset('storage/' . $product->image_path) : 'https://placehold.co/600x400?text=Jewelry' }}" alt="{{ $product->name }}" class="w-full h-56 object-cover">
                </a>
                <div class="p-4">
                    <h2 class="text-lg font-semibold text-gray-800">{{ $product->name }}</h2>
                    <p class="text-gray-600 mt-2">RM {{ number_format($product->price, 2) }}</p>
                    <a href="{{ route('products.show', $product) }}" class="mt-4 inline-block bg-gray-800 text-white py-2 px-4 rounded hover:bg-gray-700">View Details</a>
                </div>
            </div>
        @empty
            <p class="col-span-full text-gray-500">No products are currently available.</p>
        @endforelse
    </div>
    <div class="mt-8">
        {{ $products->links() }}
    </div>
@endsection