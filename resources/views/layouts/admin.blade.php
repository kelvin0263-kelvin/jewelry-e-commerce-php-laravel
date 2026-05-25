<!DOCTYPE html>
<html class="admin-force-light">

<head>
    <meta charset="UTF-8" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img/apple-icon.png') }}" />
    <link rel="shortcut icon" href="{{ asset('images/smallIcon.jpg') }}" type="image/x-icon">
    <title>@yield('title')</title>
    <meta name="color-scheme" content="light">
    <script>
        document.documentElement.classList.remove('dark');
        new MutationObserver(() => {
            document.documentElement.classList.remove('dark');
        }).observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class']
        });
    </script>
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

    <style>
        html.admin-force-light,
        html.admin-force-light body {
            color-scheme: light;
            background-color: #f8f9fa !important;
        }

        html.admin-force-light body {
            color: #64748b !important;
            overflow-x: hidden;
        }

        html.admin-force-light main,
        html.admin-force-light main p,
        html.admin-force-light main span,
        html.admin-force-light main h1,
        html.admin-force-light main h2,
        html.admin-force-light main h3,
        html.admin-force-light main h4,
        html.admin-force-light main h5,
        html.admin-force-light main h6,
        html.admin-force-light main label,
        html.admin-force-light main td,
        html.admin-force-light main th {
            color: #344767 !important;
            opacity: 1 !important;
        }

        html.admin-force-light main .text-white {
            color: #ffffff !important;
        }

        html.admin-force-light main .text-emerald-500 {
            color: #10b981 !important;
            opacity: 1 !important;
        }

        html.admin-force-light main .text-red-600 {
            color: #dc2626 !important;
            opacity: 1 !important;
        }

        html.admin-force-light main .text-blue-500 {
            color: #3b82f6 !important;
            opacity: 1 !important;
        }

        html.admin-force-light main .text-orange-500 {
            color: #f97316 !important;
            opacity: 1 !important;
        }

        html.admin-force-light main .text-purple-500 {
            color: #a855f7 !important;
            opacity: 1 !important;
        }

        html.admin-force-light main .text-cyan-500 {
            color: #06b6d4 !important;
            opacity: 1 !important;
        }

        html.admin-force-light main .bg-white,
        html.admin-force-light main [class*="dark:bg-slate"] {
            background-color: #ffffff !important;
        }

        html.admin-force-light aside a,
        html.admin-force-light aside button {
            color: #344767 !important;
        }

        html.admin-force-light aside a span,
        html.admin-force-light aside button span {
            color: #344767 !important;
            opacity: 1 !important;
        }

        body.admin-sidebar-open {
            overflow: hidden;
        }
    </style>

    @stack('styles')
</head>

<body class="m-0 font-sans text-base antialiased font-normal leading-default bg-gray-50 text-slate-500">
    <div id="admin-sidebar-backdrop" class="fixed inset-0 z-[1030] hidden bg-slate-900/50 xl:hidden"></div>

    <header
        class="fixed inset-x-0 top-0 z-[1020] flex h-16 items-center justify-between bg-white/95 px-4 shadow-sm backdrop-blur xl:hidden">
        <button type="button" id="admin-sidebar-open"
            class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-700 shadow-sm"
            aria-controls="admin-sidebar" aria-expanded="false" aria-label="Open admin navigation">
            <i class="fas fa-bars text-base"></i>
        </button>
        <div class="min-w-0 flex-1 px-4 text-center">
            <span class="block truncate text-sm font-semibold text-slate-800">@yield('title', 'Admin')</span>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="inline-flex h-10 w-10 items-center justify-center">
            <img src="{{ asset('images/smallIcon.jpg') }}" class="h-8 w-8 rounded-full object-cover" alt="Admin" />
        </a>
    </header>

    <!-- <div class="absolute w-full bg-blue-500 dark:hidden min-h-75"></div> -->
    <!-- sidenav  -->
    <aside id="admin-sidebar"
        class="fixed top-0 left-0 h-dvh w-72 max-w-[86vw] bg-white shadow-xl rounded-r-2xl xl:rounded-2xl
         overflow-y-auto transition-transform duration-200 -translate-x-full
         xl:w-64 xl:translate-x-0 ease-nav-brand z-[1040]"
        aria-expanded="false">
        <div class="flex flex-col h-full">

            <div class="flex h-19 items-center justify-between px-4 xl:block xl:px-0">


                <img src="{{ asset('images/logo.png') }}"
                    class="h-full max-w-[12rem] object-contain transition-all duration-200 ease-nav-brand xl:w-full xl:max-w-none" alt="Logo" />

                <button type="button" id="admin-sidebar-close"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 xl:hidden"
                    aria-label="Close admin navigation">
                    <i class="fas fa-times text-sm"></i>
                </button>

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
                            href="{{ route('admin.inventory.dashboard') }}">
                            <div
                                class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                                <i
                                    class="relative top-0 text-sm leading-normal text-orange-500 ni ni-calendar-grid-58"></i>
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
                            <span class="ml-1 duration-300 opacity-100 pointer-events-none ease">Product
                                Management</span>
                        </a>
                    </li>

                    <li class="mt-0.5 w-full">
                        <a class="py-2.7 {{ request()->routeIs('ordermanagement.*') ? 'bg-blue-500/13 font-semibold text-slate-700' : 'dark:text-white dark:opacity-80' }} text-sm ease-nav-brand my-0 mx-2 flex items-center whitespace-nowrap rounded-lg px-4 transition-colors"
                            href="{{ route('ordermanagement.index') }}">
                            <div
                                class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                                <i
                                    class="relative top-0 text-sm leading-normal text-orange-500 ni ni-single-copy-04"></i>
                            </div>
                            <span class="ml-1 duration-300 opacity-100 pointer-events-none ease">Order Management</span>
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

                    {{-- <li class="w-full mt-4">
                    <h6 class="pl-6 ml-2 text-xs font-bold leading-tight uppercase dark:text-white opacity-60">Account
                        pages</h6>
                </li> --}}

                    <li class="mt-0.5 w-full">
                        <a class="py-2.7 {{ request()->routeIs('admin.register.*') ? 'bg-blue-500/13 font-semibold text-slate-700' : 'dark:text-white dark:opacity-80' }} text-sm ease-nav-brand my-0 mx-2 flex items-center whitespace-nowrap px-4 transition-colors"
                            href="{{ route('admin.register.form') }}">
                            <div
                                class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                                <i class="relative top-0 text-sm leading-normal text-slate-700 ni ni-single-02"></i>
                            </div>
                            <span class="ml-1 duration-300 opacity-100 pointer-events-none ease">Admin Management</span>
                        </a>
                    </li>


                    {{-- <li class="mt-0.5 w-full">
                    <a class=" dark:text-white dark:opacity-80 py-2.7 text-sm ease-nav-brand my-0 mx-2 flex items-center whitespace-nowrap px-4 transition-colors"
                        href="./pages/sign-in.html">
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                            <i class="relative top-0 text-sm leading-normal text-orange-500 ni ni-single-copy-04"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease">Sign In</span>
                    </a>
                </li> --}}

                    <li class="mt-0.5 w-full">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="dark:text-white dark:opacity-80 py-2.7 text-sm ease-nav-brand my-0 mx-2 flex items-center whitespace-nowrap px-4 transition-colors rounded-lg w-full text-left">
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
        class="relative min-h-screen pt-16 transition-all duration-200 ease-in-out xl:ml-68 xl:pt-0 rounded-xl ps ps--active-y">
        <div class="w-full max-w-full px-4 py-4 mx-auto md:px-6 md:py-6">
            @yield('content')

        </div>
    </main>

    <script>
        (() => {
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('admin-sidebar-backdrop');
            const openButton = document.getElementById('admin-sidebar-open');
            const closeButton = document.getElementById('admin-sidebar-close');
            const mobileQuery = window.matchMedia('(max-width: 1279px)');

            if (!sidebar || !backdrop || !openButton || !closeButton) {
                return;
            }

            const openSidebar = () => {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                document.body.classList.add('admin-sidebar-open');
                openButton.setAttribute('aria-expanded', 'true');
                sidebar.setAttribute('aria-expanded', 'true');
            };

            const closeSidebar = () => {
                if (!mobileQuery.matches) {
                    return;
                }

                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                document.body.classList.remove('admin-sidebar-open');
                openButton.setAttribute('aria-expanded', 'false');
                sidebar.setAttribute('aria-expanded', 'false');
            };

            openButton.addEventListener('click', openSidebar);
            closeButton.addEventListener('click', closeSidebar);
            backdrop.addEventListener('click', closeSidebar);
            sidebar.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', closeSidebar);
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeSidebar();
                }
            });
            mobileQuery.addEventListener('change', (event) => {
                if (!event.matches) {
                    backdrop.classList.add('hidden');
                    document.body.classList.remove('admin-sidebar-open');
                    openButton.setAttribute('aria-expanded', 'false');
                    sidebar.setAttribute('aria-expanded', 'true');
                } else {
                    closeSidebar();
                }
            });
        })();
    </script>

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
