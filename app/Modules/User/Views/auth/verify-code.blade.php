@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto">

    <h2 class="text-3xl font-extrabold text-gray-900 mb-2">{{ __('Verify Code') }}</h2>
    <p class="mb-6 text-sm text-gray-600">
        {{ __('We have sent a 6-digit verification code. Please enter it below to continue resetting your password.') }}
    </p>

    {{-- Alert for verification code --}}
    @if(isset($alert_code))
        <script>
            alert("Your verification code is: {{ $alert_code }}");
        </script>
    @endif

    <form method="POST" action="{{ route('password.verify') }}" novalidate class="space-y-6">
        @csrf

        {{-- Hidden nonce --}}
        <input type="hidden" name="nonce" value="{{ $nonce ??'' }}">

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
                {{ __('Verify Code') }}
            </button>
        </div>
    </form>
</div>
@endsection
