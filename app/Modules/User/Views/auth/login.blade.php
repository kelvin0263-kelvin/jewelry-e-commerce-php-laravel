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
            <div id="login-status"></div>

            <form id="login-form" class="space-y-6" novalidate>
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
                Don’t have an account?
                <a href="{{ route('register') }}" class="font-medium text-black hover:underline">
                    Register Now!
                </a>
            </p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const loginForm = document.getElementById('login-form');
            const loginStatus = document.getElementById('login-status');

            const externalApi = "http://127.0.0.1:8000/api/login"; // external first
            const internalApi = "/api/login"; // fallback

            loginForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = {
                    email: document.getElementById('email').value,
                    password: document.getElementById('password').value,
                    remember: document.getElementById('remember_me').checked,
                };

                loginStatus.innerHTML = `<div class="p-3 mb-3 text-sm text-gray-600 bg-gray-100 rounded-lg">⏳ Logging in...</div>`;

                // Try external API first
                fetch(externalApi, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify(formData),
                })
                    .then(res => {
                        if (!res.ok) throw new Error("External login failed");
                        return res.json();
                    })
                    .then(response => {
                        loginStatus.innerHTML = `<div class="p-3 mb-3 text-sm text-green-800 bg-green-100 rounded-lg">✅ ${response.message || 'Login successful!'}</div>`;
                        console.log("External login success:", response);
                        // redirect after login based on role
                        const redirectUrl = (response && response.redirect)
                            ? response.redirect
                            : ((response && response.user && response.user.is_admin)
                                ? "{{ route('admin.dashboard') }}"
                                : "{{ route('home') }}");
                        window.location.href = redirectUrl || "{{ route('home') }}";
                    })
                    .catch(err => {
                        loginStatus.innerHTML = `<div class="p-3 mb-3 text-sm text-yellow-800 bg-yellow-50 rounded-lg">⚠️ External login failed, trying internal...</div>`;
                        console.warn("External failed:", err.message);

                        // Try internal API
                        fetch(internalApi, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify(formData),
                        })
                            .then(res => {
                                if (!res.ok) throw new Error("Internal login failed");
                                return res.json();
                            })
                            .then(response => {
                                loginStatus.innerHTML = `<div class="p-3 mb-3 text-sm text-green-800 bg-green-100 rounded-lg">✅ ${response.message || 'Login successful!'}</div>`;
                                console.log("Internal login success:", response);
                                const redirectUrl = (response && response.redirect)
                                    ? response.redirect
                                    : ((response && response.user && response.user.is_admin)
                                        ? "{{ route('admin.dashboard') }}"
                                        : "{{ route('home') }}");
                                window.location.href = redirectUrl || "{{ route('home') }}";
                            })
                            .catch(err2 => {
                                loginStatus.innerHTML = `<div class="p-3 mb-3 text-sm text-red-800 bg-red-100 rounded-lg">❌ Login failed: ${err2.message}</div>`;
                                console.error("Both logins failed:", err2);
                            });
                    });
            });
        });
    </script>
@endsection
