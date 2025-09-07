<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="shortcut icon" href="{{ asset('images/smallIcon.jpg') }}" type="image/x-icon">
    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])


</head>

<body>

    <!-- Inline SVG sprite for icons -->
    <svg xmlns="http://www.w3.org/2000/svg" style="position:absolute;width:0;height:0;overflow:hidden" aria-hidden="true"
        focusable="false">
        <symbol id="icon-account" viewBox="0 0 24 24">
            <path fill="currentColor"
                d="M12 12c2.761 0 5-2.239 5-5S14.761 2 12 2 7 4.239 7 7s2.239 5 5 5Zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5Z" />
        </symbol>
    </svg>

    <header id="mainNav" class="fixed top-0 left-0 w-full z-50 transition-colors duration-300 ease-in-out bg-white">

        <div class="max-w-7xl mx-auto flex flex-col items-center relative">
            {{-- Logo --}}
            <div class="py-4">
                <a id="logoText" href="{{ url('/') }}"
                    class="text-3xl font-serif tracking-widest transition-colors duration-300 text-white inline-block relative px-2 pb-1 after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-black after:transition-all after:duration-300 hover:after:w-full">
                    TIFFANY REPLICA
                </a>

            </div>

            {{-- Navigation Row --}}
            <div class="w-full relative flex items-center justify-between">

                {{-- Left spacer (optional, keep empty or put icons like search/location) --}}
                <div class="w-20"></div>

                {{-- Main Navigation --}}
                <ul id="navLinks"
                    class="relative flex space-x-10 text-sm font-medium text-white transition-colors duration-300">

                    <li>
                        <a href="{{ route('home') }}"
                            class="inline-block px-2 pb-3 relative transition
          after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-black
          after:transition-all after:duration-300 hover:after:w-full">
                            Home
                        </a>
                    </li>
                    {{-- LOVE & ENGAGEMENT --}}
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
                                <div
                                    class="grid grid-cols-1 lg:grid-cols-[1fr_1fr_1fr_1.6fr] gap-10 lg:gap-16 xl:gap-20">

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
                                                    class="hover:underline transition duration-200">Wedding Bands</a>
                                            </li>
                                            <li><a href="#"class="hover:underline transition duration-200"
                                                    class="hover:underline transition duration-200"
                                                    class="hover:underline transition duration-200">Couple’s Rings</a>
                                            </li>
                                            <li><a href="#"class="hover:underline transition duration-200"
                                                    class="hover:underline transition duration-200"
                                                    class="hover:underline transition duration-200">Women’s Wedding
                                                    Bands</a></li>
                                            <li><a href="#"class="hover:underline transition duration-200"
                                                    class="hover:underline transition duration-200"
                                                    class="hover:underline transition duration-200">Men’s Wedding
                                                    Bands</a></li>
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
                                            <li><a
                                                    href="#"class="hover:underline transition duration-200">T&CO.™</a>
                                            </li>
                                        </ul>
                                    </div>

                                    {{-- Column 3 --}}
                                    <div>
                                        <h3 class="text-gray-900 font-semibold mb-3">The Tiffany Difference</h3>
                                        <ul class="space-y-2 text-gray-600">
                                            <li><a href="#"class="hover:underline transition duration-200">A
                                                    Tiffany Ring</a></li>
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

                    {{-- Other nav items --}}


                    <li class="group relative">
                        <a href="{{ route('faq.index') }}"
                            class="inline-block px-2 pb-3 relative transition
                          after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-black
                          after:transition-all after:duration-300 hover:after:w-full 
                          {{ request()->routeIs('faq.*') ? 'font-bold text-black after:w-full' : '' }}">
                            FAQ
                        </a>
                    </li>
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

                    <li class="group relative">
                        <a href="#"
                            class="inline-block px-2 pb-3 relative transition
              after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-black
              after:transition-all after:duration-300 group-hover:after:w-full">
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
                                            <li><a
                                                    href="{{ route('faq.index') }}"class="hover:underline transition duration-200">
                                                    FAQ & Help</a></li>
                                            <li><a
                                                    href="{{ route('self-service.index') }}"class="hover:underline transition duration-200">
                                                    Self Service</a></li>
                                            <li><a
                                                    href="{{ route('tickets.index') }}"class="hover:underline transition duration-200">Support
                                                    Tickets</a></li>
                                            <li><a
                                                    href="{{ route('chat-history.index') }}"class="hover:underline transition duration-200">
                                                    Chat History</a></li>
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
                                            <li><a href="#"class="hover:underline transition duration-200">Email
                                                    Us</a></li>
                                            <li><a href="#"class="hover:underline transition duration-200"> Call
                                                    Center</a></li>
                                        </ul>
                                    </div>

                                    {{-- Column 3 --}}
                                    <div>
                                        <h3 class="text-gray-900 font-semibold mb-3">Guides</h3>
                                        <ul class="space-y-2 text-gray-600">
                                            <li><a href="#"class="hover:underline transition duration-200">Getting
                                                    Started</a></li>
                                            <li><a href="#"class="hover:underline transition duration-200">Account
                                                    & Profile</a></li>
                                            <li><a
                                                    href="#"class="hover:underline transition duration-200">Troubleshooting</a>
                                            </li>
                                            <li><a href="#"class="hover:underline transition duration-200">Billing
                                                    & Payments</a></li>
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

                {{-- Guest account icon (keeps center aligned) --}}
                @guest
                    <div class="w-20 flex justify-end">
                        <a id="customerTrigger" href="{{ route('login') }}" aria-label="Account"
                            class="inline-flex items-center px-2 pb-3 relative text-sm font-medium text-white transition 
                                  after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 after:bg-black 
                                  after:transition-all after:duration-300 hover:after:w-full hover:text-black 
                                  focus:outline-none focus-visible:ring-2 focus-visible:ring-black/40 rounded-sm">

                            <svg class="h-5 w-5 transition-transform duration-200 will-change-transform"
                                aria-hidden="true" focusable="false">
                                <use href="#icon-account"></use>
                            </svg>
                        </a>
                    </div>
                @endguest

                {{-- Customer (always right aligned) --}}
                @auth
                    <div class="w-auto flex justify-end">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button id="customerTrigger"
                                    class="flex items-center space-x-1 px-2 pb-3 relative text-sm font-medium text-white 
                       transition after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 
                       after:bg-black after:transition-all after:duration-300 hover:after:w-full">
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
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endauth
            </div>
        </div>
    </header>



    <div class="homepage-container-1" id="homepage">
        <div class="homepage-container-1-1">
            <br>
            <h1>Enchanting time with the métiers d'art</h1>
            <p>Craftsmen breathe life into a story.
            </p>
            <br><br>
        </div>
        <video src="{{ asset('videos/hero-video.mp4') }}" autoplay muted loop playsinline preload="auto"></video>
    </div>
    <!-- Engagement Rings wide image with caption -->
    <section id="engagement-rings" class="py-12 md:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 md:px-6">
            <div class="relative overflow-hidden">
                <img src="{{ asset('images/home6.webp') }}" alt="Engagement ring" class="w-full h-auto object-cover"
                    loading="lazy">
            </div>
            <div class="mt-8 text-center">
                <h2 class="text-3xl md:text-4xl font-serif text-gray-800">Engagement Rings</h2>
                <p class="mt-4 text-gray-500">Each Graff engagement ring is a remarkable masterpiece, thoughtfully
                    designed and expertly crafted to highlight the distinctive character of its diamond.</p>
                <a href="{{ route('products.index') }}"
                    class="inline-block mt-6 px-10 py-3 border border-[#c9b57b] text-[#8b7a41] tracking-widest text-sm md:text-base hover:bg-[#c9b57b] hover:text-white transition-colors duration-200">SHOP
                    NOW</a>
            </div>
        </div>
    </section>
    <!-- Fine Jewelry Collections split section -->
    <section class="relative w-full" id="fine-jewelry">
        <div class="grid grid-cols-1 md:grid-cols-2 min-h-[100vh] items-stretch">
            <div class="order-2 md:order-1 flex items-center justify-center bg-white py-16">
                <div class="px-8 md:px-12 lg:px-16 text-center md:text-left">
                    <h2 class="text-3xl md:text-4xl lg:text-5xl font-serif tracking-wide text-gray-800">Fine Jewelry
                        Collections</h2>
                    <p class="mt-4 text-gray-500 max-w-xl">From exceptional in scale to delicate in design, explore the
                        House's unrivaled diamond jewels.</p>
                    <a href="{{ route('products.index') }}"
                        class="inline-block mt-8 px-10 py-3 border border-[#c9b57b] text-[#8b7a41] tracking-widest text-sm md:text-base hover:bg-[#c9b57b] hover:text-white transition-colors duration-200">DISCOVER</a>
                </div>
            </div>
            <div class="order-1 md:order-2 relative bg-black">
                <img src="{{ asset('images/home1.webp') }}" alt="Fine jewelry diamonds"
                    class="absolute inset-0 w-full h-full object-cover object-center" loading="lazy">
            </div>
        </div>
        <div class="hidden md:block absolute inset-y-0 left-1/2 w-px bg-gray-200"></div>
    </section>
    <!-- Watch Collections split section -->
    <section class="relative w-full border-t border-[#c9b57b]" id="watch-collections">
        <div class="grid grid-cols-1 md:grid-cols-2 min-h-[100vh] items-stretch">
            <div class="order-1 relative bg-black">
                <img src="{{ asset('images/home2.avif') }}" alt="Watch collection"
                    class="absolute inset-0 w-full h-full object-cover object-center" loading="lazy">
            </div>
            <div class="order-2 flex items-center justify-center bg-white py-16">
                <div class="px-8 md:px-12 lg:px-16 text-center md:text-left">
                    <h2 class="text-3xl md:text-4xl lg:text-5xl font-serif tracking-wide text-gray-800">Watch
                        Collections</h2>
                    <p class="mt-4 text-gray-500 max-w-xl">Through its inimitable watches collections, the House of
                        Harry Winston reinvents the way to tell time.</p>
                    <a href="{{ route('products.index') }}"
                        class="inline-block mt-8 px-10 py-3 border border-[#c9b57b] text-[#8b7a41] tracking-widest text-sm md:text-base hover:bg-[#c9b57b] hover:text-white transition-colors duration-200">DISCOVER</a>
                </div>
            </div>
        </div>
        <div class="hidden md:block absolute inset-y-0 left-1/2 w-px bg-gray-200"></div>
    </section>



    <!-- The Tiffany Experience -->
    <section id="tiffany-experience" class="py-20 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-serif text-gray-800 text-center">The Tiffany Experience</h2>
            <div class="mt-14 grid grid-cols-1 md:grid-cols-3 gap-12 md:gap-16 items-start">
                <!-- Shipping & Returns -->
                <div class="text-center">
                    <div class="h-28 md:h-32 flex items-center justify-center">
                        <img src="{{ asset('images/home3.jpeg') }}" alt="Shipping and Returns"
                            class="h-full w-auto object-contain select-none" loading="lazy">
                    </div>
                    <h3 class="mt-8 text-2xl md:text-3xl font-serif text-gray-800 leading-tight">Shipping<br>& Returns
                    </h3>
                    <p class="mt-4 text-gray-500 max-w-xs mx-auto">Complimentary shipping and returns on all orders.
                    </p>
                    <a href="#"
                        class="inline-block mt-6 text-gray-800 hover:text-black tracking-widest text-xs border-b border-[#c9b57b] hover:border-black pb-1">LEARN
                        MORE</a>
                </div>

                <!-- At Your Service -->
                <div class="text-center">
                    <div class="h-28 md:h-32 flex items-center justify-center">
                        <img src="{{ asset('images/home4.jpeg') }}" alt="At Your Service"
                            class="h-full w-auto object-contain select-none" loading="lazy">
                    </div>
                    <h3 class="mt-8 text-2xl md:text-3xl font-serif text-gray-800 leading-tight">At Your<br>Service
                    </h3>
                    <p class="mt-4 text-gray-500 max-w-xs mx-auto">Our client care experts are always here to help.</p>
                    <a href="#"
                        class="inline-block mt-6 text-gray-800 hover:text-black tracking-widest text-xs border-b border-[#c9b57b] hover:border-black pb-1">CONTACT
                        US</a>
                </div>

                <!-- Iconic Blue Box -->
                <div class="text-center">
                    <div class="h-28 md:h-32 flex items-center justify-center">
                        <img src="{{ asset('images/home5.jpeg') }}" alt="Iconic Blue Box"
                            class="h-full w-auto object-contain select-none" loading="lazy">
                    </div>
                    <h3 class="mt-8 text-2xl md:text-3xl font-serif text-gray-800 leading-tight">Iconic<br>Blue Box
                    </h3>
                    <p class="mt-4 text-gray-500 max-w-xs mx-auto">Your purchase comes wrapped in our Blue Box
                        packaging.</p>
                    <a href="#"
                        class="inline-block mt-6 text-gray-800 hover:text-black tracking-widest text-xs border-b border-[#c9b57b] hover:border-black pb-1">EXPLORE</a>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer"><!--Footer-->
        <div class="container-footer">
            <div class="row"><!--Arrange All DIV IN ROW-->
                <div class="footer-col">
                    <h4>company</h4>
                    <ul>
                        <li><a href="../About-us/aboutus.html">about us</a></li>
                        <li><a href="../Services/termNcondition.html">privacy policy</a></li>

                    </ul>
                </div>
                <div class="footer-col">
                    <h4>get help</h4>
                    <ul>
                        <li><a href="../Services/service.html">FAQ</a></li>


                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="../Product-Page/product.html?filter=all">Renting</a></li>
                        <li><a href="../Product-Page/product.html?filter=all">Investing</a></li>

                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Follow Us</h4>
                    <div class="social-links"><!--Social Icon-->
                        <a href="https://www.facebook.com/nike/">
                            <svg style=" width: 16px;height:16px" aria-hidden="true" fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M512 256C512 114.6 397.4 0 256 0S0 114.6 0 256C0 376 82.7 476.8 194.2 504.5V334.2H141.4V256h52.8V222.3c0-87.1 39.4-127.5 125-127.5c16.2 0 44.2 3.2 55.7 6.4V172c-6-.6-16.5-1-29.6-1c-42 0-58.2 15.9-58.2 57.2V256h83.6l-14.4 78.2H287V510.1C413.8 494.8 512 386.9 512 256h0z" />
                            </svg></a>


                        <a href="https://twitter.com/adidas"> <svg style=" width: 16px;height:16px"
                                aria-hidden="true" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 512 512">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z" />
                            </svg></a>
                        <a href="https://www.instagram.com/puma/"><svg style=" width: 16px;height:16px"
                                aria-hidden="true" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 448 512">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z" />
                            </svg></a>




                        <a href="https://www.linkedin.com/company/under-armour"> <svg style=" width: 16px;height:16px"
                                aria-hidden="true" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 448 512">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M100.3 448H7.4V148.9h92.9zM53.8 108.1C24.1 108.1 0 83.5 0 53.8a53.8 53.8 0 0 1 107.6 0c0 29.7-24.1 54.3-53.8 54.3zM447.9 448h-92.7V302.4c0-34.7-.7-79.2-48.3-79.2-48.3 0-55.7 37.7-55.7 76.7V448h-92.8V148.9h89.1v40.8h1.3c12.4-23.5 42.7-48.3 87.9-48.3 94 0 111.3 61.9 111.3 142.3V448z" />
                            </svg>
                        </a>

                    </div>
                </div>
            </div>
        </div>
        <div class="Copyright"><!--Copy right-->
            <p>Copyright &COPY; 2024 Next.Chain All right reserved.</p>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/ethers@5.7.2/dist/ethers.umd.min.js"></script>
    <script src="app1.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const nav = document.getElementById("mainNav");
            const links = document.getElementById("navLinks");
            const logo = document.getElementById("logoText");
            const customer = document.getElementById("customerTrigger");

            function setSolid() {
                nav?.classList.add("bg-white", "shadow-md");
                nav?.classList.remove("bg-transparent");

                links?.classList.add("text-gray-800");
                links?.classList.remove("text-white");

                logo?.classList.add("text-gray-800");
                logo?.classList.remove("text-white");

                customer?.classList.add("text-gray-800");
                customer?.classList.remove("text-white");
            }

            function setTransparent() {
                nav?.classList.add("bg-transparent");
                nav?.classList.remove("bg-white", "shadow-md");

                links?.classList.add("text-white");
                links?.classList.remove("text-gray-800");

                logo?.classList.add("text-white");
                logo?.classList.remove("text-gray-800");

                customer?.classList.add("text-white");
                customer?.classList.remove("text-gray-800");
            }

            function handleScroll() {
                if (window.scrollY > 50) {
                    setSolid();
                } else {
                    setTransparent();
                }
            }

            // Scroll handling
            window.addEventListener("scroll", handleScroll, {
                passive: true
            });
            handleScroll();

            // 🔹 Hover entire nav bar
            nav.addEventListener("mouseenter", setSolid);
            nav.addEventListener("mouseleave", () => {
                if (window.scrollY <= 50) {
                    setTransparent();
                }
            });
        });
    </script>
</body>

</html>
