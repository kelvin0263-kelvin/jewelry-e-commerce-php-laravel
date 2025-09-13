@extends('layouts.app', ['noOuterBorder' => true])

@section('title', 'My Profile')

@section('content')

    <div class="py-8">
        <div class="max-w-4xl mx-auto space-y-10"> {{-- reduced spacing slightly --}}

            {{-- Page Header --}}
            <div class="text-center mb-6"> {{-- slightly smaller bottom margin --}}
                <h1 class="text-4xl font-bold text-black">{{ __('Profile') }}</h1>
                <p class="mt-2 text-gray-600">{{ __('Manage your account information') }}</p>
            </div>

            {{-- Profile Information --}}
            <div class="mb-6 bg-white border border-gray-300 rounded-2xl p-6 shadow-md"> {{-- reduced padding and shadow slightly
                --}}
                <h2 class="text-2xl font-semibold text-black mb-4">{{ __('Profile Information') }}</h2>
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Full Name') }}</label>
                        <input id="name" name="name" type="text"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-white text-black focus:border-sky-400 focus:ring-1 focus:ring-sky-400 px-3 py-2 transition"
                            value="{{ old('name', $user->name ?? '') }}" required autofocus>
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Email Address') }}</label>
                        <input id="email" name="email" type="email"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-white text-black focus:border-sky-400 focus:ring-1 focus:ring-sky-400 px-3 py-2 transition"
                            value="{{ old('email', $user->email ?? '') }}" required>
                        @error('email')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Gender --}}
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700">{{ __('Gender') }}</label>
                        <select id="gender" name="gender"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-white text-black focus:border-sky-400 focus:ring-1 focus:ring-sky-400 px-3 py-2 transition">
                            <option value="" disabled>{{ __('Select Gender') }}</option>
                            <option value="male" {{ old('gender', $user->gender ?? '') == 'male' ? 'selected' : '' }}>
                                {{ __('Male') }}
                            </option>
                            <option value="female" {{ old('gender', $user->gender ?? '') == 'female' ? 'selected' : '' }}>
                                {{ __('Female') }}
                            </option>
                        </select>
                        @error('gender')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Birthday --}}
                    <div>
                        <label for="birthday" class="block text-sm font-medium text-gray-700">Birthday</label>
                        <input id="birthday" name="birthday" type="date"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-white text-black focus:border-sky-400 focus:ring-1 focus:ring-sky-400 px-3 py-2 transition"
                            value="{{ old('birthday', $user->birthday ?? '') }}"
                            min="{{ \Carbon\Carbon::today()->subYears(100)->toDateString() }}"
                            max="{{ \Carbon\Carbon::today()->subYears(18)->toDateString() }}">
                        @error('birthday')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-2 rounded-lg bg-black text-white font-semibold hover:bg-sky-500 hover:text-black transition duration-300 shadow-sm">
                            {{ __('Update Profile') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Password Update --}}
            <div class="mb-6 bg-white border border-gray-300 rounded-2xl p-6 shadow-md">
                <h2 class="text-2xl font-semibold text-black mb-4">{{ __('Change Password') }}</h2>
                @if(session('status') === 'Password changed successfully!')
                    <div class="mb-3 rounded-lg bg-green-50 p-2 text-green-800 border border-green-200">
                        {{ __('Your password has been updated.') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password"
                            class="block text-sm font-medium text-gray-700">{{ __('Current Password') }}</label>
                        <input id="current_password" name="current_password" type="password"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-white text-black focus:border-sky-400 focus:ring-1 focus:ring-sky-400 px-3 py-2 transition">
                        @error('current_password', 'updatePassword')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password"
                            class="block text-sm font-medium text-gray-700">{{ __('New Password') }}</label>
                        <input id="password" name="password" type="password"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-white text-black focus:border-sky-400 focus:ring-1 focus:ring-sky-400 px-3 py-2 transition">
                        @error('password', 'updatePassword')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation"
                            class="block text-sm font-medium text-gray-700">{{ __('Confirm Password') }}</label>
                        <input id="password_confirmation" name="password_confirmation" type="password"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-white text-black focus:border-sky-400 focus:ring-1 focus:ring-sky-400 px-3 py-2 transition">
                        @error('password_confirmation')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-2 rounded-lg bg-black text-white font-semibold hover:bg-sky-500 hover:text-black transition duration-300 shadow-sm">
                            {{ __('Update Password') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Delete Account --}}
            <div class="mb-6 bg-white border border-gray-300 rounded-2xl p-6 shadow-md">
                <h2 class="text-2xl font-semibold text-black mb-3">{{ __('Delete Account') }}</h2>
                <p class="text-sm text-gray-700 mb-4">
                    {{ __('Once your account is deleted, all data will be permanently removed. Please confirm before proceeding.') }}
                </p>

                <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4">
                    @csrf
                    @method('DELETE')

                    <div>
                        <input id="delete_password" name="password" type="password"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-white text-black focus:border-red-500 focus:ring-1 focus:ring-red-500 px-3 py-2"
                            placeholder="Enter password to confirm">
                        @error('password', 'deleteAccount')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-2 rounded-lg bg-black text-white font-semibold hover:bg-sky-500 hover:text-black transition duration-300 shadow-sm">
                            {{ __('Delete Account') }} </button>
                    </div>
                </form>
            </div>



        </div>
    </div>
@endsection