@extends('layouts.admin')

@section('pageTitle', 'Maintenance Report')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

        <div>

            <h1 class="text-xl font-semibold text-slate-800">
                Maintenance Report
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Laporan riwayat maintenance dan biaya asset.
            </p>

        </div>


        <div class="flex flex-wrap gap-2">

            <a
                href="{{ route('reports.maintenances.excel', request()->query()) }}"
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-emerald-700">

                <svg
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z"/>

                </svg>

                Export Excel

            </a>


            <a
                href="{{ route('reports.maintenances.pdf', request()->query()) }}"
                class="inline-flex items-center gap-2 rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-rose-700">

                <svg
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M7 21h10a2 2 0 002-2V7l-5-5H7a2 2 0 00-2 2v15a2 2 0 002 2z"/>

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M14 2v6h6"/>

                </svg>

                Export PDF

            </a>

        </div>

    </div>


    {{-- FILTER --}}

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

        <form
            method="GET"
            action="{{ route('reports.maintenances') }}"
            class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">

            {{-- SEARCH --}}

            <div class="xl:col-span-2">

                <label class="mb-1.5 block text-sm font-medium text-slate-700">
                    Search
                </label>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Asset code, brand, model, tipe maintenance..."
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

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
                        value="pending"
                        @selected(request('status') === 'pending')>
                        Pending
                    </option>

                    <option
                        value="in_progress"
                        @selected(request('status') === 'in_progress')>
                        In Progress
                    </option>

                    <option
                        value="completed"
                        @selected(request('status') === 'completed')>
                        Completed
                    </option>

                    <option
                        value="cancelled"
                        @selected(request('status') === 'cancelled')>
                        Cancelled
                    </option>

                </select>

            </div>


            {{-- TYPE --}}

            <div>

                <label class="mb-1.5 block text-sm font-medium text-slate-700">
                    Tipe
                </label>

                <select
                    name="type"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                    <option value="">
                        Semua Tipe
                    </option>

                    <option
                        value="Hardware"
                        @selected(request('type') === 'Hardware')>
                        Hardware
                    </option>

                    <option
                        value="Software"
                        @selected(request('type') === 'Software')>
                        Software
                    </option>

                    <option
                        value="Network"
                        @selected(request('type') === 'Network')>
                        Network
                    </option>

                    <option
                        value="Preventive"
                        @selected(request('type') === 'Preventive')>
                        Preventive
                    </option>

                    <option
                        value="Other"
                        @selected(request('type') === 'Other')>
                        Other
                    </option>

                </select>

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


            {{-- DATE FROM --}}

            <div>

                <label class="mb-1.5 block text-sm font-medium text-slate-700">
                    Tanggal Dari
                </label>

                <input
                    type="date"
                    name="date_from"
                    value="{{ request('date_from') }}"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

            </div>


            {{-- DATE TO --}}

            <div>

                <label class="mb-1.5 block text-sm font-medium text-slate-700">
                    Tanggal Sampai
                </label>

                <input
                    type="date"
                    name="date_to"
                    value="{{ request('date_to') }}"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

            </div>


            {{-- BUTTON --}}

            <div class="flex items-end gap-2 md:col-span-2 xl:col-span-4">

                <button
                    type="submit"
                    class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700">

                    Filter

                </button>

                <a
                    href="{{ route('reports.maintenances') }}"
                    class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">

                    Reset

                </a>

            </div>

        </form>

    </div>


    {{-- SUMMARY --}}

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="text-sm text-slate-500">
                Total Maintenance
            </div>

            <div class="mt-2 text-2xl font-bold text-slate-800">
                {{ $totalMaintenance }}
            </div>

        </div>


        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="text-sm text-slate-500">
                Pending / In Progress
            </div>

            <div class="mt-2 text-2xl font-bold text-blue-600">
                {{ $inProgressMaintenance }}
            </div>

        </div>


        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="text-sm text-slate-500">
                Completed
            </div>

            <div class="mt-2 text-2xl font-bold text-emerald-600">
                {{ $completedMaintenance }}
            </div>

        </div>


        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="text-sm text-slate-500">
                Total Biaya
            </div>

            <div class="mt-2 text-xl font-bold text-slate-800">
                Rp {{ number_format((float) $totalCost, 0, ',', '.') }}
            </div>

        </div>

    </div>


    {{-- TABLE --}}

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
                            Tanggal
                        </th>

                        <th class="px-4 py-3">
                            Tipe
                        </th>

                        <th class="px-4 py-3">
                            Biaya
                        </th>

                        <th class="px-4 py-3">
                            Status
                        </th>

                        <th class="px-4 py-3">
                            Tindakan
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse ($maintenances as $maintenance)

                        <tr class="hover:bg-slate-50">

                            {{-- ASSET --}}

                            <td class="px-4 py-4">

                                <div class="font-medium text-slate-800">
                                    {{ $maintenance->asset?->asset_code ?? '-' }}
                                </div>

                                <div class="mt-1 text-xs text-slate-500">

                                    {{ $maintenance->asset?->brand }}
                                    {{ $maintenance->asset?->model }}

                                </div>

                                <div class="mt-1 text-xs text-slate-400">

                                    SN:
                                    {{ $maintenance->asset?->serial_number ?? '-' }}

                                </div>

                            </td>


                            {{-- CATEGORY --}}

                            <td class="px-4 py-4 text-slate-600">

                                {{ $maintenance->asset?->category?->name ?? '-' }}

                            </td>


                            {{-- LOCATION --}}

                            <td class="px-4 py-4 text-slate-600">

                                {{ $maintenance->asset?->location?->name ?? '-' }}

                            </td>


                            {{-- DATE --}}

                            <td class="whitespace-nowrap px-4 py-4 text-slate-600">

                                {{ $maintenance->maintenance_date?->format('d M Y') }}

                            </td>


                            {{-- TYPE --}}

                            <td class="px-4 py-4 text-slate-600">

                                {{ $maintenance->type }}

                            </td>


                            {{-- COST --}}

                            <td class="whitespace-nowrap px-4 py-4 text-slate-600">

                                Rp {{ number_format((float) $maintenance->cost, 0, ',', '.') }}

                            </td>


                            {{-- STATUS --}}

                            <td class="px-4 py-4">

                                @if ($maintenance->status === 'pending')

                                    <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">
                                        Pending
                                    </span>

                                @elseif ($maintenance->status === 'in_progress')

                                    <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-700">
                                        In Progress
                                    </span>

                                @elseif ($maintenance->status === 'completed')

                                    <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                        Completed
                                    </span>

                                @else

                                    <span class="inline-flex rounded-full bg-rose-100 px-2.5 py-1 text-xs font-medium text-rose-700">
                                        Cancelled
                                    </span>

                                @endif

                            </td>


                            {{-- ACTION --}}

                            <td class="max-w-xs px-4 py-4 text-slate-600">

                                <div class="line-clamp-2">
                                    {{ $maintenance->action_taken ?? '-' }}
                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="8"
                                class="px-4 py-12 text-center text-sm text-slate-500">

                                Tidak ada data maintenance yang sesuai.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if ($maintenances->hasPages())

            <div class="border-t border-slate-200 px-4 py-3">

                {{ $maintenances->links() }}

            </div>

        @endif

    </div>

</div>

@endsection