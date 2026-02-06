<header x-data="{ mobileOpen: false }" @keydown.window.escape="mobileOpen = false"
    class="w-full bg-white shadow-md relative">
    @php
        // Derive cart count for guests and logged-in users (supports both cart implementations)
        $cartCount = 0;
        try {
            $cartCount += \App\Modules\Cart\Models\CartItem::where('user_id', auth()->id())->sum('quantity');
        } catch (\Throwable $e) {
        }
    @endphp
    <!-- Inline SVG sprite for icons -->
    <svg xmlns="http://www.w3.org/2000/svg" style="position:absolute;width:0;height:0;overflow:hidden" aria-hidden="true"
        focusable="false">
        <symbol id="icon-account" viewBox="0 0 24 24">
            <path fill="currentColor"
                d="M12 12c2.761 0 5-2.239 5-5S14.761 2 12 2 7 4.239 7 7s2.239 5 5 5Zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5Z" />
        </symbol>
        <symbol id="icon-bag" viewBox="0 0 24 24">
            <path d="M6 7h12l-1 12H7L6 7z" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linejoin="round" />
            <path d="M9 7a3 3 0 0 1 6 0" fill="none" stroke="currentColor" stroke-width="2" />
        </symbol>
        <symbol id="icon-heart" viewBox="0 0 24 24">
            <path fill="black"
                d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 6 4 4 6.5 4 8.24 4 10 5.5 12 7.5 14 5.5 15.76 4 17.5 4 20 4 22 6 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
        </symbol>
    </svg>
    <div class="max-w-7xl mx-auto flex flex-col items-center relative">
        {{-- Logo + mobile top bar --}}
        <div
            class="w-full items-center px-4 sm:px-6 py-4 md:py-6 gap-4 grid grid-cols-[auto_1fr_auto] md:flex md:justify-center">
            <div class="flex items-center md:hidden">
                <button type="button"
                    class="p-2 rounded-md border border-gray-200 text-gray-700"
                    aria-label="Open navigation menu" @click="mobileOpen = true">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <div class="flex justify-center md:justify-center">
                <a href="{{ url('/') }}"
                    class="text-2xl sm:text-3xl font-serif tracking-widest text-gray-800 inline-block relative px-2 pb-1
                          after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-black
                          after:transition-all after:duration-300 hover:after:w-full">
                    TIFFANY REPLICA
                </a>
            </div>

            {{-- Mobile quick actions --}}
            <div class="flex items-center justify-end space-x-2 md:hidden">
                <a href="{{ route('wishlist.index') }}" aria-label="Wishlist"
                    class="inline-flex items-center p-2 text-gray-800 hover:text-black">
                    <svg class="h-5 w-5 fill-current" aria-hidden="true" focusable="false">
                        <use href="#icon-heart"></use>
                    </svg>
                </a>
                <a href="{{ route('cart.index') }}" aria-label="Shopping Bag"
                    class="inline-flex items-center p-2 text-gray-800 hover:text-black relative">
                    <svg class="h-5 w-5 fill-current" aria-hidden="true" focusable="false">
                        <use href="#icon-bag"></use>
                    </svg>
                    @if (($cartCount ?? 0) > 0)
                        <span
                            class="absolute -top-0.5 -right-0.5 h-4 w-4 rounded-full bg-teal-400 text-white text-[10px] leading-4 text-center">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>
                @guest
                    <a href="{{ route('login') }}" aria-label="Account"
                        class="inline-flex items-center p-2 text-gray-800 hover:text-black">
                        <svg class="h-5 w-5 fill-current" aria-hidden="true" focusable="false">
                            <use href="#icon-account"></use>
                        </svg>
                    </a>
                @endguest
                @auth
                    <a href="{{ route('profile.show') }}" aria-label="Account"
                        class="inline-flex items-center p-2 text-gray-800 hover:text-black">
                        <svg class="h-5 w-5 fill-current" aria-hidden="true" focusable="false">
                            <use href="#icon-account"></use>
                        </svg>
                    </a>
                @endauth
            </div>
        </div>

        {{-- Navigation Row (desktop) --}}
        <div class="hidden md:grid w-full relative grid-cols-3 items-center">
            <div class="w-20 justify-self-start flex items-center">
                <a href="{{ route('wishlist.index') }}" aria-label="Wishlist"
                    class="inline-flex items-center px-2 pb-3 relative text-sm font-medium transition 
                            text-black after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-black 
                            after:transition-all after:duration-300 hover:after:w-full 
                            focus:outline-none focus-visible:ring-2 focus-visible:ring-black/40 rounded-sm">
                    <svg class="h-5 w-5 transition-transform duration-200 will-change-transform fill-current"
                        aria-hidden="true" focusable="false">
                        <use href="#icon-heart"></use>
                    </svg>
                </a>
                <a href="{{ route('cart.index') }}" aria-label="Shopping Bag"
                    class="inline-flex items-center px-2 pb-3 relative text-sm font-medium text-black transition 
                       after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-black 
                       after:transition-all after:duration-300 hover:after:w-full hover:text-black 
                       focus:outline-none focus-visible:ring-2 focus-visible:ring-black/40 rounded-sm">
                    <svg class="h-5 w-5 transition-transform duration-200 will-change-transform fill-current"
                        aria-hidden="true" focusable="false">
                        <use href="#icon-bag"></use>
                    </svg>
                    @if (($cartCount ?? 0) > 0)
                        <span
                            class="absolute -top-0.5 -right-0.5 h-4 w-4 rounded-full bg-teal-400 text-white text-[10px] leading-4 text-center">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>
            </div>

            {{-- Main Navigation --}}
            <ul class="relative flex space-x-10 text-sm font-medium text-gray-800 justify-self-center whitespace-nowrap">
                <li class="group relative">
                    <a href="{{ url('/') }}"
                        class="inline-block px-2 pb-3 relative transition
                              after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-black
                              after:transition-all after:duration-300 hover:after:w-full
                              {{ request()->routeIs('dashboard') ? 'font-bold text-black after:w-full' : '' }}">
                        Home
                    </a>
                </li>

                {{-- Products Dropdown --}}
                <li class="group relative">
                    <a href="{{ route('products.index') }}"
                        class="inline-block px-2 pb-3 relative transition
                              after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-black
                              after:transition-all after:duration-300 hover:after:w-full
                              {{ request()->routeIs('products.*') ? 'font-bold text-black after:w-full' : '' }}">
                        Products
                    </a>

                    {{-- Full-width dropdown (fixed to viewport) --}}
                    <div class="fixed inset-x-0 hidden bg-white shadow-xl border-t border-gray-200 group-hover:block animate-fadeSlide z-40 pt-10 pb-12 mt-[10px]"
                        style="top: var(--navH, 100px); min-height: calc(100vh - var(--navH, 86px));">

                        {{-- Inner container keeps content centered --}}
                        <div class="max-w-7xl mx-auto px-8 py-10">
                            <div class="grid grid-cols-1 lg:grid-cols-[1fr_1fr_1fr_1.6fr] gap-10 lg:gap-16 xl:gap-20">

                                {{-- Column 1 --}}
                                <div>
                                    <h3 class="text-gray-900 font-semibold mb-3">Categories</h3>
                                    <ul class="space-y-2 text-gray-600">
                                        <li><a href="#"class="hover:underline transition duration-200"
                                                class="hover:underline transition duration-200"
                                                class="hover:underline transition duration-200">Engagement Rings</a>
                                        </li>
                                        <li><a href="#"class="hover:underline transition duration-200"
                                                class="hover:underline transition duration-200"
                                                class="hover:underline transition duration-200">Wedding Bands</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200"
                                                class="hover:underline transition duration-200"
                                                class="hover:underline transition duration-200">Couple’s Rings</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200"
                                                class="hover:underline transition duration-200"
                                                class="hover:underline transition duration-200">Women’s Wedding
                                                Bands</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200"
                                                class="hover:underline transition duration-200"
                                                class="hover:underline transition duration-200">Men’s Wedding Bands</a>
                                        </li>
                                    </ul>
                                    <h3 class="text-gray-400 font-semibold mt-6 mb-2">Shop By Shape</h3>
                                    <ul class="space-y-2 text-gray-600">
                                        <li><a href="#"class="hover:underline transition duration-200"
                                                class="hover:underline transition duration-200">Round</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200"
                                                class="hover:underline transition duration-200">Oval</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200"
                                                class="hover:underline transition duration-200">Emerald</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200"
                                                class="hover:underline transition duration-200">Cushion</a></li>
                                    </ul>
                                </div>

                                {{-- Column 2 --}}
                                <div>
                                    <h3 class="text-gray-900 font-semibold mb-3">Collections</h3>
                                    <ul class="space-y-2 text-gray-600">
                                        <li><a href="#"class="hover:underline transition duration-200">The
                                                Tiffany® Setting</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">Tiffany
                                                True®</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">Tiffany
                                                Harmony®</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">Tiffany
                                                Soleste®</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">Tiffany
                                                Novo®</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">Jean
                                                Schlumberger</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">Tiffany
                                                Together</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">Tiffany
                                                Forever</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">T&CO.™</a>
                                        </li>
                                    </ul>
                                </div>

                                {{-- Column 3 --}}
                                <div>
                                    <h3 class="text-gray-900 font-semibold mb-3">The Tiffany Difference</h3>
                                    <ul class="space-y-2 text-gray-600">
                                        <li><a href="#"class="hover:underline transition duration-200">A Tiffany
                                                Ring</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">Tiffany
                                                Lifetime Service</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">Responsible
                                                Sourcing</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">How to
                                                Choose an Engagement Ring</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">How to
                                                Choose a Wedding Band</a></li>
                                    </ul>
                                </div>

                                {{-- Column 4 (single feature image) --}}
                                <div class="lg:pl-4">
                                    <h3 class="text-gray-900 font-semibold mb-3">Tiffany Watches</h3>
                                    <img src="{{ asset('images/nav2.jpeg') }}" alt="Tiffany Watches"
                                        class="w-full h-72 md:h-96 lg:h-[480px] object-cover rounded">
                                </div>

                            </div>
                        </div>
                    </div>
                </li>

                {{-- FAQ --}}
                <li class="group relative">
                    <a href="{{ route('aboutus') }}"
                        class="inline-block px-2 pb-3 relative transition
                              after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-black
                              after:transition-all after:duration-300 hover:after:w-full
                              {{ request()->routeIs('aboutus') ? 'font-bold text-black after:w-full' : '' }}">
                        About Us
                    </a>
                </li>
                
                <li class="group relative">

                @auth
                    <a href="{{ route('profile.show') }}"
                        class="inline-block px-2 pb-3 relative transition
                                after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-black
                                after:transition-all after:duration-300 hover:after:w-full 
                                {{ request()->routeIs('profile.*') ? 'font-bold text-black after:w-full' : '' }}">
                        Account
                    </a>
                @else
                    <a href="{{ route('login') }}" aria-label="Account"
                        class="inline-block px-2 pb-3 relative transition
                                after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-black
                                after:transition-all after:duration-300 hover:after:w-full">
                        Account
                    </a>
                @endauth
                </li>

                {{-- Support --}}
                <li class="group relative">
                        <a  href="{{ route('faq.index') }}"
                        class="inline-block px-2 pb-3 relative transition
              after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-black
              after:transition-all after:duration-300 hover:after:w-full">
                        Support
                    </a>

                    {{-- Full-width dropdown (like Products) --}}
                    <div class="fixed inset-x-0 hidden bg-white shadow-xl border-t border-gray-200 group-hover:block animate-fadeSlide z-40 pt-10 pb-12 mt-[10px]"
                        style="top: var(--navH, 100px); min-height: calc(100vh - var(--navH, 86px));">

                        {{-- Inner container keeps content centered --}}
                        <div class="max-w-7xl mx-auto px-8 py-10">
                            <div class="grid grid-cols-4 gap-8">

                                {{-- Column 1 --}}
                                <div>
                                    <h3 class="text-gray-900 font-semibold mb-3">Help Center</h3>
                                    <ul class="space-y-2 text-gray-600">
                                        <li><a
                                                href="{{ route('faq.index') }}"class="hover:underline transition duration-200">
                                                FAQ & Help</a></li>
                                        <li><a
                                                href="{{ route('self-service.index') }}"class="hover:underline transition duration-200">
                                                Self Service</a></li>
                                        <li><a
                                                href="{{ route('tickets.index') }}"class="hover:underline transition duration-200">
                                                Support Tickets</a></li>
                                        <li><a
                                                href="{{ route('chat-history.index') }}"class="hover:underline transition duration-200">
                                                Chat History</a></li>
                                    </ul>
                                </div>

                                {{-- Column 2 --}}
                                <div>
                                    <h3 class="text-gray-900 font-semibold mb-3">Live Support</h3>
                                    <ul class="space-y-2 text-gray-600">
                                        {{-- <li>
                                            <button onclick="startLiveChat()" class="text-600 hover:underline">
                                                Live Chat
                                            </button>
                                        </li> --}}
                                        <li><a href="#"class="hover:underline transition duration-200">Email
                                                Us</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">Call
                                                Center</a></li>
                                    </ul>
                                </div>

                                {{-- Column 3 --}}
                                <div>
                                    <h3 class="text-gray-900 font-semibold mb-3">Guides</h3>
                                    <ul class="space-y-2 text-gray-600">
                                        <li><a href="#"class="hover:underline transition duration-200">Getting
                                                Started</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">Account &
                                                Profile</a></li>
                                        <li><a
                                                href="#"class="hover:underline transition duration-200">Troubleshooting</a>
                                        </li>
                                        <li><a href="#"class="hover:underline transition duration-200">Billing &
                                                Payments</a></li>
                                    </ul>
                                </div>

                                {{-- Column 4 (images/visuals) --}}
                                {{-- Column 4 (single feature image) --}}
                                <div>
                                    <h3 class="text-gray-900 font-semibold mb-3">Tiffany Watches</h3>
                                    <img src="{{ asset('images/nav1.jpeg') }}" alt="Tiffany Watches"
                                        class="w-full h-64 md:h-72 lg:h-80 object-cover rounded">
                                </div>

                            </div>
                        </div>
                    </div>
                </li>

            </ul>


            @guest
                <div class="w-28 flex items-center justify-end space-x-3 justify-self-end">
                    <a href="{{ route('login') }}" aria-label="Account"
                        class="inline-flex items-center px-2 pb-3 relative text-sm font-medium text-black transition 
                  after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-black 
                  after:transition-all after:duration-300 hover:after:w-full hover:text-black 
                  focus:outline-none focus-visible:ring-2 focus-visible:ring-black/40 rounded-sm">

                        <svg class="h-5 w-5 transition-transform duration-200 will-change-transform fill-current"
                            aria-hidden="true" focusable="false">
                            <use href="#icon-account"></use>
                        </svg>
                    </a>
                </div>
            @endguest

            {{-- Customer (always right aligned) --}}
            @auth
                <div class="w-auto flex items-center justify-end space-x-3 justify-self-end">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button id="customerTrigger"
                                class="flex items-center space-x-1 px-2 pb-3 relative text-sm font-medium text-black 
                       transition after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 
                       after:bg-black after:transition-all after:duration-300 hover:after:w-full">
                                <svg class="h-5 w-5 transition-transform duration-200 will-change-transform fill-current"
                                    aria-hidden="true" focusable="false">
                                    <use href="#icon-account"></use>
                                </svg>
                                <span>Hello, {{ auth()->user()->name }}</span>
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            {{-- <x-dropdown-link :href="route('dashboard')">{{ __('Dashboard') }}</x-dropdown-link> --}}
                            <x-dropdown-link :href="route('profile.show')">{{ __('Profile') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('orders.index')">{{ __('Orders') }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            @endauth
        </div>
    </div>

    {{-- Mobile slide-out --}}
    <div x-show="mobileOpen" x-transition.opacity class="fixed inset-0 z-50 md:hidden" x-cloak>
        <button class="absolute inset-0 bg-black/50" aria-label="Close navigation menu" @click="mobileOpen = false"></button>
        <div x-show="mobileOpen" x-transition:enter="transition transform ease-out duration-200"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition transform ease-in duration-150" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="absolute right-0 top-0 h-full w-80 max-w-[80vw] bg-white shadow-2xl p-6 overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <span class="text-lg font-semibold text-gray-900">Menu</span>
                <button type="button" class="p-2 rounded-md border border-gray-200 text-gray-700"
                    aria-label="Close navigation menu" @click="mobileOpen = false">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="space-y-4 text-gray-800">
                <a href="{{ url('/') }}" class="block text-base font-medium hover:text-black">Home</a>
                <a href="{{ route('products.index') }}" class="block text-base font-medium hover:text-black">Products</a>
                <a href="{{ route('aboutus') }}" class="block text-base font-medium hover:text-black">About Us</a>
                <a href="{{ route('faq.index') }}" class="block text-base font-medium hover:text-black">Support</a>

                <div class="pt-4 mt-4 border-t border-gray-200 space-y-3">
                    <a href="{{ route('wishlist.index') }}" class="flex items-center justify-between">
                        <span>Wishlist</span>
                    </a>
                    <a href="{{ route('cart.index') }}" class="flex items-center justify-between">
                        <span>Cart</span>
                        @if (($cartCount ?? 0) > 0)
                            <span
                                class="inline-flex items-center justify-center h-5 min-w-[20px] rounded-full bg-teal-500 text-white text-xs px-2">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    @auth
                        <a href="{{ route('profile.show') }}" class="block">Profile</a>
                        <a href="{{ route('orders.index') }}" class="block">Orders</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left text-red-600">Log Out</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block">Login / Account</a>
                    @endauth
                </div>
            </nav>
        </div>
    </div>
</header>

