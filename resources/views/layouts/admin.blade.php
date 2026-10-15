<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        {{ $title ?? 'Dashboard' }} - Asset Management System
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @livewireStyles

</head>


<body class="min-h-screen bg-slate-50 text-slate-900">


    <!-- ======================================================== -->
    <!-- MOBILE OVERLAY -->
    <!-- ======================================================== -->

    <div
        id="sidebar-overlay"
        class="fixed inset-0 z-30 hidden bg-slate-900/40 backdrop-blur-sm lg:hidden"
        onclick="toggleSidebar()"
    ></div>


    <!-- ======================================================== -->
    <!-- SIDEBAR -->
    <!-- ======================================================== -->

    <aside
        id="sidebar"
        class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col border-r border-slate-200 bg-white transition-transform duration-300 lg:translate-x-0"
    >

        <!-- Logo -->

        <div class="flex h-16 items-center border-b border-slate-200 px-5">

            <div class="flex items-center gap-3">

                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-600 text-white shadow-sm">

                    <svg
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                        <path d="M8 5V3h8v2"/>
                        <path d="M8 12h8"/>
                    </svg>

                </div>


                <div>

                    <p class="text-sm font-bold leading-tight text-slate-900">
                        Asset Management
                    </p>

                    <p class="text-[11px] text-slate-400">
                        Management System
                    </p>

                </div>

            </div>

        </div>


        <!-- Navigation -->

        <nav class="flex-1 overflow-y-auto px-3 py-5">

            <!-- MAIN -->

            <p class="mb-2 px-3 text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400">
                Main
            </p>


            <a
                href="{{ route('dashboard') }}"
                class="mb-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                {{ request()->routeIs('dashboard')
                    ? 'bg-blue-50 text-blue-700'
                    : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
            >

                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <rect x="3" y="3" width="7" height="7"/>
                    <rect x="14" y="3" width="7" height="7"/>
                    <rect x="3" y="14" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/>
                </svg>

                <span>
                    Dashboard
                </span>

            </a>


            <!-- MASTER DATA -->

            <p class="mb-2 mt-7 px-3 text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400">
                Master Data
            </p>


            <!-- Asset -->

            <a
                href="{{ route('assets.index') }}"
                class="mb-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                {{ request()->routeIs('assets.*')
                    ? 'bg-blue-50 text-blue-700'
                    : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
            >

                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <rect x="3" y="4" width="18" height="14" rx="2"/>
                    <path d="M8 20h8"/>
                    <path d="M12 18v2"/>
                </svg>

                Asset
            </a>


            <!-- Category -->

            <a
                href="{{ route('asset-categories.index') }}"
                class="mb-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                {{ request()->routeIs('asset-categories.*')
                ? 'bg-blue-50 text-blue-700'
                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
            >

                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M4 6h16"/>
                    <path d="M4 12h16"/>
                    <path d="M4 18h16"/>
                </svg>

                Category
            </a>


            <!-- Location -->

            <a
                href="{{ route('locations.index') }}"
                class="mb-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                {{ request()->routeIs('locations.*')
                ? 'bg-blue-50 text-blue-700'
                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
            >

                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/>
                    <circle cx="12" cy="10" r="2.5"/>
                </svg>

                Location
            </a>


            <!-- Vendor -->

            <a
                href="{{ route('vendors.index') }}"
                class="mb-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                {{ request()->routeIs('vendors.*')
                    ? 'bg-blue-50 text-blue-700'
                    : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
            >

                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M4 21V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v16"/>
                    <path d="M9 21v-4h6v4"/>
                    <path d="M8 7h2"/>
                    <path d="M14 7h2"/>
                    <path d="M8 11h2"/>
                    <path d="M14 11h2"/>
                </svg>

                Vendor
            </a>


            <!-- TRANSACTIONS -->

            <p class="mb-2 mt-7 px-3 text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400">
                Transactions
            </p>


            <a
                href="#"
                class="mb-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-slate-900"
            >

                <span class="flex h-5 w-5 items-center justify-center text-sm">
                    ↔
                </span>

                Assignment

                <span class="ml-auto rounded-md bg-slate-100 px-1.5 py-0.5 text-[9px] font-semibold text-slate-400">
                    Soon
                </span>

            </a>


            <a
                href="#"
                class="mb-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-slate-900"
            >

                <span class="flex h-5 w-5 items-center justify-center text-sm">
                    ↩
                </span>

                Return Asset

                <span class="ml-auto rounded-md bg-slate-100 px-1.5 py-0.5 text-[9px] font-semibold text-slate-400">
                    Soon
                </span>

            </a>


            <a
                href="#"
                class="mb-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-slate-900"
            >

                <span class="flex h-5 w-5 items-center justify-center text-sm">
                    🔧
                </span>

                Maintenance

                <span class="ml-auto rounded-md bg-slate-100 px-1.5 py-0.5 text-[9px] font-semibold text-slate-400">
                    Soon
                </span>

            </a>


            <!-- REPORT -->

            <p class="mb-2 mt-7 px-3 text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400">
                Reports
            </p>


            <a
                href="#"
                class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-slate-900"
            >

                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="M4 19V5"/>
                    <path d="M4 19h16"/>
                    <path d="M8 16v-4"/>
                    <path d="M12 16V8"/>
                    <path d="M16 16v-7"/>
                </svg>

                Reports

                <span class="ml-auto rounded-md bg-slate-100 px-1.5 py-0.5 text-[9px] font-semibold text-slate-400">
                    Soon
                </span>

            </a>

        </nav>


        <!-- User -->

        <div class="border-t border-slate-200 p-3">

            <div class="flex items-center gap-3 rounded-lg bg-slate-50 px-3 py-2.5">

                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">

                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}

                </div>


                <div class="min-w-0 flex-1">

                    <p class="truncate text-xs font-semibold text-slate-800">
                        {{ auth()->user()->name ?? 'Administrator' }}
                    </p>

                    <p class="truncate text-[11px] text-slate-400">
                        Administrator
                    </p>

                </div>

            </div>

        </div>

    </aside>


    <!-- ======================================================== -->
    <!-- MAIN -->
    <!-- ======================================================== -->

    <div class="min-h-screen lg:pl-64">


        <!-- TOPBAR -->

        <header class="sticky top-0 z-20 h-16 border-b border-slate-200 bg-white/90 backdrop-blur">

            <div class="flex h-full items-center justify-between px-4 sm:px-6">

                <div class="flex items-center gap-3">

                    <button
                        onclick="toggleSidebar()"
                        type="button"
                        class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 lg:hidden"
                    >

                        <svg
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M4 6h16"/>
                            <path d="M4 12h16"/>
                            <path d="M4 18h16"/>
                        </svg>

                    </button>


                    <div>

                        <p class="text-sm font-semibold text-slate-800">
                            {{ $pageTitle ?? 'Dashboard' }}
                        </p>

                        <p class="hidden text-[11px] text-slate-400 sm:block">
                            Asset Management System
                        </p>

                    </div>

                </div>


                <div class="flex items-center gap-2">


                    <!-- Notification -->

                    <button
                        type="button"
                        class="relative rounded-lg p-2 text-slate-500 transition hover:bg-slate-100"
                    >

                        <svg
                            width="19"
                            height="19"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                            <path d="M10 21h4"/>
                        </svg>

                        <span class="absolute right-1.5 top-1.5 h-1.5 w-1.5 rounded-full bg-red-500"></span>

                    </button>


                    <div class="mx-1 h-7 w-px bg-slate-200"></div>


                    <!-- User -->

                    <div class="hidden items-center gap-2 sm:flex">

                        <div class="text-right">

                            <p class="text-xs font-semibold text-slate-800">
                                {{ auth()->user()->name ?? 'Administrator' }}
                            </p>

                            <p class="text-[10px] text-slate-400">
                                Administrator
                            </p>

                        </div>


                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">

                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}

                        </div>

                    </div>


                    <!-- Logout -->

                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                    >

                        @csrf

                        <button
                            type="submit"
                            title="Logout"
                            class="rounded-lg p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600"
                        >

                            <svg
                                width="18"
                                height="18"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M10 17l5-5-5-5"/>
                                <path d="M15 12H3"/>
                                <path d="M21 19V5a2 2 0 0 0-2-2h-7"/>
                                <path d="M12 21h7a2 2 0 0 0 2-2"/>
                            </svg>

                        </button>

                    </form>

                </div>

            </div>

        </header>


        <!-- CONTENT -->

        <main class="p-4 sm:p-6">

            @yield('content')

        </main>

    </div>


    @livewireScripts


    <script>

        function toggleSidebar() {

            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');

        }

    </script>

</body>

</html>