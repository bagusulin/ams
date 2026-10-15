@extends('layouts.admin')

@section('pageTitle', 'Asset Report')

@section('content')

<div class="space-y-6">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

        <div>

            <h1 class="text-xl font-semibold text-slate-800">
                Asset Report
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Laporan seluruh asset berdasarkan filter yang dipilih.
            </p>

        </div>


        <div class="flex flex-wrap gap-2">

            <a
                href="{{ route('reports.assets.excel', request()->query()) }}"
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-emerald-700">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z" />

                </svg>

                Export Excel

            </a>


            <a
                href="{{ route('reports.assets.pdf', request()->query()) }}"
                class="inline-flex items-center gap-2 rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-rose-700">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M7 21h10a2 2 0 002-2V7l-5-5H7a2 2 0 00-2 2v15a2 2 0 002 2z" />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M14 2v6h6" />

                </svg>

                Export PDF

            </a>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- FILTER --}}
    {{-- ========================================================= --}}

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

        <form
            method="GET"
            action="{{ route('reports.assets') }}"
            class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">

            {{-- SEARCH --}}
            <div class="xl:col-span-2">

                <label class="mb-1.5 block text-sm font-medium text-slate-700">
                    Search
                </label>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Asset code, brand, model, serial..."
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

            </div>


            {{-- CATEGORY --}}
            <div>

                <label class="mb-1.5 block text-sm font-medium text-slate-700">
                    Category
                </label>

                <select
                    name="category_id"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                    <option value="">
                        Semua Category
                    </option>

                    @foreach ($categories as $category)

                        <option
                            value="{{ $category->id }}"
                            @selected(request('category_id') == $category->id)>

                            {{ $category->name }}

                        </option>

                    @endforeach

                </select>

            </div>


            {{-- LOCATION --}}
            <div>

                <label class="mb-1.5 block text-sm font-medium text-slate-700">
                    Location
                </label>

                <select
                    name="location_id"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                    <option value="">
                        Semua Location
                    </option>

                    @foreach ($locations as $location)

                        <option
                            value="{{ $location->id }}"
                            @selected(request('location_id') == $location->id)>

                            {{ $location->name }}

                        </option>

                    @endforeach

                </select>

            </div>


            {{-- STATUS --}}
            <div>

                <label class="mb-1.5 block text-sm font-medium text-slate-700">
                    Status
                </label>

                <select
                    name="status"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                    <option value="">
                        Semua Status
                    </option>

                    <option
                        value="available"
                        @selected(request('status') === 'available')>
                        Available
                    </option>

                    <option
                        value="assigned"
                        @selected(request('status') === 'assigned')>
                        Assigned
                    </option>

                    <option
                        value="maintenance"
                        @selected(request('status') === 'maintenance')>
                        Maintenance
                    </option>

                    <option
                        value="lost"
                        @selected(request('status') === 'lost')>
                        Lost
                    </option>

                </select>

            </div>


            {{-- CONDITION --}}
            <div>

                <label class="mb-1.5 block text-sm font-medium text-slate-700">
                    Condition
                </label>

                <select
                    name="condition"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                    <option value="">
                        Semua Condition
                    </option>

                    <option
                        value="good"
                        @selected(request('condition') === 'good')>
                        Good
                    </option>

                    <option
                        value="fair"
                        @selected(request('condition') === 'fair')>
                        Fair
                    </option>

                    <option
                        value="damaged"
                        @selected(request('condition') === 'damaged')>
                        Damaged
                    </option>

                </select>

            </div>


            {{-- BUTTON --}}
            <div class="flex items-end gap-2">

                <button
                    type="submit"
                    class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                    Filter
                </button>

                <a
                    href="{{ route('reports.assets') }}"
                    class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">
                    Reset
                </a>

            </div>

        </form>

    </div>


    {{-- ========================================================= --}}
    {{-- SUMMARY --}}
    {{-- ========================================================= --}}

    <div class="grid gap-4 md:grid-cols-3">

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="text-sm text-slate-500">
                Total Hasil
            </div>

            <div class="mt-2 text-2xl font-bold text-slate-800">
                {{ $assets->total() }}
            </div>

        </div>


        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="text-sm text-slate-500">
                Filter Status
            </div>

            <div class="mt-2 text-lg font-semibold text-slate-800">
                {{ request('status') ? ucfirst(request('status')) : 'Semua' }}
            </div>

        </div>


        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="text-sm text-slate-500">
                Periode Generate
            </div>

            <div class="mt-2 text-lg font-semibold text-slate-800">
                {{ now()->format('d M Y H:i') }}
            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- TABLE --}}
    {{-- ========================================================= --}}

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="overflow-x-auto">

            <table class="min-w-full text-sm">

                <thead class="border-b border-slate-200 bg-slate-50">

                    <tr class="text-left text-xs font-semibold uppercase tracking-wider text-slate-500">

                        <th class="px-4 py-3">
                            Asset
                        </th>

                        <th class="px-4 py-3">
                            Category
                        </th>

                        <th class="px-4 py-3">
                            Location
                        </th>

                        <th class="px-4 py-3">
                            Vendor
                        </th>

                        <th class="px-4 py-3">
                            Status
                        </th>

                        <th class="px-4 py-3">
                            Condition
                        </th>

                        <th class="px-4 py-3">
                            Rental End
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse ($assets as $asset)

                        <tr class="hover:bg-slate-50">

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


                            <td class="px-4 py-4 text-slate-600">
                                {{ $asset->category?->name ?? '-' }}
                            </td>


                            <td class="px-4 py-4 text-slate-600">
                                {{ $asset->location?->name ?? '-' }}
                            </td>


                            <td class="px-4 py-4 text-slate-600">
                                {{ $asset->vendor?->name ?? '-' }}
                            </td>


                            <td class="px-4 py-4">

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


                            <td class="px-4 py-4 text-slate-600">
                                {{ ucfirst($asset->condition) }}
                            </td>


                            <td class="px-4 py-4 text-slate-600">

                                {{ $asset->rental_end_date?->format('d M Y') ?? '-' }}

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="px-4 py-12 text-center text-sm text-slate-500">

                                Tidak ada asset yang sesuai dengan filter.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if ($assets->hasPages())

            <div class="border-t border-slate-200 px-4 py-3">

                {{ $assets->links() }}

            </div>

        @endif

    </div>

</div>

@endsection