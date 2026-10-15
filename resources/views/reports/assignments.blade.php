@extends('layouts.admin')

@section('pageTitle', 'Assignment & Return Report')

@section('content')

<div class="space-y-6">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

        <div>

            <h1 class="text-xl font-semibold text-slate-800">
                Assignment & Return Report
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Laporan asset yang sedang digunakan dan riwayat pengembalian.
            </p>

        </div>

        <div class="flex flex-wrap gap-2">

            <a
                href="{{ route('reports.assignments.excel', request()->query()) }}"
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
                href="{{ route('reports.assignments.pdf', request()->query()) }}"
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
    {{-- TAB --}}
    {{-- ========================================================= --}}

    <div class="rounded-2xl border border-slate-200 bg-white p-2 shadow-sm">

        <div class="flex flex-wrap gap-2">

            <a
                href="{{ route('reports.assignments', array_merge(request()->except('page', 'type'), ['type' => 'active'])) }}"
                class="rounded-xl px-4 py-2.5 text-sm font-medium transition
                {{ $type === 'active'
                    ? 'bg-blue-600 text-white'
                    : 'text-slate-600 hover:bg-slate-100' }}">

                Assignment Aktif

            </a>


            <a
                href="{{ route('reports.assignments', array_merge(request()->except('page', 'type'), ['type' => 'returned'])) }}"
                class="rounded-xl px-4 py-2.5 text-sm font-medium transition
                {{ $type === 'returned'
                    ? 'bg-blue-600 text-white'
                    : 'text-slate-600 hover:bg-slate-100' }}">

                Riwayat Return

            </a>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- FILTER --}}
    {{-- ========================================================= --}}

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

        <form
            method="GET"
            action="{{ route('reports.assignments') }}"
            class="grid gap-4 lg:grid-cols-2 xl:grid-cols-5">

            <input
                type="hidden"
                name="type"
                value="{{ $type }}">


            {{-- SEARCH --}}
            <div class="xl:col-span-2">

                <label class="mb-1.5 block text-sm font-medium text-slate-700">
                    Search
                </label>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Asset code, user, brand, serial..."
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

            </div>


            {{-- USER --}}
            <div>

                <label class="mb-1.5 block text-sm font-medium text-slate-700">
                    User
                </label>

                <select
                    name="user_id"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                    <option value="">
                        Semua User
                    </option>

                    @foreach ($users as $user)

                        <option
                            value="{{ $user->id }}"
                            @selected(request('user_id') == $user->id)>

                            {{ $user->name }}

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
                    {{ $type === 'active' ? 'Assignment' : 'Return' }} Dari
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
                    {{ $type === 'active' ? 'Assignment' : 'Return' }} Sampai
                </label>

                <input
                    type="date"
                    name="date_to"
                    value="{{ request('date_to') }}"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

            </div>


            {{-- BUTTONS --}}
            <div class="flex items-end gap-2 lg:col-span-2 xl:col-span-5">

                <button
                    type="submit"
                    class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700">

                    Filter

                </button>


                <a
                    href="{{ route('reports.assignments', ['type' => $type]) }}"
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
                {{ $type === 'active'
                    ? 'Assignment Aktif'
                    : 'Total Return' }}
            </div>

            <div class="mt-2 text-2xl font-bold text-slate-800">
                {{ $totalAssignments }}
            </div>

        </div>


        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="text-sm text-slate-500">
                Mode Report
            </div>

            <div class="mt-2 text-lg font-semibold text-slate-800">

                {{ $type === 'active'
                    ? 'Assignment Aktif'
                    : 'Riwayat Return' }}

            </div>

        </div>


        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="text-sm text-slate-500">
                Generate
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
                            User
                        </th>

                        <th class="px-4 py-3">
                            Location
                        </th>

                        <th class="px-4 py-3">
                            {{ $type === 'active'
                                ? 'Assigned'
                                : 'Assigned' }}
                        </th>

                        @if ($type === 'returned')

                            <th class="px-4 py-3">
                                Returned
                            </th>

                        @endif

                        <th class="px-4 py-3">
                            Durasi
                        </th>

                        <th class="px-4 py-3">
                            Catatan
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse ($assignments as $assignment)

                        @php

                            $assignedDate = $assignment->assigned_at;

                            $duration = $assignedDate
                                ? (
                                    $type === 'active'
                                        ? $assignedDate->diffInDays(now())
                                        : ($assignment->returned_at
                                            ? $assignedDate->diffInDays($assignment->returned_at)
                                            : 0)
                                )
                                : 0;

                        @endphp

                        <tr class="hover:bg-slate-50">

                            {{-- ASSET --}}
                            <td class="px-4 py-4">

                                <div class="font-medium text-slate-800">

                                    {{ $assignment->asset?->asset_code ?? '-' }}

                                </div>

                                <div class="mt-1 text-xs text-slate-500">

                                    {{ $assignment->asset?->brand }}
                                    {{ $assignment->asset?->model }}

                                </div>

                                <div class="mt-1 text-xs text-slate-400">

                                    SN:
                                    {{ $assignment->asset?->serial_number ?? '-' }}

                                </div>

                            </td>


                            {{-- CATEGORY --}}
                            <td class="px-4 py-4 text-slate-600">

                                {{ $assignment->asset?->category?->name ?? '-' }}

                            </td>


                            {{-- USER --}}
                            <td class="px-4 py-4">

                                <div class="font-medium text-slate-700">

                                    {{ $assignment->user?->name ?? '-' }}

                                </div>

                                <div class="mt-1 text-xs text-slate-400">

                                    {{ $assignment->user?->email ?? '-' }}

                                </div>

                            </td>


                            {{-- LOCATION --}}
                            <td class="px-4 py-4 text-slate-600">

                                {{ $assignment->location?->name
                                    ?? $assignment->asset?->location?->name
                                    ?? '-' }}

                            </td>


                            {{-- ASSIGNED --}}
                            <td class="whitespace-nowrap px-4 py-4 text-slate-600">

                                {{ $assignment->assigned_at?->format('d M Y') ?? '-' }}

                            </td>


                            {{-- RETURNED --}}
                            @if ($type === 'returned')

                                <td class="whitespace-nowrap px-4 py-4 text-slate-600">

                                    {{ $assignment->returned_at?->format('d M Y') ?? '-' }}

                                </td>

                            @endif


                            {{-- DURATION --}}
                            <td class="whitespace-nowrap px-4 py-4">

                                <span class="font-medium text-slate-700">

                                    {{ $duration }} hari

                                </span>

                            </td>


                            {{-- NOTES --}}
                            <td class="max-w-xs px-4 py-4 text-slate-600">

                                <div class="line-clamp-2">

                                    {{ $assignment->notes ?? '-' }}

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="{{ $type === 'returned' ? 8 : 7 }}"
                                class="px-4 py-12 text-center text-sm text-slate-500">

                                Tidak ada data
                                {{ $type === 'active'
                                    ? 'assignment aktif'
                                    : 'riwayat return' }}
                                yang sesuai.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if ($assignments->hasPages())

            <div class="border-t border-slate-200 px-4 py-3">

                {{ $assignments->links() }}

            </div>

        @endif

    </div>

</div>

@endsection