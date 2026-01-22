{{-- 
Author: Miko Tan See Qian
Date: 2025-09-15 
--}}
@extends('layouts.auth')

@section('content')
    <div
        class="flex items-center justify-center min-h-screen bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 px-4">
        <div
            class="w-full max-w-md p-8 bg-white rounded-2xl shadow-2xl border border-gray-200 transform transition duration-500 hover:scale-[1.02]">

            {{-- Heading --}}
            <h1 class="text-3xl font-extrabold text-gray-900 text-center mb-2 tracking-wide">
                Welcome Back
            </h1>
            <p class="text-center text-gray-500 mb-8 text-sm">
                Please sign in to continue
            </p>

            {{-- Status Message --}}
            @if ($errors->any())
                <div class="p-3 mb-3 text-sm text-red-800 bg-red-100 rounded-lg">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('status'))
                <div class="p-3 mb-3 text-sm text-green-800 bg-green-100 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input id="email" name="email" type="email" required autofocus autocomplete="username"
                            class="pl-10 pr-4 py-3 w-full rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 shadow-sm focus:ring-2 focus:ring-black focus:border-black transition-all duration-300" />
                    </div>
                </div>

                {{-- Password --}}
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input id="password" name="password" type="password" required autocomplete="current-password"
                            class="pl-10 pr-4 py-3 w-full rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 shadow-sm focus:ring-2 focus:ring-black focus:border-black transition-all duration-300" />
                    </div>
                </div>

                {{-- Remember Me & Forgot Password --}}
                <div class="mb-6 flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center text-sm text-gray-700">
                        <input id="remember_me" name="remember" type="checkbox"
                            class="h-4 w-4 rounded border-gray-400 text-black focus:ring-black">
                        <span class="ml-2">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-sm font-medium text-gray-600 hover:text-black transition-colors duration-300 underline">
                            Forgot password?
                        </a>
                    @endif
                </div>

                {{-- Login button --}}
                <div class="flex justify-center">
                    <button type="submit" id="login-btn"
                        class="w-full py-3 rounded-xl bg-black text-white font-semibold text-lg shadow-md hover:shadow-xl hover:bg-gray-800 focus:outline-none focus:ring-4 focus:ring-gray-400 transition-all duration-300">
                        Log in
                    </button>
                </div>
            </form>

            {{-- Footer --}}
            <p class="mt-8 text-center text-sm text-gray-600">
                Don't have an account?
                <a href="{{ route('register') }}" class="font-medium text-black hover:underline">
                    Register Now!
                </a>
            </p>
        </div>
    </div>
@endsection

