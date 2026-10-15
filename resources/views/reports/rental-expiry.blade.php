@extends('layouts.admin')

@section('pageTitle', 'Rental Expiry Report')

@section('content')

<div class="w-full space-y-6">

    {{-- ========================================================= --}}
    {{-- FLASH MESSAGE --}}
    {{-- ========================================================= --}}

    @if (session()->has('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            {{ session('error') }}
        </div>
    @endif


    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

        <div>
            <h1 class="text-xl font-semibold text-slate-800">
                Rental Expiry Report
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Pantau asset yang masa sewanya telah berakhir atau segera jatuh tempo.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">

            <a
                href="{{ route('reports.rental-expiry.excel', request()->query()) }}"
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-emerald-700">

                <svg
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">

                    <path d="M12 3v12"/>
                    <path d="m7 10 5 5 5-5"/>
                    <path d="M5 17v3h14v-3"/>

                </svg>

                Export Excel

            </a>

            <a
                href="{{ route('reports.rental-expiry.pdf', request()->query()) }}"
                class="inline-flex items-center gap-2 rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-rose-700">

                <svg
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">

                    <path d="M7 3h7l5 5v13H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
                    <path d="M14 3v6h5"/>
                    <path d="M9 14h6"/>
                    <path d="M9 17h6"/>

                </svg>

                Export PDF

            </a>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- SUMMARY CARDS --}}
    {{-- ========================================================= --}}

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">

        {{-- TOTAL --}}

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <p class="text-sm text-slate-500">
                Total Asset Sewa
            </p>

            <p class="mt-2 text-2xl font-bold text-slate-800">
                {{ number_format($totalRentalAssets) }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Sesuai filter lainnya
            </p>

        </div>


        {{-- EXPIRED --}}

        <div class="rounded-2xl border border-rose-200 bg-white p-5 shadow-sm">

            <p class="text-sm text-slate-500">
                Expired
            </p>

            <p class="mt-2 text-2xl font-bold text-rose-600">
                {{ number_format($expiredCount) }}
            </p>

            <a
                href="{{ route('reports.rental-expiry', array_merge(request()->except(['page', 'bucket']), ['bucket' => 'expired'])) }}"
                class="mt-2 inline-block text-xs font-medium text-rose-600 hover:underline">
                Lihat asset
            </a>

        </div>


        {{-- DUE 7 DAYS --}}

        <div class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm">

            <p class="text-sm text-slate-500">
                Jatuh Tempo ≤7 Hari
            </p>

            <p class="mt-2 text-2xl font-bold text-amber-600">
                {{ number_format($due7Count) }}
            </p>

            <a
                href="{{ route('reports.rental-expiry', array_merge(request()->except(['page', 'bucket']), ['bucket' => 'expired'])) }}"
                class="mt-2 inline-block text-xs font-medium text-rose-600 hover:underline">
                Lihat asset
            </a>

        </div>


        {{-- DUE 8-60 DAYS --}}

        <div class="rounded-2xl border border-orange-200 bg-white p-5 shadow-sm">

            <p class="text-sm text-slate-500">
                Jatuh Tempo 8–60 Hari
            </p>

            <p class="mt-2 text-2xl font-bold text-orange-600">
                {{ number_format($due8To60Count) }}
            </p>

            <a
                href="{{ route('reports.rental-expiry', array_merge(request()->except(['page', 'bucket']), ['bucket' => 'due_8_60'])) }}"
                class="mt-2 inline-block text-xs font-medium text-orange-600 hover:underline">
                Lihat asset
            </a>

        </div>


        {{-- OVER 60 DAYS --}}

        <div class="rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm">

            <p class="text-sm text-slate-500">
                Lebih dari 60 Hari
            </p>

            <p class="mt-2 text-2xl font-bold text-emerald-600">
                {{ number_format($over60Count) }}
            </p>

            <a
                href="{{ route('reports.rental-expiry', array_merge(request()->except(['page', 'bucket']), ['bucket' => 'expired'])) }}"
                class="mt-2 inline-block text-xs font-medium text-rose-600 hover:underline">
                Lihat asset
            </a>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- FILTER --}}
    {{-- ========================================================= --}}

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

        <form
            method="GET"
            action="{{ route('reports.rental-expiry') }}"
            class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

            {{-- SEARCH --}}

            <div class="xl:col-span-2">

                <label
                    for="search"
                    class="mb-1.5 block text-sm font-medium text-slate-700">

                    Cari Asset

                </label>

                <input
                    id="search"
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    maxlength="255"
                    placeholder="Asset code, brand, model, serial number..."
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

            </div>


            {{-- BUCKET --}}

            <div>

                <label
                    for="bucket"
                    class="mb-1.5 block text-sm font-medium text-slate-700">

                    Kategori Masa Sewa

                </label>

                <select
                    id="bucket"
                    name="bucket"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                    <option
                        value="all"
                        @selected($bucket === 'all')>

                        Semua Masa Sewa

                    </option>

                    <option
                        value="expired"
                        @selected($bucket === 'expired')>

                        Expired

                    </option>

                    <option
                        value="due_7"
                        @selected($bucket === 'due_7')>

                        Jatuh Tempo ≤7 Hari

                    </option>

                    <option
                        value="due_8_60"
                        @selected($bucket === 'due_8_60')>

                        Jatuh Tempo 8–60 Hari

                    </option>

                    <option
                        value="over_60"
                        @selected($bucket === 'over_60')>

                        Lebih dari 60 Hari

                    </option>

                </select>

            </div>


            {{-- ASSET STATUS --}}

            <div>

                <label
                    for="asset_status"
                    class="mb-1.5 block text-sm font-medium text-slate-700">

                    Status Asset

                </label>

                <select
                    id="asset_status"
                    name="asset_status"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                    <option value="">
                        Semua Status
                    </option>

                    <option
                        value="available"
                        @selected(request('asset_status') === 'available')>

                        Available

                    </option>

                    <option
                        value="assigned"
                        @selected(request('asset_status') === 'assigned')>

                        Assigned

                    </option>

                    <option
                        value="maintenance"
                        @selected(request('asset_status') === 'maintenance')>

                        Maintenance

                    </option>

                    <option
                        value="lost"
                        @selected(request('asset_status') === 'lost')>

                        Lost

                    </option>

                </select>

            </div>


            {{-- CATEGORY --}}

            <div>

                <label
                    for="category_id"
                    class="mb-1.5 block text-sm font-medium text-slate-700">

                    Category

                </label>

                <select
                    id="category_id"
                    name="category_id"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                    <option value="">
                        Semua Category
                    </option>

                    @foreach ($categories as $category)

                        <option
                            value="{{ $category->id }}"
                            @selected((string) request('category_id') === (string) $category->id)>

                            {{ $category->name }}

                        </option>

                    @endforeach

                </select>

            </div>


            {{-- LOCATION --}}

            <div>

                <label
                    for="location_id"
                    class="mb-1.5 block text-sm font-medium text-slate-700">

                    Location

                </label>

                <select
                    id="location_id"
                    name="location_id"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                    <option value="">
                        Semua Location
                    </option>

                    @foreach ($locations as $location)

                        <option
                            value="{{ $location->id }}"
                            @selected((string) request('location_id') === (string) $location->id)>

                            {{ $location->name }}

                        </option>

                    @endforeach

                </select>

            </div>


            {{-- DATE FROM --}}

            <div>

                <label
                    for="date_from"
                    class="mb-1.5 block text-sm font-medium text-slate-700">

                    Rental End Dari

                </label>

                <input
                    id="date_from"
                    type="date"
                    name="date_from"
                    value="{{ request('date_from') }}"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

            </div>


            {{-- DATE TO --}}

            <div>

                <label
                    for="date_to"
                    class="mb-1.5 block text-sm font-medium text-slate-700">

                    Rental End Sampai

                </label>

                <input
                    id="date_to"
                    type="date"
                    name="date_to"
                    value="{{ request('date_to') }}"
                    min="{{ request('date_from') }}"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

            </div>


            {{-- BUTTONS --}}

            <div class="flex items-end gap-2 xl:col-span-4">

                <button
                    type="submit"
                    class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700">

                    Filter

                </button>

                <a
                    href="{{ route('reports.rental-expiry') }}"
                    class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">

                    Reset

                </a>

            </div>

        </form>

    </div>


    {{-- ========================================================= --}}
    {{-- TABLE HEADER --}}
    {{-- ========================================================= --}}

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="flex flex-col gap-1 border-b border-slate-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">

            <h2 class="font-semibold text-slate-800">
                Daftar Rental Expiry
            </h2>

            <p class="text-xs text-slate-500">
                Menampilkan
                {{ $assets->firstItem() ?? 0 }}–{{ $assets->lastItem() ?? 0 }}
                dari {{ $assets->total() }} asset
            </p>

        </div>


        {{-- ===================================================== --}}
        {{-- TABLE --}}
        {{-- ===================================================== --}}

        <div class="overflow-x-auto">

            <table class="min-w-full text-sm">

                <thead class="border-b border-slate-200 bg-slate-50">

                    <tr class="text-left text-xs font-semibold uppercase tracking-wider text-slate-500">

                        <th class="whitespace-nowrap px-4 py-3">
                            Asset
                        </th>

                        <th class="whitespace-nowrap px-4 py-3">
                            Category
                        </th>

                        <th class="whitespace-nowrap px-4 py-3">
                            Location
                        </th>

                        <th class="whitespace-nowrap px-4 py-3">
                            Vendor
                        </th>

                        <th class="whitespace-nowrap px-4 py-3">
                            Rental End
                        </th>

                        <th class="whitespace-nowrap px-4 py-3">
                            Sisa Hari
                        </th>

                        <th class="whitespace-nowrap px-4 py-3">
                            Status Sewa
                        </th>

                        <th class="whitespace-nowrap px-4 py-3">
                            Status Asset
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse ($assets as $asset)

                        @php
                            $daysRemaining = (int) today()->diffInDays(
                                $asset->rental_end_date,
                                false
                            );

                            $expiryLabel = \App\Support\Reports\RentalExpiryQuery::bucketLabel(
                                $asset->rental_end_date
                            );
                        @endphp

                        <tr class="hover:bg-slate-50">

                            {{-- ASSET --}}

                            <td class="px-4 py-4">

                                <div class="font-medium text-slate-800">
                                    {{ $asset->asset_code }}
                                </div>

                                <div class="mt-1 text-xs text-slate-500">
                                    {{ $asset->brand }}
                                    {{ $asset->model }}
                                </div>

                                <div class="mt-1 text-xs text-slate-400">
                                    SN: {{ $asset->serial_number ?: '-' }}
                                </div>

                            </td>


                            {{-- CATEGORY --}}

                            <td class="whitespace-nowrap px-4 py-4 text-slate-600">
                                {{ $asset->category?->name ?? '-' }}
                            </td>


                            {{-- LOCATION --}}

                            <td class="whitespace-nowrap px-4 py-4 text-slate-600">
                                {{ $asset->location?->name ?? '-' }}
                            </td>


                            {{-- VENDOR --}}

                            <td class="whitespace-nowrap px-4 py-4 text-slate-600">
                                {{ $asset->vendor?->name ?? '-' }}
                            </td>


                            {{-- RENTAL END --}}

                            <td class="whitespace-nowrap px-4 py-4 text-slate-600">
                                {{ $asset->rental_end_date?->format('d M Y') ?? '-' }}
                            </td>


                            {{-- DAYS REMAINING --}}

                            <td class="whitespace-nowrap px-4 py-4">

                                @if ($daysRemaining < 0)

                                    <span class="font-semibold text-rose-600">
                                        Lewat {{ abs($daysRemaining) }} hari
                                    </span>

                                @elseif ($daysRemaining === 0)

                                    <span class="font-semibold text-rose-600">
                                        Hari ini
                                    </span>

                                @elseif ($daysRemaining <= 7)

                                    <span class="font-semibold text-rose-600">
                                        {{ $daysRemaining }} hari
                                    </span>

                                @elseif ($daysRemaining <= 60)

                                    <span class="font-semibold text-amber-600">
                                        {{ $daysRemaining }} hari
                                    </span>

                                @else

                                    <span class="font-semibold text-emerald-600">
                                        {{ $daysRemaining }} hari
                                    </span>

                                @endif

                            </td>


                            {{-- EXPIRY LABEL --}}

                            <td class="whitespace-nowrap px-4 py-4">

                                @if ($daysRemaining < 0)

                                    <span class="inline-flex rounded-full bg-rose-100 px-2.5 py-1 text-xs font-medium text-rose-700">
                                        {{ $expiryLabel }}
                                    </span>

                                @elseif ($daysRemaining <= 7)

                                    <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">
                                        {{ $expiryLabel }}
                                    </span>

                                @elseif ($daysRemaining <= 60)

                                    <span class="inline-flex rounded-full bg-orange-100 px-2.5 py-1 text-xs font-medium text-orange-700">
                                        {{ $expiryLabel }}
                                    </span>

                                @else

                                    <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                        {{ $expiryLabel }}
                                    </span>

                                @endif

                            </td>


                            {{-- ASSET STATUS --}}

                            <td class="whitespace-nowrap px-4 py-4">

                                @if ($asset->status === 'available')

                                    <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                        Available
                                    </span>

                                @elseif ($asset->status === 'assigned')

                                    <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-700">
                                        Assigned
                                    </span>

                                @elseif ($asset->status === 'maintenance')

                                    <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">
                                        Maintenance
                                    </span>

                                @else

                                    <span class="inline-flex rounded-full bg-rose-100 px-2.5 py-1 text-xs font-medium text-rose-700">
                                        Lost
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="8"
                                class="px-4 py-12 text-center text-sm text-slate-500">

                                Tidak ada asset yang sesuai dengan filter.

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}

        @if ($assets->hasPages())

            <div class="border-t border-slate-200 px-4 py-3">
                {{ $assets->links() }}
            </div>

        @endif

    </div>

</div>

@endsection