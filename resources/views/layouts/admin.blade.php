{{-- In resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jewelry Admin - @yield('title')</title>
    {{-- 未来可以在这里添加 CSS 样式 --}}
</head>

<body>
    <nav>
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a href="{{ route('admin.products.index') }}">Products</a>
    </nav>
    <header>
        <h1>Jewelry E-Commerce Admin Panel</h1>
        <nav>
            <a href="{{ route('admin.products.index') }}">Products</a>
            {{-- 未来可以添加更多链接，如 Orders, Customers 等 --}}
        </nav>
    </header>

    <hr>

    <main>
        {{-- 成功消息提示 --}}
        @if (session('success'))
            <div style="color: green; margin-bottom: 15px;">
                {{ session('success') }}
            </div>
        @endif

        {{-- @yield 指令会把子页面里的内容填充到这里 --}}
        @yield('content')
    </main>

    <hr>

    <footer>
        <p>&copy; {{ date('Y') }} Jewelry E-Commerce. All rights reserved.</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @yield('scripts') {{-- Add a spot for page-specific scripts --}}
</body>

</html>