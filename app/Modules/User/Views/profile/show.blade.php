@extends('layouts.app', ['noOuterBorder' => true])

@section('title', 'My Profile')

@section('content')

    <div class="py-6 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto">

            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-lg">

                {{-- Profile Header --}}
                <div class="bg-gray-100 p-6 flex items-center space-x-5 border-b border-gray-200">
                    <div class="flex-shrink-0">
                        <img class="h-24 w-24 rounded-full border-2 border-gray-300 object-cover shadow-md"
                            src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=87CEEB&color=000000"
                            alt="{{ $user->name }}">
                    </div>
                    <div>
                        <h2 class="text-2xl font-extrabold text-gray-900">{{ $user->name }}</h2>
                        <p class="text-gray-600 text-sm">{{ $user->email }}</p>
                    </div>
                </div>

                {{-- Profile Information --}}
                <div class="p-6 space-y-6">
                    <h3 class="text-lg font-semibold text-sky-600 border-b border-gray-200 pb-2">
                        {{ __('Profile Information') }}
                    </h3>

                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                        {{-- Left Column --}}
                        <div>
                            <dt class="font-medium text-gray-700">{{ __('Full Name') }}</dt>
                            <dd class="text-gray-900">{{ $user->name }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-700">{{ __('Email Address') }}</dt>
                            <dd class="text-gray-900">{{ $user->email }}</dd>
                        </div>

                        {{-- Right Column --}}
                        <div>
                            <dt class="font-medium text-gray-700">{{ __('Gender') }}</dt>
                            <dd class="text-gray-900">{{ $user->gender ? ucfirst($user->gender) : '—' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-700">{{ __('Birthday') }}</dt>
                            <dd class="text-gray-900">{{ $user->birthday ? $user->birthday->format('d M Y') : '—' }}</dd>
                        </div>
                    </dl>

                    {{-- Actions --}}
                    <div class="flex justify-end pt-6 border-t border-gray-200">
                        <a href="{{ route('profile.edit') }}"
                            class="inline-flex items-center px-5 py-2.5 rounded-lg bg-black text-white font-medium hover:bg-sky-500 hover:text-black transition-all duration-300 shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-white group-hover:text-black"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5l3 3m-2-2L13 10l-4 1 1-4 6.5-6.5z" />
                            </svg>
                            {{ __('Edit Profile') }}
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection
