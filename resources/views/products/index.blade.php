{{-- In resources/views/products/index.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Products</title>
</head>
<body>
    <h1>Our Jewelry Collection</h1>

    <div>
        @if($products->isEmpty())
            <p>No products found.</p>
        @else
            <ul>
                @foreach($products as $product)
                    <li>
                        <h2>{{ $product->name }}</h2>
                        <p>Price: RM {{ $product->price }}</p>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</body>
</html>