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

            <!-- QR ASSET -->

            <a
                href="{{ route('asset-qr.index') }}"
                class="mb-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                {{ request()->routeIs('asset-qr.*')
                    ? 'bg-blue-50 text-blue-700'
                    : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">

                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">

                    <rect x="3" y="3" width="7" height="7"/>
                    <rect x="14" y="3" width="7" height="7"/>
                    <rect x="3" y="14" width="7" height="7"/>
                    <path d="M14 14h3v3h-3z"/>
                    <path d="M19 14v2"/>
                    <path d="M14 19h2"/>
                    <path d="M19 19h2"/>

                </svg>

                <span>QR Asset</span>

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

            <a href="{{ route('asset-history.index') }}"
                class="mb-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                {{ request()->routeIs('asset-history.*') 
                    ? 'bg-blue-50 text-blue-700'
                    : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
            
                <svg width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                <path d="M3 12a9 9 0 1 0 3-6.7"/>
                <path d="M3 4v6h6"/>
                <path d="M12 7v5l3 2"/>
                </svg>
            Asset History
            </a>

            <a
            href="{{ route('assignments.index') }}"
            class="mb-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
            {{ request()->routeIs('assignments.*')
            ? 'bg-blue-50 text-blue-700'
            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
            >

                <span class="flex h-5 w-5 items-center justify-center text-sm">
                    ↔
                </span>
                Assignment
            </a>

            <a
            href="{{ route('returns.index') }}"
            class="mb-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
            {{ request()->routeIs('returns.*')
            ? 'bg-blue-50 text-blue-700'
            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
            >

                <span class="flex h-5 w-5 items-center justify-center text-sm">
                    ↩
                </span>
                Return Asset
            </a>


            <a
            href="{{ route('maintenances.index') }}"
            class="mb-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
            {{ request()->routeIs('maintenances.*')
            ? 'bg-blue-50 text-blue-700'
            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
            >

                <span class="flex h-5 w-5 items-center justify-center text-sm">
                    🔧
                </span>
                Maintenance
            </a>


            <!-- REPORTS -->

            <p class="mb-2 mt-7 px-3 text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400">
                Reports
            </p>

            <!-- Asset Report -->

            <a
                href="{{ route('reports.assets') }}"
                class="mb-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                {{ request()->routeIs('reports.assets')
                    ? 'bg-blue-50 text-blue-700'
                    : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">

                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">

                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <path d="M8 17V13"/>
                    <path d="M12 17V9"/>
                    <path d="M16 17V6"/>

                </svg>

                <span>
                    Reports
                </span>

            </a>


            <!-- Assignment & Return Report -->

            <a
                href="{{ route('reports.assignments') }}"
                class="mb-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                {{ request()->routeIs('reports.assignments')
                    ? 'bg-blue-50 text-blue-700'
                    : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">

                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">

                    <path d="M9 11l3 3L22 4"/>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>

                </svg>

                <span>
                    Assignment & Return
                </span>

            </a>

            <a
                href="{{ route('reports.maintenances') }}"
                class="mb-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                {{ request()->routeIs('reports.maintenances*')
                    ? 'bg-blue-50 text-blue-700'
                    : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">

                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">

                    <path d="M12 3v18"/>
                    <path d="M3 12h18"/>
                    <path d="M5 5l14 14"/>
                    <path d="M19 5L5 19"/>
                </svg>
                <span>
                    Maintenance Report
                </span>
            </a>

            <!-- RENTAL EXPIRY REPORT -->

            <a
                href="{{ route('reports.rental-expiry') }}"
                class="mb-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                {{ request()->routeIs('reports.rental-expiry*')
                    ? 'bg-blue-50 text-blue-700'
                    : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">

                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">

                    <rect x="3" y="4" width="18" height="17" rx="2"/>
                    <path d="M16 2v4"/>
                    <path d="M8 2v4"/>
                    <path d="M3 10h18"/>
                    <path d="M8 14h3"/>
                    <path d="M8 17h6"/>
                </svg>

                <span>
                    Rental Expiry
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

<div class="min-h-screen w-full lg:pl-64">

    <!-- TOPBAR -->

    <header class="sticky top-0 z-20 h-16 w-full border-b border-slate-200 bg-white/90 backdrop-blur">

        <div class="flex h-full items-center justify-between px-4 sm:px-6">

            <div class="flex items-center gap-3">

                <button
                    onclick="toggleSidebar()"
                    type="button"
                    class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 lg:hidden">

                    <svg
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2">

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

                {{-- ========================================================= --}}
                {{-- RENTAL EXPIRY NOTIFICATION --}}
                {{-- ========================================================= --}}

                @php
                    $expiryNotificationCount = $rentalExpiryNotificationCount ?? 0;
                    $expiredNotifications = $expiredRentalNotifications ?? collect();
                    $upcomingNotifications = $upcomingRentalNotifications ?? collect();
                @endphp

                <details class="group relative">

                    {{-- BELL BUTTON --}}

                    <summary
                        aria-label="Buka notifikasi rental"
                        title="Notifikasi rental"
                        class="relative flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 [&::-webkit-details-marker]:hidden">

                        <svg
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round">

                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                            <path d="M10 21h4"/>

                        </svg>

                        @if ($expiryNotificationCount > 0)

                            <span
                                class="absolute -right-0.5 -top-0.5 flex min-h-5 min-w-5 items-center justify-center rounded-full border-2 border-white bg-rose-500 px-1 text-[10px] font-bold text-white">

                                {{ $expiryNotificationCount > 99 ? '99+' : $expiryNotificationCount }}

                            </span>

                        @endif

                    </summary>


                    {{-- NOTIFICATION PANEL --}}

                    <div
                        class="absolute right-0 z-50 mt-2 w-96 max-w-[calc(100vw-2rem)] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">

                        {{-- PANEL HEADER --}}

                        <div class="border-b border-slate-200 px-4 py-3">

                            <div class="flex items-center justify-between gap-3">

                                <div>
                                    <h3 class="text-sm font-semibold text-slate-800">
                                        Reminder Rental
                                    </h3>

                                    <p class="mt-0.5 text-xs text-slate-500">
                                        Masa sewa dan jatuh tempo dalam 60 hari
                                    </p>
                                </div>

                                <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                    {{ $expiryNotificationCount }} asset
                                </span>

                            </div>

                        </div>


                        {{-- EMPTY STATE --}}

                        @if ($expiryNotificationCount === 0)

                            <div class="px-4 py-8 text-center">

                                <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">

                                    <svg
                                        width="20"
                                        height="20"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round">

                                        <path d="m5 12 4 4L19 6"/>

                                    </svg>

                                </div>

                                <p class="mt-3 text-sm font-medium text-slate-700">
                                    Tidak ada reminder rental
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    Tidak ada asset expired atau jatuh tempo dalam 60 hari.
                                </p>

                            </div>

                        @else

                            <div class="max-h-[65vh] overflow-y-auto">

                                {{-- EXPIRED SECTION --}}

                                @if ($expiredRentalNotificationCount > 0)

                                    <div class="border-b border-slate-100">

                                        <div class="flex items-center justify-between bg-rose-50 px-4 py-2">

                                            <span class="text-xs font-semibold uppercase tracking-wide text-rose-700">
                                                Masa Sewa Expired
                                            </span>

                                            <span class="text-xs font-semibold text-rose-700">
                                                {{ $expiredRentalNotificationCount }}
                                            </span>

                                        </div>

                                        @forelse ($expiredNotifications as $asset)

                                            @php
                                                $daysExpired = (int) $asset->rental_end_date
                                                    ->diffInDays(today(), false);
                                            @endphp

                                            <div class="border-b border-slate-100 px-4 py-3 last:border-b-0">

                                                <div class="flex items-start gap-3">

                                                    <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-rose-500"></span>

                                                    <div class="min-w-0 flex-1">

                                                        <p class="truncate text-sm font-semibold text-slate-800">
                                                            {{ $asset->asset_code }}
                                                        </p>

                                                        <p class="mt-0.5 truncate text-xs text-slate-500">
                                                            {{ $asset->brand }}
                                                            {{ $asset->model }}
                                                        </p>

                                                        <p class="mt-1 text-xs text-rose-600">
                                                            Expired
                                                            {{ $asset->rental_end_date->format('d M Y') }}
                                                            @if ($daysExpired > 0)
                                                                · Lewat {{ $daysExpired }} hari
                                                            @endif
                                                        </p>

                                                    </div>

                                                </div>

                                            </div>

                                        @empty

                                            <p class="px-4 py-3 text-xs text-slate-500">
                                                Tidak ada asset expired.
                                            </p>

                                        @endforelse

                                        @if ($expiredRentalNotificationCount > 5)

                                            <p class="px-4 py-2 text-xs text-slate-500">
                                                Menampilkan 5 dari {{ $expiredRentalNotificationCount }} asset expired.
                                            </p>

                                        @endif

                                    </div>

                                @endif


                                {{-- UPCOMING SECTION --}}

                                @if ($upcomingRentalNotificationCount > 0)

                                    <div>

                                        <div class="flex items-center justify-between bg-amber-50 px-4 py-2">

                                            <span class="text-xs font-semibold uppercase tracking-wide text-amber-700">
                                                Akan Jatuh Tempo
                                            </span>

                                            <span class="text-xs font-semibold text-amber-700">
                                                {{ $upcomingRentalNotificationCount }}
                                            </span>

                                        </div>

                                        @forelse ($upcomingNotifications as $asset)

                                            @php
                                                $daysRemaining = (int) today()->diffInDays(
                                                    $asset->rental_end_date,
                                                    false
                                                );

                                                $bucket = $daysRemaining <= 7
                                                    ? 'due_7'
                                                    : 'due_8_60';
                                            @endphp

                                            <div class="border-b border-slate-100 px-4 py-3 last:border-b-0">

                                                <div class="flex items-start gap-3">

                                                    <span
                                                        class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $daysRemaining <= 7 ? 'bg-rose-500' : 'bg-amber-500' }}">
                                                    </span>

                                                    <div class="min-w-0 flex-1">

                                                        <p class="truncate text-sm font-semibold text-slate-800">
                                                            {{ $asset->asset_code }}
                                                        </p>

                                                        <p class="mt-0.5 truncate text-xs text-slate-500">
                                                            {{ $asset->brand }}
                                                            {{ $asset->model }}
                                                        </p>

                                                        <p class="mt-1 text-xs {{ $daysRemaining <= 7 ? 'font-semibold text-rose-600' : 'text-amber-700' }}">

                                                            @if ($daysRemaining === 0)
                                                                Jatuh tempo hari ini
                                                            @else
                                                                Jatuh tempo dalam {{ $daysRemaining }} hari
                                                            @endif

                                                            · {{ $asset->rental_end_date->format('d M Y') }}

                                                        </p>

                                                    </div>

                                                </div>

                                            </div>

                                        @empty

                                            <p class="px-4 py-3 text-xs text-slate-500">
                                                Tidak ada asset yang segera jatuh tempo.
                                            </p>

                                        @endforelse

                                        @if ($upcomingRentalNotificationCount > 5)

                                            <p class="px-4 py-2 text-xs text-slate-500">
                                                Menampilkan 5 dari {{ $upcomingRentalNotificationCount }} asset yang akan jatuh tempo.
                                            </p>

                                        @endif

                                    </div>

                                @endif

                            </div>

                        @endif


                        {{-- PANEL FOOTER --}}

                        <div class="border-t border-slate-200 bg-slate-50 p-3">

                            <a
                                href="{{ route('reports.rental-expiry') }}"
                                class="flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-3 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700">

                                Lihat Rental Expiry Report

                                <svg
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round">

                                    <path d="M5 12h14"/>
                                    <path d="m12 5 7 7-7 7"/>

                                </svg>

                            </a>

                        </div>

                    </div>

                </details>


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
                    action="{{ route('logout') }}">

                    @csrf

                    <button
                        type="submit"
                        title="Logout"
                        class="rounded-lg p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600">

                        <svg
                            width="18"
                            height="18"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2">

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

    <main class="w-full p-4 sm:p-6">

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