@extends('layouts.auth')

@section('content')
    <h2 class="text-2xl font-semibold text-black mb-4">{{ __('Confirm Your Password') }}</h2>
    <p class="text-gray-700 mb-6">{{ __('This is a secure area of the application. Please confirm your password before continuing.') }}</p>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6" novalidate>
        @csrf
        <div>
            <label for="password" class="block text-sm text-gray-700">{{ __('Password') }}</label>
            <input id="password" name="password" type="password" required minlength="8" autocomplete="current-password"
                   class="mt-1 block w-full rounded-lg border border-gray-300 bg-white text-black focus:border-gray-600 focus:ring-0 px-3 py-2" />
            @error('password')
                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2 rounded-lg bg-black text-white font-medium hover:bg-gray-800">{{ __('Confirm') }}</button>
        </div>
    </form>

    <div class="mt-6">
        <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-black">{{ __('Back to Login') }}</a>
    </div>
@endsection
