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


<div class="min-h-screen bg-gray-100 flex flex-col justify-center items-center">
        <div class="w-full max-w-md bg-white shadow-xl rounded-2xl p-8 border border-gray-200">

            <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Verify Code</h2>
            <p class="mb-6 text-sm text-gray-600">
                We have sent a 6-digit verification code. Please enter it below to continue resetting your password.
            </p>

            {{-- Alert for verification code --}}
            @if(isset($alert_code))
                <script>
                    alert("Your verification code is: " + @json($alert_code));
                </script>
            @endif

            <form method="POST" action="{{ route('password.verify') }}" novalidate class="space-y-6">
                @csrf

                {{-- Hidden nonce --}}
                <input type="hidden" name="nonce" value="{{ $nonce ?? '' }}">

                {{-- Verification Code --}}
                <div class="mb-6">
                    <label for="code" class="block text-sm font-medium text-gray-700">Verification Code</label>
                    <input id="code" name="code" type="text" required autofocus maxlength="6" pattern="^\d{6}$"
                        placeholder="Enter 6-digit code"
                        class="mt-2 block w-full rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 px-4 py-3 text-lg tracking-widest text-center shadow-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-all duration-300" />
                    @error('code')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between">
                    <a href="{{ route('password.request') }}"
                        class="text-sm font-medium text-gray-600 hover:text-black transition-colors">
                        Resend Code
                    </a>
                    <button type="submit"
                        class="px-6 py-3 rounded-xl bg-black text-white font-semibold shadow-md hover:bg-gray-800 focus:outline-none focus:ring-4 focus:ring-gray-400 transition-all duration-300">
                        Verify Code
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>
