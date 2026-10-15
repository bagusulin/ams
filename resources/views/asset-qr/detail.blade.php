
@extends('layouts.admin')

@section('pageTitle', 'Detail Asset')

@section('content')

<div class="w-full space-y-6">

    {{-- HEADER --}}

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-xl font-semibold text-slate-800">
                Detail Asset
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Informasi asset dari hasil pemindaian QR.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">

            <a
                href="{{ route('asset-qr.print', $asset) }}"
                target="_blank"
                rel="noopener"
                class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                Cetak QR
            </a>

            <a
                href="{{ route('asset-qr.index') }}"
                class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">
                Kembali
            </a>

        </div>

    </div>


    {{-- ASSET OVERVIEW --}}

    <div class="grid gap-6 lg:grid-cols-[1fr_250px]">

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-start">

                <div class="min-w-0 flex-1">

                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Kode Asset
                    </p>

                    <h2 class="mt-1 text-2xl font-bold text-slate-900">
                        {{ $asset->asset_code }}
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ $asset->brand }} {{ $asset->model }}
                    </p>

                    <div class="mt-4 flex flex-wrap gap-2">

                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">
                            {{ $asset->category?->name ?? 'Tanpa kategori' }}
                        </span>

                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                            {{ ucfirst($asset->status) }}
                        </span>

                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                            Kondisi: {{ ucfirst($asset->condition) }}
                        </span>

                    </div>

                </div>

            </div>

        </div>


        {{-- QR --}}

        <div class="rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm">

            <img
                src="{{ route('asset-qr.image', $asset) }}"
                alt="QR {{ $asset->asset_code }}"
                width="180"
                height="180"
                class="mx-auto h-44 w-44">

            <p class="mt-2 text-sm font-semibold text-slate-800">
                {{ $asset->asset_code }}
            </p>

            <p class="mt-1 text-xs text-slate-500">
                QR Code Asset
            </p>

        </div>

    </div>


    {{-- DETAIL INFORMATION --}}

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

        <h2 class="text-base font-semibold text-slate-800">
            Informasi Asset
        </h2>

        <div class="mt-5 grid gap-x-8 gap-y-5 sm:grid-cols-2 xl:grid-cols-3">

            <div>
                <p class="text-xs text-slate-400">Serial Number</p>
                <p class="mt-1 break-words text-sm font-medium text-slate-700">
                    {{ $asset->serial_number ?: '-' }}
                </p>
            </div>

            <div>
                <p class="text-xs text-slate-400">Category</p>
                <p class="mt-1 text-sm font-medium text-slate-700">
                    {{ $asset->category?->name ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-xs text-slate-400">Location</p>
                <p class="mt-1 text-sm font-medium text-slate-700">
                    {{ $asset->location?->name ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-xs text-slate-400">Vendor</p>
                <p class="mt-1 text-sm font-medium text-slate-700">
                    {{ $asset->vendor?->name ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-xs text-slate-400">Tanggal Pembelian</p>
                <p class="mt-1 text-sm font-medium text-slate-700">
                    {{ $asset->purchase_date?->format('d M Y') ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-xs text-slate-400">Kondisi</p>
                <p class="mt-1 text-sm font-medium text-slate-700">
                    {{ ucfirst($asset->condition) }}
                </p>
            </div>

            <div>
                <p class="text-xs text-slate-400">Awal Sewa</p>
                <p class="mt-1 text-sm font-medium text-slate-700">
                    {{ $asset->rental_start_date?->format('d M Y') ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-xs text-slate-400">Akhir Sewa</p>
                <p class="mt-1 text-sm font-medium text-slate-700">
                    {{ $asset->rental_end_date?->format('d M Y') ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-xs text-slate-400">Biaya Sewa</p>
                <p class="mt-1 text-sm font-medium text-slate-700">
                    @if ($asset->rental_cost !== null)
                        Rp {{ number_format((float) $asset->rental_cost, 0, ',', '.') }}
                    @else
                        -
                    @endif
                </p>
            </div>

            <div class="sm:col-span-2 xl:col-span-3">
                <p class="text-xs text-slate-400">Catatan</p>
                <p class="mt-1 whitespace-pre-line text-sm text-slate-700">
                    {{ $asset->notes ?: '-' }}
                </p>
            </div>

        </div>

    </div>


    {{-- CURRENT ASSIGNMENT --}}

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

        <h2 class="text-base font-semibold text-slate-800">
            Penggunaan Saat Ini
        </h2>

        @if ($asset->currentAssignment)

            <div class="mt-4 grid gap-4 sm:grid-cols-3">

                <div>
                    <p class="text-xs text-slate-400">User</p>
                    <p class="mt-1 text-sm font-medium text-slate-700">
                        {{ $asset->currentAssignment->user?->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-slate-400">Lokasi Assignment</p>
                    <p class="mt-1 text-sm font-medium text-slate-700">
                        {{ $asset->currentAssignment->location?->name ?? $asset->location?->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-slate-400">Tanggal Assignment</p>
                    <p class="mt-1 text-sm font-medium text-slate-700">
                        {{ $asset->currentAssignment->assigned_at?->format('d M Y') ?? '-' }}
                    </p>
                </div>

            </div>

        @else

            <p class="mt-4 text-sm text-slate-500">
                Asset tidak memiliki assignment aktif.
            </p>

        @endif

    </div>


    {{-- ASSIGNMENT HISTORY --}}

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

        <h2 class="text-base font-semibold text-slate-800">
            Riwayat Assignment & Return
        </h2>

        <div class="mt-4 overflow-x-auto">

            <table class="min-w-full text-sm">

                <thead class="border-b border-slate-200 bg-slate-50">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th class="px-3 py-3">User</th>
                        <th class="px-3 py-3">Lokasi</th>
                        <th class="px-3 py-3">Assignment</th>
                        <th class="px-3 py-3">Return</th>
                        <th class="px-3 py-3">Catatan</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse ($assignments as $assignment)

                        <tr>
                            <td class="px-3 py-3 text-slate-700">
                                {{ $assignment->user?->name ?? '-' }}
                            </td>

                            <td class="px-3 py-3 text-slate-600">
                                {{ $assignment->location?->name ?? '-' }}
                            </td>

                            <td class="whitespace-nowrap px-3 py-3 text-slate-600">
                                {{ $assignment->assigned_at?->format('d M Y') ?? '-' }}
                            </td>

                            <td class="whitespace-nowrap px-3 py-3 text-slate-600">
                                {{ $assignment->returned_at?->format('d M Y') ?? 'Masih aktif' }}
                            </td>

                            <td class="px-3 py-3 text-slate-600">
                                {{ $assignment->notes ?: '-' }}
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="px-3 py-8 text-center text-slate-500">
                                Belum ada riwayat assignment.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- MAINTENANCE HISTORY --}}

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

        <h2 class="text-base font-semibold text-slate-800">
            Riwayat Maintenance
        </h2>

        <div class="mt-4 overflow-x-auto">

            <table class="min-w-full text-sm">

                <thead class="border-b border-slate-200 bg-slate-50">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th class="px-3 py-3">Tanggal</th>
                        <th class="px-3 py-3">Tipe</th>
                        <th class="px-3 py-3">Status</th>
                        <th class="px-3 py-3">Biaya</th>
                        <th class="px-3 py-3">Tindakan</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse ($maintenances as $maintenance)

                        <tr>
                            <td class="whitespace-nowrap px-3 py-3 text-slate-600">
                                {{ $maintenance->maintenance_date?->format('d M Y') ?? '-' }}
                            </td>

                            <td class="px-3 py-3 text-slate-700">
                                {{ $maintenance->type }}
                            </td>

                            <td class="px-3 py-3 text-slate-700">
                                {{ ucfirst(str_replace('_', ' ', $maintenance->status)) }}
                            </td>

                            <td class="whitespace-nowrap px-3 py-3 text-slate-600">
                                Rp {{ number_format((float) $maintenance->cost, 0, ',', '.') }}
                            </td>

                            <td class="px-3 py-3 text-slate-600">
                                {{ $maintenance->action_taken ?: '-' }}
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="px-3 py-8 text-center text-slate-500">
                                Belum ada riwayat maintenance.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- RECENT ASSET HISTORY --}}

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

        <h2 class="text-base font-semibold text-slate-800">
            Asset History
        </h2>

        <div class="mt-4 space-y-4">

            @forelse ($histories as $history)

                <div class="flex gap-3">

                    <div class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-blue-500"></div>

                    <div class="min-w-0 flex-1">

                        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">

                            <p class="text-sm font-medium text-slate-800">
                                {{ ucfirst(str_replace('_', ' ', $history->action)) }}
                            </p>

                            <p class="text-xs text-slate-400">
                                {{ $history->created_at?->format('d M Y H:i') }}
                            </p>

                        </div>

                        <p class="mt-1 text-sm text-slate-600">
                            {{ $history->description ?: '-' }}
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            Oleh: {{ $history->user?->name ?? 'Sistem' }}
                        </p>

                    </div>

                </div>

            @empty

                <p class="text-sm text-slate-500">
                    Belum ada asset history.
                </p>

            @endforelse

        </div>

    </div>

</div>

@endsection
