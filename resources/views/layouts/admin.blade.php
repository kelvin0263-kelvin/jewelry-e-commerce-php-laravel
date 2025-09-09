
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/apple-icon.png') }}" />
        <link rel="shortcut icon" href="{{ asset('images/smallIcon.jpg') }}" type="image/x-icon">
    <title>@yield('title')</title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Nucleo Icons -->
    <link href="{{ asset('css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/nucleo-svg.css') }}" rel="stylesheet" />
    <!-- Popper -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <!-- Main Styling path= public/css/argon-dashboard-tailwind.css -->
    <link href="{{ asset('css/argon-dashboard-tailwind.css') }}" rel="stylesheet" />
    
    <!-- Vite Assets for Echo and real-time functionality -->
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    
    @stack('styles')
</head>

<body
    class="m-0 font-sans text-base antialiased font-normal dark:bg-slate-900 leading-default bg-gray-50 text-slate-500">
    <!-- <div class="absolute w-full bg-blue-500 dark:hidden min-h-75"></div> -->
    <!-- sidenav  -->
<aside
  class="fixed top-0 left-0 h-screen w-64 bg-white shadow-xl rounded-2xl 
         overflow-y-auto transition-transform duration-200 -translate-x-full 
         xl:translate-x-0 ease-nav-brand z-990"
  aria-expanded="false">
<div class="flex flex-col h-full">

        <div class="h-19">


                <img src="{{ asset('images/logo.png') }}"
     class="w-full h-full object-contain transition-all duration-200 ease-nav-brand"
     alt="Logo" />

        
        </div>

        <hr
            class="h-px mt-0 bg-transparent bg-gradient-to-r from-transparent via-black/40 to-transparent dark:bg-gradient-to-r dark:from-transparent dark:via-white dark:to-transparent" />

        <div class="items-center block w-auto max-h-screen overflow-auto h-sidenav grow basis-full">
            <ul class="flex flex-col pl-0 mb-0">
                <li class="mt-0.5 w-full">
                    <a class="py-2.7 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-500/13 font-semibold text-slate-700' : 'dark:text-white dark:opacity-80' }} text-sm ease-nav-brand my-0 mx-2 flex items-center whitespace-nowrap rounded-lg px-4 transition-colors"
                        href="{{ route('admin.dashboard') }}">
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                            <i class="relative top-0 text-sm leading-normal text-blue-500 ni ni-tv-2"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease">Dashboard</span>
                    </a>
                </li>

                <li class="mt-0.5 w-full">
                    <a class="py-2.7 {{ request()->routeIs('admin.inventory.*') ? 'bg-blue-500/13 font-semibold text-slate-700' : 'dark:text-white dark:opacity-80' }} text-sm ease-nav-brand my-0 mx-2 flex items-center whitespace-nowrap rounded-lg px-4 transition-colors"
                        href="{{ route('admin.inventory.index') }}">
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                            <i class="relative top-0 text-sm leading-normal text-orange-500 ni ni-calendar-grid-58"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease">Inventory</span>
                    </a>
                </li>

                <li class="mt-0.5 w-full">
                    <a class="py-2.7 {{ request()->routeIs('admin.product-management.*') ? 'bg-blue-500/13 font-semibold text-slate-700' : 'dark:text-white dark:opacity-80' }} text-sm ease-nav-brand my-0 mx-2 flex items-center whitespace-nowrap rounded-lg px-4 transition-colors"
                        href="{{ route('admin.product-management.index') }}">
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                            <i class="relative top-0 text-sm leading-normal text-blue-500 ni ni-shop"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease">Product Management</span>
                    </a>
                </li>

                <li class="mt-0.5 w-full">
                    <a class="py-2.7 {{ request()->routeIs('admin.customers.*') ? 'bg-blue-500/13 font-semibold text-slate-700' : 'dark:text-white dark:opacity-80' }} text-sm ease-nav-brand my-0 mx-2 flex items-center whitespace-nowrap rounded-lg px-4 transition-colors"
                        href="{{ route('admin.customers.index') }}">
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center fill-current stroke-0 text-center xl:p-2.5">
                            <i class="relative top-0 text-sm leading-normal text-emerald-500 ni ni-credit-card"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease">Customer</span>
                    </a>
                </li>

                <li class="mt-0.5 w-full">
                    <a class="py-2.7 {{ request()->routeIs('admin.reports.*') ? 'bg-blue-500/13 font-semibold text-slate-700' : 'dark:text-white dark:opacity-80' }} text-sm ease-nav-brand my-0 mx-2 flex items-center whitespace-nowrap rounded-lg px-4 transition-colors"
                        href="{{ route('admin.reports.product-performance') }}">
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                            <i class="relative top-0 text-sm leading-normal text-cyan-500 ni ni-app"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease">Reports</span>
                    </a>
                </li>

                <li class="mt-0.5 w-full">
                    <a class="py-2.7 {{ request()->routeIs('admin.chat.index') ? 'bg-blue-500/13 font-semibold text-slate-700' : 'dark:text-white dark:opacity-80' }} text-sm ease-nav-brand my-0 mx-2 flex items-center whitespace-nowrap rounded-lg px-4 transition-colors"
                        href="{{ route('admin.chat.index') }}">
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                            <i class="relative top-0 text-sm leading-normal text-purple-500 fas fa-comments"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease">Chat</span>
                    </a>
                </li>

                <li class="mt-0.5 w-full">
                    <a class="py-2.7 {{ request()->routeIs('admin.chat-queue.*') ? 'bg-blue-500/13 font-semibold text-slate-700' : 'dark:text-white dark:opacity-80' }} text-sm ease-nav-brand my-0 mx-2 flex items-center whitespace-nowrap rounded-lg px-4 transition-colors"
                        href="{{ route('admin.chat-queue.index') }}">
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                            <i class="relative top-0 text-sm leading-normal text-orange-500 fas fa-clock"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease">Chat Queue</span>
                    </a>
                </li>

                <li class="mt-0.5 w-full">
                    <a class="py-2.7 {{ request()->routeIs('admin.tickets.*') ? 'bg-blue-500/13 font-semibold text-slate-700' : 'dark:text-white dark:opacity-80' }} text-sm ease-nav-brand my-0 mx-2 flex items-center whitespace-nowrap rounded-lg px-4 transition-colors"
                        href="{{ route('admin.tickets.index') }}">
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                            <i class="relative top-0 text-sm leading-normal text-blue-500 fas fa-ticket-alt"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease">Support Tickets</span>
                    </a>
                </li>

                <li class="w-full mt-4">
                    <h6 class="pl-6 ml-2 text-xs font-bold leading-tight uppercase dark:text-white opacity-60">Account
                        pages</h6>
                </li>

                <li class="mt-0.5 w-full">
                    <a class=" dark:text-white dark:opacity-80 py-2.7 text-sm ease-nav-brand my-0 mx-2 flex items-center whitespace-nowrap px-4 transition-colors"
                        href="./pages/profile.html">
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                            <i class="relative top-0 text-sm leading-normal text-slate-700 ni ni-single-02"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease">Profile</span>
                    </a>
                </li>

                <li class="mt-0.5 w-full">
                    <a class=" dark:text-white dark:opacity-80 py-2.7 text-sm ease-nav-brand my-0 mx-2 flex items-center whitespace-nowrap px-4 transition-colors"
                        href="./pages/sign-in.html">
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                            <i class="relative top-0 text-sm leading-normal text-orange-500 ni ni-single-copy-04"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease">Sign In</span>
                    </a>
                </li>

                <li class="mt-0.5 w-full">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dark:text-white dark:opacity-80 py-2.7 text-sm ease-nav-brand my-0 mx-2 flex items-center whitespace-nowrap px-4 transition-colors rounded-lg w-full text-left">
                            <div
                                class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                                <i class="relative top-0 text-sm leading-normal text-cyan-500 ni ni-collection"></i>
                            </div>
                            <span class="ml-1 duration-300 opacity-100 pointer-events-none ease">Log out</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
</div>
 
    </aside>

    <!-- end sidenav -->
    <main
        class="relative h-full max-h-screen transition-all duration-200 ease-in-out xl:ml-68 rounded-xl ps ps--active-y">
        <div class="w-full px-6 py-6 mx-auto">
            @yield('content')

        </div>
    </main>


</body>
<!-- plugin for charts  -->
<script src="{{ asset('js/plugins/chartjs.min.js') }}" async></script>
<!-- plugin for scrollbar  -->
<script src="{{ asset('js/plugins/perfect-scrollbar.min.js') }}" async></script>
<!-- main script file  -->
<script src="{{ asset('js/argon-dashboard-tailwind.js') }}" async></script>

<!-- Echo configuration for real-time functionality -->
@vite(['resources/js/app.js'])



    @yield('scripts')

</html>
