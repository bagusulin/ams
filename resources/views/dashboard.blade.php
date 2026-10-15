@extends('layouts.admin')

@section('content')

    <!-- ========================================================= -->
    <!-- PAGE HEADER -->
    <!-- ========================================================= -->

    <div class="mb-6">

        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">

            <div>

                <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                    Dashboard
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Ringkasan kondisi asset dan monitoring masa sewa.
                </p>

            </div>


            <div class="text-sm text-gray-500">
                {{ now()->translatedFormat('l, d F Y') }}
            </div>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- STATISTIC CARDS -->
    <!-- ========================================================= -->

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">


        <!-- TOTAL ASSET -->
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

            <div class="flex items-start justify-between">

                <div>

                    <p class="text-sm font-medium text-gray-500">
                        Total Asset
                    </p>

                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ number_format($totalAssets) }}
                    </p>

                </div>


                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-7 4h8a2 2 0 002-2V7a2 2 0 00-2-2h-2.5l-1-2h-3l-1 2H7a2 2 0 00-2 2v12a2 2 0 002 2z"
                        />
                    </svg>

                </div>

            </div>


            <div class="mt-3 text-xs text-gray-400">
                Semua asset terdaftar
            </div>

        </div>


        <!-- AVAILABLE -->
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

            <div class="flex items-start justify-between">

                <div>

                    <p class="text-sm font-medium text-gray-500">
                        Available
                    </p>

                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ number_format($availableAssets) }}
                    </p>

                </div>


                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-50 text-green-600">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7"
                        />
                    </svg>

                </div>

            </div>


            <div class="mt-3 text-xs text-gray-400">
                Siap digunakan
            </div>

        </div>


        <!-- ASSIGNED -->
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

            <div class="flex items-start justify-between">

                <div>

                    <p class="text-sm font-medium text-gray-500">
                        Assigned
                    </p>

                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ number_format($assignedAssets) }}
                    </p>

                </div>


                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                        />
                    </svg>

                </div>

            </div>


            <div class="mt-3 text-xs text-gray-400">
                Sedang digunakan
            </div>

        </div>


        <!-- MAINTENANCE -->
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

            <div class="flex items-start justify-between">

                <div>

                    <p class="text-sm font-medium text-gray-500">
                        Maintenance
                    </p>

                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ number_format($maintenanceAssets) }}
                    </p>

                </div>


                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50 text-orange-600">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M11 3h2a2 2 0 012 2v1a2 2 0 002 2h1a2 2 0 012 2v2a2 2 0 01-2 2h-1a2 2 0 00-2 2v1a2 2 0 01-2 2h-2a2 2 0 01-2-2v-1a2 2 0 00-2-2H6a2 2 0 01-2-2v-2a2 2 0 012-2h1a2 2 0 002-2V5a2 2 0 012-2z"
                        />
                    </svg>

                </div>

            </div>


            <div class="mt-3 text-xs text-gray-400">
                Sedang diperbaiki
            </div>

        </div>


        <!-- SEWA -->
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

            <div class="flex items-start justify-between">

                <div>

                    <p class="text-sm font-medium text-gray-500">
                        Sewa ≤ 60 Hari
                    </p>

                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ number_format($expiringAssetsCount) }}
                    </p>

                </div>


                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-50 text-red-600">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v3m0 4h.01M10.29 3.86l-7.82 13.55A1.5 1.5 0 003.77 20h16.46a1.5 1.5 0 001.3-2.25L13.71 3.86a2 2 0 00-3.42 0z"
                        />
                    </svg>

                </div>

            </div>


            <div class="mt-3 text-xs text-gray-400">
                Perlu ditindaklanjuti
            </div>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- ALERT MASA SEWA -->
    <!-- ========================================================= -->

    @if ($expiredAssetsCount > 0 || $expiringAssetsCount > 0)

        <div class="mt-6 rounded-xl border border-orange-200 bg-orange-50 p-5">

            <div class="flex items-start gap-4">

                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange-100 text-orange-600">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v3m0 4h.01M10.29 3.86l-7.82 13.55A1.5 1.5 0 003.77 20h16.46a1.5 1.5 0 001.3-2.25L13.71 3.86a2 2 0 00-3.42 0z"
                        />
                    </svg>

                </div>


                <div>

                    <h3 class="text-sm font-semibold text-orange-900">
                        Perhatian Masa Sewa
                    </h3>

                    <p class="mt-1 text-sm text-orange-800">

                        @if ($expiredAssetsCount > 0)
                            Terdapat
                            <strong>{{ $expiredAssetsCount }}</strong>
                            asset yang masa sewanya telah berakhir.
                        @endif

                        @if ($expiringAssetsCount > 0)
                            Terdapat
                            <strong>{{ $expiringAssetsCount }}</strong>
                            asset yang akan berakhir dalam 60 hari.
                        @endif

                    </p>

                </div>

            </div>

        </div>

    @endif


    <!-- ========================================================= -->
    <!-- TWO COLUMN -->
    <!-- ========================================================= -->

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">


        <!-- ===================================================== -->
        <!-- ASSET TERBARU -->
        <!-- ===================================================== -->

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">

                <div>

                    <h2 class="font-semibold text-gray-900">
                        Asset Terbaru
                    </h2>

                    <p class="mt-0.5 text-xs text-gray-500">
                        Asset yang terakhir ditambahkan
                    </p>

                </div>

            </div>


            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Asset
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Kategori
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Lokasi
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse ($recentAssets as $asset)

                            @php

                                $statusLabel = match ($asset->status) {

                                    'available' => 'Available',

                                    'assigned' => 'Assigned',

                                    'maintenance' => 'Maintenance',

                                    'lost' => 'Lost',

                                    default => ucfirst($asset->status),

                                };


                                $statusClass = match ($asset->status) {

                                    'available' => 'bg-green-50 text-green-700',

                                    'assigned' => 'bg-blue-50 text-blue-700',

                                    'maintenance' => 'bg-orange-50 text-orange-700',

                                    'lost' => 'bg-red-50 text-red-700',

                                    default => 'bg-gray-100 text-gray-700',

                                };

                            @endphp


                            <tr class="hover:bg-gray-50">

                                <td class="px-5 py-4">

                                    <div>

                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ $asset->asset_code }}
                                        </p>

                                        <p class="text-xs text-gray-500">
                                            {{ trim(($asset->brand ?? '') . ' ' . ($asset->model ?? '')) ?: '-' }}
                                        </p>

                                    </div>

                                </td>


                                <td class="px-5 py-4">

                                    <span class="text-sm text-gray-700">
                                        {{ $asset->category?->name ?? '-' }}
                                    </span>

                                </td>


                                <td class="px-5 py-4">

                                    <span class="text-sm text-gray-700">
                                        {{ $asset->location?->name ?? '-' }}
                                    </span>

                                </td>


                                <td class="px-5 py-4">

                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="4"
                                    class="px-5 py-10 text-center"
                                >

                                    <p class="text-sm text-gray-500">
                                        Belum ada asset.
                                    </p>

                                    <p class="mt-1 text-xs text-gray-400">
                                        Asset terbaru akan muncul di sini.
                                    </p>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        <!-- ===================================================== -->
        <!-- MASA SEWA -->
        <!-- ===================================================== -->

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">

                <div>

                    <h2 class="font-semibold text-gray-900">
                        Masa Sewa Akan Berakhir
                    </h2>

                    <p class="mt-0.5 text-xs text-gray-500">
                        Maksimal 60 hari dari hari ini
                    </p>

                </div>

            </div>


            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Asset
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Vendor
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Berakhir
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Sisa
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse ($expiringAssets as $asset)

                            @php

                                $days = $asset->rental_days_remaining;

                                if ($days <= 7) {

                                    $remainingClass = 'bg-red-50 text-red-700';

                                    $remainingLabel = $days . ' hari';

                                } elseif ($days <= 14) {

                                    $remainingClass = 'bg-orange-50 text-orange-700';

                                    $remainingLabel = $days . ' hari';

                                } else {

                                    $remainingClass = 'bg-yellow-50 text-yellow-700';

                                    $remainingLabel = $days . ' hari';

                                }

                            @endphp


                            <tr class="hover:bg-gray-50">

                                <td class="px-5 py-4">

                                    <div>

                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ $asset->asset_code }}
                                        </p>

                                        <p class="text-xs text-gray-500">
                                            {{ $asset->category?->name ?? '-' }}
                                        </p>

                                    </div>

                                </td>


                                <td class="px-5 py-4">

                                    <span class="text-sm text-gray-700">
                                        {{ $asset->vendor?->name ?? '-' }}
                                    </span>

                                </td>


                                <td class="px-5 py-4">

                                    <span class="text-sm text-gray-700">
                                        {{ $asset->rental_end_date?->format('d M Y') ?? '-' }}
                                    </span>

                                </td>


                                <td class="px-5 py-4">

                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $remainingClass }}">

                                        @if ($days === 0)

                                            Hari ini

                                        @elseif ($days === 1)

                                            1 hari

                                        @else

                                            {{ $remainingLabel }}

                                        @endif

                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="4"
                                    class="px-5 py-10 text-center"
                                >

                                    <p class="text-sm text-gray-500">
                                        Tidak ada asset yang akan habis masa sewanya.
                                    </p>

                                    <p class="mt-1 text-xs text-gray-400">
                                        Monitoring akan muncul otomatis ketika ada asset mendekati tanggal akhir sewa.
                                    </p>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- CATEGORY SUMMARY -->
    <!-- ========================================================= -->

    <div class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

        <div class="border-b border-gray-200 px-5 py-4">

            <h2 class="font-semibold text-gray-900">
                Asset Berdasarkan Kategori
            </h2>

            <p class="mt-0.5 text-xs text-gray-500">
                Distribusi asset berdasarkan kategori aktif
            </p>

        </div>


        <div class="grid grid-cols-1 divide-y divide-gray-100 sm:grid-cols-2 xl:grid-cols-3 xl:divide-x xl:divide-y-0">

            @forelse ($categorySummary as $category)

                <div class="p-5">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm font-medium text-gray-700">
                                {{ $category->name }}
                            </p>

                            <p class="mt-1 text-xs text-gray-400">
                                Kode: {{ $category->code }}
                            </p>

                        </div>


                        <div class="text-right">

                            <p class="text-2xl font-bold text-gray-900">
                                {{ number_format($category->assets_count) }}
                            </p>

                            <p class="text-xs text-gray-400">
                                asset
                            </p>

                        </div>

                    </div>

                </div>

            @empty

                <div class="col-span-full p-8 text-center">

                    <p class="text-sm text-gray-500">
                        Belum ada kategori asset.
                    </p>

                </div>

            @endforelse

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- FOOTER INFO -->
    <!-- ========================================================= -->

    <div class="mt-6 flex flex-col gap-2 rounded-xl border border-blue-100 bg-blue-50 p-5 sm:flex-row sm:items-center">

        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600">

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"
                />
            </svg>

        </div>


        <div>

            <h3 class="text-sm font-semibold text-blue-900">
                Asset Management System
            </h3>

            <p class="mt-1 text-sm text-blue-700">
                Dashboard menampilkan kondisi asset, status penggunaan,
                kategori, dan monitoring masa sewa secara real-time dari database.
            </p>

        </div>

    </div>

@endsection