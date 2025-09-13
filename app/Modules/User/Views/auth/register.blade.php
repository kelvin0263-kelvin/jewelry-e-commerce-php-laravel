@extends('layouts.auth')
@section('content')
    <div class="max-w-md mx-auto">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Create your account</h1>
        <p class="text-sm text-gray-500 mb-8">Join us and explore our jewelry collection.</p>

        <form method="POST" action="{{ route('register') }}" class="space-y-6" novalidate>
            @csrf

            {{-- Full name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Full name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name"
                       class="mt-2 block w-full rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 px-4 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-all duration-300" />
                @error('name')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
                       class="mt-2 block w-full rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 px-4 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-all duration-300" />
                @error('email')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input id="password" name="password" type="password" required autocomplete="new-password"
                       class="mt-2 block w-full rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 px-4 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-all duration-300" />
                @error('password')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                       class="mt-2 block w-full rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 px-4 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-all duration-300" />
                @error('password_confirmation')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between mt-6">
                <a href="{{ route('login') }}" 
                   class="px-5 py-2 rounded-xl bg-black text-white text-sm font-medium shadow-md hover:bg-gray-800 transition-all duration-300">
                    Already registered?
                </a>
                <button type="submit" 
                    class="px-6 py-3 rounded-xl bg-black text-white font-semibold shadow-md hover:bg-gray-800 focus:outline-none focus:ring-4 focus:ring-gray-400 transition-all duration-300">
                    Register
                </button>
            </div>
        </form>
    </div>
@endsection
