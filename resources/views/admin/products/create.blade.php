@extends('layouts.admin')

@section('title', 'Add New Product')

@section('content')
    <h1>Add New Product</h1>
    <form action="{{ route('admin.products.store') }}" method="POST">
        {{-- ...表单内容保持不变... --}}
    </form>
@endsection