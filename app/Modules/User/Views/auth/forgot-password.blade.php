@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto">
        {{-- Title --}}
        <h2 class="text-3xl font-extrabold text-gray-900 mb-2">{{ __('Forgot Password') }}</h2>
        <p class="mb-8 text-sm text-gray-600">
            {{ __('Forgot your password? Enter your email and we will send you a 6-digit verification code.') }}
        </p>

        {{-- Form --}}
        <form method="POST" action="{{ route('password.email') }}" novalidate class="space-y-6">
            @csrf

            {{-- Email --}}
            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" 
                       name="email" 
                       type="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       maxlength="255"
                       pattern="^[\w\.-]+@[\w\.-]+\.[A-Za-z]{2,}$"
                       placeholder="you@example.com"
                       class="mt-2 block w-full rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 px-4 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-all duration-300" />
                @error('email')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between">
                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-black transition-colors underline">
                    Back to login
                </a>
                <button type="submit" 
                    class="px-6 py-3 rounded-xl bg-black text-white font-semibold shadow-md hover:bg-gray-800 focus:outline-none focus:ring-4 focus:ring-gray-400 transition-all duration-300">
                    {{ __('Send Code') }}
                </button>
            </div>
        </form>
    </div>
@endsection
