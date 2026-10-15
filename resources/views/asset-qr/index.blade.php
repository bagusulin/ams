
@extends('layouts.admin')

@section('pageTitle', 'QR Asset')

@section('content')

<div class="w-full space-y-6">

    <div>
        <h1 class="text-xl font-semibold text-slate-800">
            QR Asset
        </h1>
        <p class="mt-1 text-sm text-slate-500">
            Lihat, buka detail, dan cetak label QR setiap asset.
        </p>
    </div>

    {{-- SEARCH --}}

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">

        <form
            action="{{ route('asset-qr.index') }}"
            method="GET"
            class="flex flex-col gap-3 sm:flex-row">

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari kode asset, brand, model, atau serial number..."
                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

            <button
                type="submit"
                class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                Cari
            </button>

            <a
                href="{{ route('asset-qr.index') }}"
                class="rounded-xl border border-slate-300 px-5 py-2.5 text-center text-sm font-medium text-slate-600 hover:bg-slate-50">
                Reset
            </a>

        </form>

    </div>


    {{-- ASSET TABLE --}}

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">

            <h2 class="font-semibold text-slate-800">
                Daftar Asset
            </h2>

            <span class="text-xs text-slate-500">
                Total: {{ $assets->total() }}
            </span>

        </div>

        <div class="overflow-x-auto">

            <table class="min-w-full text-sm">

                <thead class="border-b border-slate-200 bg-slate-50">

                    <tr class="text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <th class="px-4 py-3">QR</th>
                        <th class="px-4 py-3">Asset</th>
                        <th class="px-4 py-3">Category</th>
                        <th class="px-4 py-3">Location</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>

                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse ($assets as $asset)

                        <tr class="hover:bg-slate-50">

                            {{-- QR PREVIEW --}}

                            <td class="px-4 py-3">

                                <a
                                    href="{{ route('asset-qr.print', $asset) }}"
                                    title="Cetak label QR {{ $asset->asset_code }}">

                                    <img
                                        src="{{ route('asset-qr.image', $asset) }}"
                                        alt="QR {{ $asset->asset_code }}"
                                        width="68"
                                        height="68"
                                        loading="lazy"
                                        class="h-[68px] w-[68px] rounded-lg border border-slate-200 bg-white p-1">

                                </a>

                            </td>


                            {{-- ASSET --}}

                            <td class="px-4 py-4">

                                <div class="font-semibold text-slate-800">
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

                            <td class="px-4 py-4 text-slate-600">
                                {{ $asset->category?->name ?? '-' }}
                            </td>


                            {{-- LOCATION --}}

                            <td class="px-4 py-4 text-slate-600">
                                {{ $asset->location?->name ?? '-' }}
                            </td>


                            {{-- STATUS --}}

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


                            {{-- ACTIONS --}}

                            <td class="px-4 py-4">

                                <div class="flex flex-wrap items-center justify-end gap-2">

                                    <a
                                        href="{{ route('asset-qr.detail', $asset) }}"
                                        class="rounded-lg border border-blue-200 px-3 py-2 text-xs font-medium text-blue-700 hover:bg-blue-50">
                                        Detail
                                    </a>

                                    <a
                                        href="{{ route('asset-qr.print', $asset) }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="rounded-lg bg-slate-800 px-3 py-2 text-xs font-medium text-white hover:bg-slate-700">
                                        Cetak QR
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-slate-500">
                                Tidak ada asset yang ditemukan.
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
