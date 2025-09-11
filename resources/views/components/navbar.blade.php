<header class="w-full bg-white shadow-md relative">
       <!-- Inline SVG sprite for icons -->
    <svg xmlns="http://www.w3.org/2000/svg" style="position:absolute;width:0;height:0;overflow:hidden" aria-hidden="true" focusable="false">
        <symbol id="icon-account" viewBox="0 0 24 24">
            <path fill="currentColor" d="M12 12c2.761 0 5-2.239 5-5S14.761 2 12 2 7 4.239 7 7s2.239 5 5 5Zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5Z"/>
        </symbol>
        <symbol id="icon-bag" viewBox="0 0 24 24">
            <path d="M6 7h12l-1 12H7L6 7z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            <path d="M9 7a3 3 0 0 1 6 0" fill="none" stroke="currentColor" stroke-width="2"/>
        </symbol>
    </svg>
    <div class="max-w-7xl mx-auto flex flex-col items-center relative">
        {{-- Logo --}}
        <div class="py-4">
            <a href="{{ url('/') }}"
                class="text-3xl font-serif tracking-widest text-gray-800 inline-block relative px-2 pb-1
                      after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-black
                      after:transition-all after:duration-300 hover:after:w-full">
                TIFFANY REPLICA
            </a>
        </div>

        {{-- Navigation Row --}}
        <div class="w-full relative flex items-center justify-between">
            <div class="w-20"></div>

            {{-- Main Navigation --}}
            <ul class="relative flex space-x-10 text-sm font-medium text-gray-800">
                <li>
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
                        style="top: var(--navH, 86px); min-height: calc(100vh - var(--navH, 86px));">

                        {{-- Inner container keeps content centered --}}
                        <div class="max-w-7xl mx-auto px-8 py-10">
                            <div class="grid grid-cols-1 lg:grid-cols-[1fr_1fr_1fr_1.6fr] gap-10 lg:gap-16 xl:gap-20">

                                {{-- Column 1 --}}
                                <div>
                                    <h3 class="text-gray-900 font-semibold mb-3">Categories</h3>
                                    <ul class="space-y-2 text-gray-600">
                                        <li><a href="#"class="hover:underline transition duration-200"class="hover:underline transition duration-200"class="hover:underline transition duration-200">Engagement Rings</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200"class="hover:underline transition duration-200"class="hover:underline transition duration-200">Wedding Bands</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200"class="hover:underline transition duration-200"class="hover:underline transition duration-200">Couple’s Rings</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200"class="hover:underline transition duration-200"class="hover:underline transition duration-200">Women’s Wedding Bands</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200"class="hover:underline transition duration-200"class="hover:underline transition duration-200">Men’s Wedding Bands</a></li>
                                    </ul>
                                    <h3 class="text-gray-400 font-semibold mt-6 mb-2">Shop By Shape</h3>
                                    <ul class="space-y-2 text-gray-600">
                                        <li><a href="#"class="hover:underline transition duration-200"class="hover:underline transition duration-200">Round</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200"class="hover:underline transition duration-200">Oval</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200"class="hover:underline transition duration-200">Emerald</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200"class="hover:underline transition duration-200">Cushion</a></li>
                                    </ul>
                                </div>

                                {{-- Column 2 --}}
                                <div>
                                    <h3 class="text-gray-900 font-semibold mb-3">Collections</h3>
                                    <ul class="space-y-2 text-gray-600">
                                        <li><a href="#"class="hover:underline transition duration-200">The Tiffany® Setting</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">Tiffany True®</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">Tiffany Harmony®</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">Tiffany Soleste®</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">Tiffany Novo®</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">Jean Schlumberger</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">Tiffany Together</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">Tiffany Forever</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">T&CO.™</a></li>
                                    </ul>
                                </div>

                                {{-- Column 3 --}}
                                <div>
                                    <h3 class="text-gray-900 font-semibold mb-3">The Tiffany Difference</h3>
                                    <ul class="space-y-2 text-gray-600">
                                        <li><a href="#"class="hover:underline transition duration-200">A Tiffany Ring</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">Tiffany Lifetime Service</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">Responsible Sourcing</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">How to Choose an Engagement Ring</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">How to Choose a Wedding Band</a></li>
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
                <li>
                    <a href="{{ route('faq.index') }}"
                        class="inline-block px-2 pb-3 relative transition
                              after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-black
                              after:transition-all after:duration-300 hover:after:w-full
                              {{ request()->routeIs('faq.*') ? 'font-bold text-black after:w-full' : '' }}">
                        FAQ
                    </a>
                </li>

                {{-- My Tickets --}}
                @auth
                    <li>
                        <a href="{{ route('tickets.index') }}"
                            class="inline-block px-2 pb-3 relative transition
                                  after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-black
                                  after:transition-all after:duration-300 hover:after:w-full
                                  {{ request()->routeIs('tickets.*') ? 'font-bold text-black after:w-full' : '' }}">
                            My Tickets
                        </a>
                    </li>
                @endauth

                {{-- Support --}}
                <li class="group relative">
                    <a href="#"
                        class="inline-block px-2 pb-3 relative transition
              after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-black
              after:transition-all after:duration-300 hover:after:w-full">
                        Support
                    </a>

                    {{-- Full-width dropdown (like Products) --}}
                    <div class="fixed inset-x-0 hidden bg-white shadow-xl border-t border-gray-200 group-hover:block animate-fadeSlide z-40 pt-10 pb-12 mt-[10px]"
                        style="top: var(--navH, 86px); min-height: calc(100vh - var(--navH, 86px));">

                        {{-- Inner container keeps content centered --}}
                        <div class="max-w-7xl mx-auto px-8 py-10">
                            <div class="grid grid-cols-4 gap-8">

                                {{-- Column 1 --}}
                                <div>
                                    <h3 class="text-gray-900 font-semibold mb-3">Help Center</h3>
                                    <ul class="space-y-2 text-gray-600">
                                        <li><a href="{{ route('faq.index') }}"class="hover:underline transition duration-200"> FAQ & Help</a></li>
                                        <li><a href="{{ route('self-service.index') }}"class="hover:underline transition duration-200"> Self Service</a></li>
                                        <li><a href="{{ route('tickets.index') }}"class="hover:underline transition duration-200"> Support Tickets</a></li>
                                        <li><a href="{{ route('chat-history.index') }}"class="hover:underline transition duration-200"> Chat History</a></li>
                                    </ul>
                                </div>

                                {{-- Column 2 --}}
                                <div>
                                    <h3 class="text-gray-900 font-semibold mb-3">Live Support</h3>
                                    <ul class="space-y-2 text-gray-600">
                                        <li>
                                            <button onclick="startLiveChat()" class="text-600 hover:underline">
                                                Live Chat
                                            </button>
                                        </li>
                                        <li><a href="#"class="hover:underline transition duration-200">Email Us</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">Call Center</a></li>
                                    </ul>
                                </div>

                                {{-- Column 3 --}}
                                <div>
                                    <h3 class="text-gray-900 font-semibold mb-3">Guides</h3>
                                    <ul class="space-y-2 text-gray-600">
                                        <li><a href="#"class="hover:underline transition duration-200">Getting Started</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">Account & Profile</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">Troubleshooting</a></li>
                                        <li><a href="#"class="hover:underline transition duration-200">Billing & Payments</a></li>
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
                @php
                    // Derive cart count for guests and logged-in users (supports both cart implementations)
                    $cartCount = 0;
                    try {
                        $sessionId = session()->getId();
                        $cartCount += \App\Modules\Product\Models\Cart::where('session_id', $sessionId)
                            ->when(auth()->check(), function ($q) {
                                $q->orWhere('user_id', auth()->id());
                            })
                            ->sum('quantity');
                    } catch (\Throwable $e) {}
                    try {
                        if (auth()->check()) {
                            $cartCount += \App\Modules\Cart\Models\CartItem::where('user_id', auth()->id())->sum('quantity');
                        }
                    } catch (\Throwable $e) {}
                @endphp
                <div class="w-28 flex items-center justify-end space-x-3">
                    <a href="{{ route('cart.index') }}" aria-label="Shopping Bag"
                       class="inline-flex items-center px-2 pb-3 relative text-sm font-medium text-black transition 
                       after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-black 
                       after:transition-all after:duration-300 hover:after:w-full hover:text-black 
                       focus:outline-none focus-visible:ring-2 focus-visible:ring-black/40 rounded-sm">
                        <svg class="h-5 w-5 transition-transform duration-200 will-change-transform fill-current"
                             aria-hidden="true" focusable="false">
                            <use href="#icon-bag"></use>
                        </svg>
                        @if(($cartCount ?? 0) > 0)
                            <span class="absolute -top-0.5 -right-0.5 h-4 w-4 rounded-full bg-teal-400 text-white text-[10px] leading-4 text-center">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
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
                @php
                    // Derive cart count for the authenticated user (supports both implementations)
                    $cartCount = 0;
                    try {
                        $cartCount += \App\Modules\Product\Models\Cart::where('user_id', auth()->id())->sum('quantity');
                    } catch (\Throwable $e) {}
                    try {
                        $cartCount += \App\Modules\Cart\Models\CartItem::where('user_id', auth()->id())->sum('quantity');
                    } catch (\Throwable $e) {}
                @endphp
                <div class="w-auto flex items-center justify-end space-x-3">
                    <a href="{{ route('cart.index') }}" aria-label="Shopping Bag"
                       class="inline-flex items-center px-2 pb-3 relative text-sm font-medium text-black transition 
                       after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-black 
                       after:transition-all after:duration-300 hover:after:w-full hover:text-black 
                       focus:outline-none focus-visible:ring-2 focus-visible:ring-black/40 rounded-sm">
                        <svg class="h-5 w-5 transition-transform duration-200 will-change-transform fill-current"
                             aria-hidden="true" focusable="false">
                            <use href="#icon-bag"></use>
                        </svg>
                        @if(($cartCount ?? 0) > 0)
                            <span class="absolute -top-0.5 -right-0.5 h-4 w-4 rounded-full bg-teal-400 text-white text-[10px] leading-4 text-center">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
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
                            <x-dropdown-link :href="route('dashboard')">{{ __('Dashboard') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                            <x-dropdown-link :href="route('orders.index')">{{ __('Orders') }}</x-dropdown-link>

                        </x-slot>
                    </x-dropdown>
                </div>
            @endauth
        </div>
    </div>
</header>
