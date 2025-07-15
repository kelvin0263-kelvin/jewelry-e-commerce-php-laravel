@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
    <h1>Edit Product: {{ $product->name }}</h1>
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST">
        {{-- ...表单内容保持不变... --}}
    </form>
@endsection