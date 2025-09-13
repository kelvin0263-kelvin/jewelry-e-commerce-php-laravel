@extends('layouts.auth')
@section('content')
    <div class="mb-4 text-sm text-gray-700">
        {{ __('Thanks for signing up! Please verify your email address using the link we sent. If you didn\'t receive it, you can request another email below.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-lg bg-green-50 p-3 text-green-800 border border-green-200">
            {{ __('We\'ve sent you a new verification link. Please check your inbox.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="px-5 py-2 rounded-lg bg-black text-white font-medium hover:bg-gray-800">
                {{ __('Resend Verification Email') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-gray-600 hover:text-black">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
@endsection
