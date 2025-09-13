@extends('layouts.auth')

@section('content')
    <div class="max-w-md mx-auto">
        {{-- Title --}}
        <h2 class="text-3xl font-extrabold text-gray-900 mb-2">{{ __('Reset Password') }}</h2>
        <p class="mb-8 text-sm text-gray-600">
            {{ __('Enter your new password below to complete the reset process.') }}
        </p>

        {{-- Form --}}
        <form method="POST" action="{{ route('password.update.custom') }}" class="space-y-6" novalidate>
            @csrf

            <input type="hidden" name="email" value="{{ old('email', request('email')) }}">

            {{-- New Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input id="password"
                       type="password"
                       name="password"
                       required
                       minlength="8"
                       autocomplete="new-password"
                       placeholder="Enter new password"
                       class="mt-2 block w-full rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 px-4 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-all duration-300" />
                @error('password')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input id="password_confirmation"
                       type="password"
                       name="password_confirmation"
                       required
                       placeholder="Re-enter new password"
                       class="mt-2 block w-full rounded-xl border border-gray-300 bg-white text-gray-900 placeholder-gray-400 px-4 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition-all duration-300" />
                @error('password_confirmation')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <div class="flex justify-end">
                <button type="submit"
                        class="px-6 py-3 rounded-xl bg-black text-white font-semibold shadow-md hover:bg-gray-800 focus:outline-none focus:ring-4 focus:ring-gray-400 transition-all duration-300">
                    {{ __('Reset Password') }}
                </button>
            </div>
        </form>
    </div>
@endsection
