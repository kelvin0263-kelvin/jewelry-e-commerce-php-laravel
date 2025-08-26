@extends('layouts.admin')

@section('title', 'Product Management')

@section('content')
    <h1>Product Management</h1>
    <p><em>Enhance products from inventory and publish them to customers.</em></p>
    
    @if(session('success'))
        <div style="background: lightgreen; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div style="background: lightcoral; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
            {{ session('error') }}
        </div>
    @endif

    <div style="margin-bottom: 20px;">
        <strong>Legend:</strong>
        <span style="background: orange; color: white; padding: 2px 6px; border-radius: 3px; margin-right: 10px;">Pending Review</span>
        <span style="background: blue; color: white; padding: 2px 6px; border-radius: 3px; margin-right: 10px;">Approved</span>
        <span style="background: green; color: white; padding: 2px 6px; border-radius: 3px;">Published</span>
    </div>

    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>ID</th>
                <th>SKU</th>
                <th>Name</th>
                <th>Category</th>
                <th>Status</th>
                <th>Customer Images</th>
                <th>Published By</th>
                <th>Published At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category ?? 'Not set' }}</td>
                    <td>
                        @if($product->status === 'pending_review')
                            <span style="background: orange; color: white; padding: 2px 6px; border-radius: 3px;">Pending Review</span>
                        @elseif($product->status === 'approved')
                            <span style="background: blue; color: white; padding: 2px 6px; border-radius: 3px;">Approved</span>
                        @elseif($product->status === 'published')
                            <span style="background: green; color: white; padding: 2px 6px; border-radius: 3px;">Published</span>
                        @endif
                    </td>
                    <td>
                        @if($product->customer_images && count($product->customer_images) > 0)
                            <span style="color: green;">{{ count($product->customer_images) }} images</span>
                        @else
                            <span style="color: red;">No images</span>
                        @endif
                    </td>
                    <td>{{ $product->publishedBy->name ?? 'Not published' }}</td>
                    <td>{{ $product->published_at ? $product->published_at->format('M d, Y') : 'Not published' }}</td>
                    <td>
                        @if($product->status === 'pending_review')
                            <form action="{{ route('admin.product-management.approve', $product) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" style="background: blue; color: white; border: none; padding: 5px 10px; cursor: pointer;">Approve</button>
                            </form>
                        @endif
                        
                        @if($product->status === 'approved')
                            <form action="{{ route('admin.product-management.publish', $product) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" style="background: green; color: white; border: none; padding: 5px 10px; cursor: pointer;">Publish</button>
                            </form>
                        @endif
                        
                        @if($product->status === 'published')
                            <form action="{{ route('admin.product-management.unpublish', $product) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" style="background: gray; color: white; border: none; padding: 5px 10px; cursor: pointer;">Unpublish</button>
                            </form>
                        @endif
                        
                        <a href="{{ route('admin.product-management.edit', $product) }}" style="margin-left: 5px;">Edit Details</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">No products ready for management. Create products in Inventory first.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $products->links() }}

@endsection

