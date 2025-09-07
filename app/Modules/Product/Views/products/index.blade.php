@extends('layouts.app')

@section('title', 'Our Collection')

@section('content')
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Our Collection</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse ($products as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <a href="{{ route('products.show', $product) }}">
                    @if ($product->customer_images && count($product->customer_images) > 0)
                        <img src="{{ asset('storage/' . $product->customer_images[0]) }}" alt="{{ $product->name }}"
                            class="w-full h-56 object-cover">
                    @else
                        <img src="https://placehold.co/600x400?text=Jewelry" alt="{{ $product->name }}"
                            class="w-full h-56 object-cover">
                    @endif
                </a>
                <div class="p-4">
                    <h2 class="text-lg font-semibold text-gray-800">{{ $product->name }}</h2>
                    @if ($product->category)
                        <p class="text-sm text-gray-500">{{ $product->category }}</p>
                    @endif
                    <div class="mt-2">
                        @if ($product->discount_price)
                            <span class="text-red-500 font-bold">RM {{ number_format($product->discount_price, 2) }}</span>
                            <span class="text-gray-500 line-through ml-2">RM {{ number_format($product->price, 2) }}</span>
                        @else
                            <span class="text-gray-800 font-bold">RM {{ number_format($product->price, 2) }}</span>
                        @endif
                    </div>
                    <a href="{{ route('products.show', $product) }}"
                        class="mt-4 inline-block bg-gray-800 text-white py-2 px-4 rounded hover:bg-gray-700">View
                        Details</a>
                </div>
            </div>
        @empty
            <p class="col-span-full text-gray-500">No products are currently available.</p>
        @endforelse
    </div>
    <div class="mt-8">
        {{ $products->links() }}
    </div>
   <button id="ask-support" class="btn btn-primary">Chat with us</button>
<script>
document.getElementById('ask-support')?.addEventListener('click', async () => {
  const ctx = {
    source: 'product_page',
    product_id: '{{ $product->id ?? null }}',
    product_name: '{{ $product->name ?? null }}'
  };
  try {
    await window.SupportChat.open({
      autoStart: true,
      initial_message: 'Hi, I need help with this product.',
      escalation_context: ctx
    });
  } catch (e) {
    console.error(e);
    alert('Unable to start chat right now.');
  }
});
</script>
@endsection
