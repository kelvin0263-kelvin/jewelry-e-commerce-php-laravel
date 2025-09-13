<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Jewelry Store')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <!-- Additional Styles -->
        @stack('styles')

</head>

<body class="font-sans antialiased bg-gray-100">

    {{-- Flash popup messages --}}
    @if (session('status'))
        <div x-data="{ show: true }" x-show="show" x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="translate-y-10 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transform transition ease-in duration-300"
            x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="translate-y-10 opacity-0"
            x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-5 left-1/2 transform -translate-x-1/2 bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg text-sm z-50">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div x-data="{ show: true }" x-show="show" x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="translate-y-10 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transform transition ease-in duration-300"
            x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="translate-y-10 opacity-0"
            x-init="setTimeout(() => show = false, 4000)"
            class="fixed bottom-5 left-1/2 transform -translate-x-1/2 bg-red-600 text-white px-4 py-3 rounded-lg shadow-lg text-sm z-50">
            {{ $errors->first() }}
        </div>
    @endif


    <div class="min-h-screen bg-gray-100">
        <x-navbar />

        <!-- Page Content -->
        <main class="container mx-auto px-6 py-8">
            @yield('content')
        </main>
    </div>

    <x-chat-widget /> {{-- Add this line --}}
</body>

</html>