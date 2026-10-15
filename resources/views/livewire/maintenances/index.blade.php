<div class="space-y-6">

    {{-- ========================================================= --}}
    {{-- FLASH MESSAGE --}}
    {{-- ========================================================= --}}

    @if (session()->has('success'))
        <div
            class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div
            class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            {{ session('error') }}
        </div>
    @endif

    @error('delete')
        <div
            class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            {{ $message }}
        </div>
    @enderror


    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

        <div>
            <h1 class="text-xl font-semibold text-slate-800">
                Maintenance
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Kelola riwayat dan status maintenance asset.
            </p>
        </div>

        <button
            type="button"
            wire:click="openCreate"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700">
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
                    d="M12 4v16m8-8H4" />
            </svg>

            Tambah Maintenance
        </button>

    </div>


    {{-- ========================================================= --}}
    {{-- FILTER --}}
    {{-- ========================================================= --}}

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">

        <div class="grid gap-4 md:grid-cols-[1fr_220px]">

            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">
                    Cari
                </label>

                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Asset code, brand, model, tipe maintenance..."
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">
                    Status
                </label>

                <select
                    wire:model.live="statusFilter"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                    <option value="all">
                        Semua Status
                    </option>

                    <option value="pending">
                        Pending
                    </option>

                    <option value="in_progress">
                        In Progress
                    </option>

                    <option value="completed">
                        Completed
                    </option>

                    <option value="cancelled">
                        Cancelled
                    </option>

                </select>
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

                        <th class="px-4 py-3 text-right">
                            Aksi
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

                                <div class="mt-0.5 text-xs text-slate-500">
                                    {{ $maintenance->asset?->brand }}
                                    {{ $maintenance->asset?->model }}
                                </div>

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
                            <td class="px-4 py-4">

                                <div class="flex items-center justify-end gap-2">

                                    <button
                                        type="button"
                                        wire:click="openDetail({{ $maintenance->id }})"
                                        class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50">
                                        Detail
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="openEdit({{ $maintenance->id }})"
                                        class="rounded-lg border border-blue-200 px-3 py-1.5 text-xs font-medium text-blue-600 hover:bg-blue-50">
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="deleteMaintenance({{ $maintenance->id }})"
                                        wire:confirm="Yakin ingin menghapus data maintenance ini?"
                                        class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-50">
                                        Hapus
                                    </button>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="px-4 py-12 text-center">

                                <div class="text-sm font-medium text-slate-600">
                                    Belum ada data maintenance.
                                </div>

                                <div class="mt-1 text-xs text-slate-400">
                                    Silakan tambahkan maintenance baru.
                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if ($maintenances->hasPages())

            <div class="border-t border-slate-200 px-4 py-3">

                {{ $maintenances->links() }}

            </div>

        @endif

    </div>


    {{-- ========================================================= --}}
    {{-- FORM MODAL --}}
    {{-- ========================================================= --}}

    @if ($showForm)

        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">

            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white shadow-2xl">

                {{-- HEADER --}}
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">

                    <div>

                        <h2 class="text-lg font-semibold text-slate-800">

                            {{ $editingId ? 'Edit Maintenance' : 'Tambah Maintenance' }}

                        </h2>

                        <p class="mt-1 text-xs text-slate-500">

                            {{ $editingId
                                ? 'Perbarui informasi maintenance.'
                                : 'Buat catatan maintenance untuk asset.'
                            }}

                        </p>

                    </div>

                    <button
                        type="button"
                        wire:click="closeForm"
                        class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 18L18 6M6 6l12 12" />

                        </svg>

                    </button>

                </div>


                {{-- BODY --}}
                <form wire:submit="save" class="space-y-5 p-5">

                    {{-- ASSET --}}
                    <div>

                        <label class="mb-1.5 block text-sm font-medium text-slate-700">
                            Asset
                        </label>

                        <select
                            wire:model="asset_id"
                            {{ $editingId ? 'disabled' : '' }}
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 disabled:bg-slate-100 disabled:text-slate-500">

                            <option value="">
                                Pilih Asset
                            </option>

                            @foreach ($availableAssets as $asset)

                                <option value="{{ $asset->id }}">

                                    {{ $asset->asset_code }}
                                    -
                                    {{ $asset->brand }}
                                    {{ $asset->model }}

                                </option>

                            @endforeach

                        </select>

                        @if ($editingId)

                            <p class="mt-1 text-xs text-slate-400">
                                Asset tidak dapat diganti saat edit maintenance.
                            </p>

                        @endif

                        @error('asset_id')

                            <p class="mt-1 text-xs text-rose-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- DATE + TYPE --}}
                    <div class="grid gap-4 md:grid-cols-2">

                        <div>

                            <label class="mb-1.5 block text-sm font-medium text-slate-700">
                                Tanggal Maintenance
                            </label>

                            <input
                                type="date"
                                wire:model="maintenance_date"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                            @error('maintenance_date')

                                <p class="mt-1 text-xs text-rose-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        <div>

                            <label class="mb-1.5 block text-sm font-medium text-slate-700">
                                Tipe
                            </label>

                            <select
                                wire:model="type"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                                <option value="">
                                    Pilih Tipe
                                </option>

                                <option value="Hardware">
                                    Hardware
                                </option>

                                <option value="Software">
                                    Software
                                </option>

                                <option value="Network">
                                    Network
                                </option>

                                <option value="Preventive">
                                    Preventive
                                </option>

                                <option value="Other">
                                    Other
                                </option>

                            </select>

                            @error('type')

                                <p class="mt-1 text-xs text-rose-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>

                    </div>


                    {{-- STATUS + COST --}}
                    <div class="grid gap-4 md:grid-cols-2">

                        <div>

                            <label class="mb-1.5 block text-sm font-medium text-slate-700">
                                Status
                            </label>

                            <select
                                wire:model="status"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                                <option value="pending">
                                    Pending
                                </option>

                                <option value="in_progress">
                                    In Progress
                                </option>

                                <option value="completed">
                                    Completed
                                </option>

                                <option value="cancelled">
                                    Cancelled
                                </option>

                            </select>

                            @error('status')

                                <p class="mt-1 text-xs text-rose-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        <div>

                            <label class="mb-1.5 block text-sm font-medium text-slate-700">
                                Biaya
                            </label>

                            <input
                                type="number"
                                min="0"
                                step="0.01"
                                wire:model="cost"
                                class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                            @error('cost')

                                <p class="mt-1 text-xs text-rose-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>

                    </div>


                    {{-- DESCRIPTION --}}
                    <div>

                        <label class="mb-1.5 block text-sm font-medium text-slate-700">
                            Deskripsi Masalah
                        </label>

                        <textarea
                            rows="4"
                            wire:model="description"
                            placeholder="Jelaskan masalah atau kondisi asset..."
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></textarea>

                        @error('description')

                            <p class="mt-1 text-xs text-rose-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- ACTION --}}
                    <div>

                        <label class="mb-1.5 block text-sm font-medium text-slate-700">
                            Tindakan
                        </label>

                        <textarea
                            rows="4"
                            wire:model="action_taken"
                            placeholder="Jelaskan tindakan/perbaikan yang dilakukan..."
                            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></textarea>

                        @error('action_taken')

                            <p class="mt-1 text-xs text-rose-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- FOOTER --}}
                    <div class="flex justify-end gap-3 border-t border-slate-200 pt-4">

                        <button
                            type="button"
                            wire:click="closeForm"
                            class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">

                            Batal

                        </button>

                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50">

                            <span wire:loading.remove>
                                {{ $editingId ? 'Simpan Perubahan' : 'Simpan Maintenance' }}
                            </span>

                            <span wire:loading>
                                Memproses...
                            </span>

                        </button>

                    </div>

                </form>

            </div>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- DETAIL MODAL --}}
    {{-- ========================================================= --}}

    @if ($showDetail && $detailMaintenance)

        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">

            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white shadow-2xl">

                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">

                    <div>

                        <h2 class="text-lg font-semibold text-slate-800">
                            Detail Maintenance
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Informasi lengkap maintenance asset.
                        </p>

                    </div>

                    <button
                        type="button"
                        wire:click="closeDetail"
                        class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 18L18 6M6 6l12 12" />

                        </svg>

                    </button>

                </div>


                <div class="space-y-5 p-5">

                    {{-- ASSET --}}
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">

                        <div class="text-xs font-medium uppercase tracking-wider text-slate-400">
                            Asset
                        </div>

                        <div class="mt-1 text-lg font-semibold text-slate-800">
                            {{ $detailMaintenance->asset?->asset_code ?? '-' }}
                        </div>

                        <div class="mt-1 text-sm text-slate-500">
                            {{ $detailMaintenance->asset?->brand }}
                            {{ $detailMaintenance->asset?->model }}
                        </div>

                    </div>


                    {{-- INFO --}}
                    <div class="grid gap-4 md:grid-cols-2">

                        <div>
                            <div class="text-xs font-medium uppercase tracking-wider text-slate-400">
                                Tanggal
                            </div>

                            <div class="mt-1 text-sm font-medium text-slate-700">
                                {{ $detailMaintenance->maintenance_date?->format('d M Y') }}
                            </div>
                        </div>


                        <div>
                            <div class="text-xs font-medium uppercase tracking-wider text-slate-400">
                                Tipe
                            </div>

                            <div class="mt-1 text-sm font-medium text-slate-700">
                                {{ $detailMaintenance->type }}
                            </div>
                        </div>


                        <div>
                            <div class="text-xs font-medium uppercase tracking-wider text-slate-400">
                                Biaya
                            </div>

                            <div class="mt-1 text-sm font-medium text-slate-700">
                                Rp {{ number_format((float) $detailMaintenance->cost, 0, ',', '.') }}
                            </div>
                        </div>


                        <div>
                            <div class="text-xs font-medium uppercase tracking-wider text-slate-400">
                                Status
                            </div>

                            <div class="mt-1">

                                @if ($detailMaintenance->status === 'pending')

                                    <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">
                                        Pending
                                    </span>

                                @elseif ($detailMaintenance->status === 'in_progress')

                                    <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-700">
                                        In Progress
                                    </span>

                                @elseif ($detailMaintenance->status === 'completed')

                                    <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                        Completed
                                    </span>

                                @else

                                    <span class="inline-flex rounded-full bg-rose-100 px-2.5 py-1 text-xs font-medium text-rose-700">
                                        Cancelled
                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>


                    {{-- DESCRIPTION --}}
                    <div>

                        <div class="text-xs font-medium uppercase tracking-wider text-slate-400">
                            Deskripsi
                        </div>

                        <div class="mt-2 rounded-xl border border-slate-200 bg-white p-4 text-sm leading-6 text-slate-600">

                            {{ $detailMaintenance->description ?: '-' }}

                        </div>

                    </div>


                    {{-- ACTION --}}
                    <div>

                        <div class="text-xs font-medium uppercase tracking-wider text-slate-400">
                            Tindakan
                        </div>

                        <div class="mt-2 rounded-xl border border-slate-200 bg-white p-4 text-sm leading-6 text-slate-600">

                            {{ $detailMaintenance->action_taken ?: '-' }}

                        </div>

                    </div>


                    {{-- LOCATION --}}
                    <div class="grid gap-4 md:grid-cols-2">

                        <div>

                            <div class="text-xs font-medium uppercase tracking-wider text-slate-400">
                                Location
                            </div>

                            <div class="mt-1 text-sm font-medium text-slate-700">

                                {{ $detailMaintenance->asset?->location?->name ?? '-' }}

                            </div>

                        </div>


                        <div>

                            <div class="text-xs font-medium uppercase tracking-wider text-slate-400">
                                Category
                            </div>

                            <div class="mt-1 text-sm font-medium text-slate-700">

                                {{ $detailMaintenance->asset?->category?->name ?? '-' }}

                            </div>

                        </div>

                    </div>


                    {{-- FOOTER --}}
                    <div class="flex justify-end border-t border-slate-200 pt-4">

                        <button
                            type="button"
                            wire:click="closeDetail"
                            class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">

                            Tutup

                        </button>

                    </div>

                </div>

            </div>

        </div>

    @endif

</div>