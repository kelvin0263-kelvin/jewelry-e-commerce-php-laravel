@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="bg-white p-8 rounded-lg shadow-md">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <img src="{{ $product->image_path ? asset('storage/' . $product->image_path) : 'https://placehold.co/600x400?text=Jewelry' }}" alt="{{ $product->name }}" class="w-full rounded-lg">
            </div>
            <div>
                <h1 class="text-4xl font-bold text-gray-800">{{ $product->name }}</h1>
                <p class="text-2xl text-gray-700 mt-4">RM {{ number_format($product->price, 2) }}</p>
                <p class="text-gray-600 mt-6">{{ $product->description }}</p>

                <div class="mt-8">
                    {{-- Future "Add to Cart" button will go here --}}
                    <button class="w-full bg-gray-800 text-white py-3 px-6 rounded text-lg hover:bg-gray-700">Add to Cart (Coming Soon)</button>
                </div>
            </div>
        </div>
    </div>
@endsection