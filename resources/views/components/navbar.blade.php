<header class="w-full bg-white shadow-md relative">
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
                    <a href="{{ route('dashboard') }}"
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

                    {{-- Dropdown content --}}
<div class="fixed inset-x-0 hidden bg-white shadow-xl border-t border-gray-200
            group-hover:block hover:block animate-fadeSlide z-40 pt-10 pb-12 mt-3"
     style="top: var(--navH, 86px); min-height: calc(100vh - var(--navH, 86px));">


                        <div class="max-w-7xl mx-auto px-8 py-10">
                            <div class="grid grid-cols-4 gap-8">
                                {{-- Example Column --}}
                                <div>
                                    <h3 class="text-gray-900 font-semibold mb-3">Categories</h3>
                                    <ul class="space-y-2 text-gray-600">
                                        <li><a href="#">Engagement Rings</a></li>
                                        <li><a href="#">Wedding Bands</a></li>
                                        <li><a href="#">Couple’s Rings</a></li>
                                    </ul>
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

    {{-- Dropdown content --}}
<div class="fixed inset-x-0 hidden bg-white shadow-xl border-t border-gray-200
            group-hover:block hover:block animate-fadeSlide z-40 pt-10 pb-12 mt-3"
     style="top: var(--navH, 86px); min-height: calc(100vh - var(--navH, 86px));">


        <div class="max-w-7xl mx-auto px-8 py-10 ">
            <div class="grid grid-cols-4 gap-8">
                {{-- Example Column --}}
                <div>
                    <h3 class="text-gray-900 font-semibold mb-3">Help Center</h3>
                    <ul class="space-y-2 text-gray-600">
                        <li><a href="{{ route('faq.index') }}">📚 FAQ & Help</a></li>
                        <li><a href="{{ route('self-service.index') }}">🛠️ Self Service</a></li>
                        <li><a href="{{ route('tickets.index') }}">🎫 Support Tickets</a></li>
                        <li><a href="{{ route('chat-history.index') }}">📝 Chat History</a></li>
                    </ul>
                </div>
                {{-- ...other columns --}}
            </div>
        </div>
    </div>
</li>

            </ul>

            {{-- Customer Dropdown --}}
            @auth
                <div class="w-20 flex justify-end">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center space-x-1 px-2 pb-3 relative text-sm font-medium text-gray-800 
                                           transition after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-0 
                                           after:bg-black after:transition-all after:duration-300 hover:after:w-full">
                                <span>Customer</span>
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
