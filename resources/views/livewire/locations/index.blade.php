<div>

    <!-- ===================================================== -->
    <!-- HEADER -->
    <!-- ===================================================== -->

    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">

        <div>

            <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">
                Master Data
            </p>

            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">
                Location
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Kelola lokasi, cabang, atau tempat penyimpanan asset.
            </p>

        </div>


        <button
            type="button"
            wire:click="create"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
        >

            <svg
                width="17"
                height="17"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path d="M12 5v14"/>
                <path d="M5 12h14"/>
            </svg>

            Add Location

        </button>

    </div>


    <!-- ===================================================== -->
    <!-- FLASH MESSAGE -->
    <!-- ===================================================== -->

    @if (session()->has('success'))

        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3">

            <div class="flex items-center gap-2 text-sm text-emerald-700">

                <svg
                    width="17"
                    height="17"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="M5 12l4 4L19 6"/>
                </svg>

                {{ session('success') }}

            </div>

        </div>

    @endif


    @if (session()->has('error'))

        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3">

            <div class="flex items-center gap-2 text-sm text-red-700">

                <svg
                    width="17"
                    height="17"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 8v5"/>
                    <path d="M12 16h.01"/>
                </svg>

                {{ session('error') }}

            </div>

        </div>

    @endif


    <!-- ===================================================== -->
    <!-- FORM -->
    <!-- ===================================================== -->

    @if ($showForm)

        <div class="mb-5 rounded-xl border border-blue-100 bg-white p-5 shadow-sm">

            <div class="mb-5 flex items-center justify-between border-b border-slate-100 pb-4">

                <div>

                    <h2 class="text-sm font-semibold text-slate-900">

                        {{ $editingId ? 'Edit Location' : 'Tambah Location' }}

                    </h2>

                    <p class="mt-1 text-xs text-slate-400">
                        Lengkapi informasi lokasi asset.
                    </p>

                </div>


                <button
                    type="button"
                    wire:click="cancel"
                    class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                >

                    <svg
                        width="18"
                        height="18"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M6 6l12 12"/>
                        <path d="M18 6L6 18"/>
                    </svg>

                </button>

            </div>


            <form wire:submit="save">

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">


                    <!-- NAME -->

                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">

                            Location Name

                            <span class="text-red-500">
                                *
                            </span>

                        </label>


                        <input
                            type="text"
                            wire:model="name"
                            placeholder="Contoh: Cabang Semarang"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >


                        @error('name')

                            <p class="mt-1 text-xs text-red-500">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    <!-- CODE -->

                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">

                            Code

                            <span class="text-red-500">
                                *
                            </span>

                        </label>


                        <input
                            type="text"
                            wire:model="code"
                            placeholder="Contoh: SMG"
                            maxlength="20"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm uppercase text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >


                        <p class="mt-1 text-[11px] text-slate-400">
                            Kode akan otomatis diubah menjadi huruf kapital.
                        </p>


                        @error('code')

                            <p class="mt-1 text-xs text-red-500">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    <!-- ADDRESS -->

                    <div class="md:col-span-2">

                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Address
                        </label>


                        <textarea
                            wire:model="address"
                            rows="3"
                            placeholder="Alamat lokasi atau cabang..."
                            class="w-full resize-none rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        ></textarea>


                        @error('address')

                            <p class="mt-1 text-xs text-red-500">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    <!-- STATUS -->

                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Status
                        </label>


                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-slate-200 px-3 py-2.5">

                            <input
                                type="checkbox"
                                wire:model="is_active"
                                class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                            >

                            <span class="text-sm text-slate-700">
                                Location Active
                            </span>

                        </label>

                    </div>

                </div>


                <!-- ACTION -->

                <div class="mt-6 flex justify-end gap-2 border-t border-slate-100 pt-5">

                    <button
                        type="button"
                        wire:click="cancel"
                        class="rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
                    >
                        Cancel
                    </button>


                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                    >

                        <span wire:loading.remove wire:target="save">
                            {{ $editingId ? 'Update Location' : 'Save Location' }}
                        </span>


                        <span
                            wire:loading
                            wire:target="save"
                        >
                            Saving...
                        </span>

                    </button>

                </div>

            </form>

        </div>

    @endif


    <!-- ===================================================== -->
    <!-- TABLE -->
    <!-- ===================================================== -->

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">


        <!-- TABLE HEADER -->

        <div class="flex flex-col justify-between gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center">

            <div>

                <h2 class="text-sm font-semibold text-slate-900">
                    Location List
                </h2>

                <p class="mt-0.5 text-xs text-slate-400">
                    {{ $locations->total() }} location terdaftar
                </p>

            </div>


            <!-- SEARCH -->

            <div class="relative w-full sm:w-80">

                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">

                    <svg
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <circle cx="11" cy="11" r="7"/>
                        <path d="M20 20l-4-4"/>
                    </svg>

                </div>


                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    placeholder="Search location..."
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2.5 pl-9 pr-3 text-xs text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                >

            </div>

        </div>


        <!-- TABLE -->

        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead>

                    <tr class="border-b border-slate-100 bg-slate-50/70">

                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            #
                        </th>

                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Location
                        </th>

                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Code
                        </th>

                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Address
                        </th>

                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Status
                        </th>

                        <th class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse ($locations as $index => $location)

                        <tr
                            wire:key="location-{{ $location->id }}"
                            class="border-b border-slate-100 last:border-0 hover:bg-slate-50/70"
                        >

                            <!-- NUMBER -->

                            <td class="px-5 py-4 text-xs text-slate-400">

                                {{ $locations->firstItem() + $index }}

                            </td>


                            <!-- NAME -->

                            <td class="px-5 py-4">

                                <div class="flex items-center gap-3">

                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600">

                                        <svg
                                            width="16"
                                            height="16"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/>
                                            <circle cx="12" cy="10" r="2.5"/>
                                        </svg>

                                    </div>


                                    <p class="text-xs font-semibold text-slate-800">
                                        {{ $location->name }}
                                    </p>

                                </div>

                            </td>


                            <!-- CODE -->

                            <td class="px-5 py-4">

                                <span class="inline-flex rounded-md bg-slate-100 px-2 py-1 font-mono text-[10px] font-semibold text-slate-600">

                                    {{ $location->code }}

                                </span>

                            </td>


                            <!-- ADDRESS -->

                            <td class="max-w-sm px-5 py-4">

                                <p class="truncate text-xs text-slate-500">
                                    {{ $location->address ?: '-' }}
                                </p>

                            </td>


                            <!-- STATUS -->

                            <td class="px-5 py-4">

                                @if ($location->is_active)

                                    <button
                                        type="button"
                                        wire:click="toggleStatus({{ $location->id }})"
                                        class="inline-flex rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-semibold text-emerald-700"
                                    >
                                        Active
                                    </button>

                                @else

                                    <button
                                        type="button"
                                        wire:click="toggleStatus({{ $location->id }})"
                                        class="inline-flex rounded-full bg-slate-100 px-2 py-1 text-[10px] font-semibold text-slate-500"
                                    >
                                        Inactive
                                    </button>

                                @endif

                            </td>


                            <!-- ACTION -->

                            <td class="px-5 py-4">

                                <div class="flex justify-end gap-1">


                                    <!-- EDIT -->

                                    <button
                                        type="button"
                                        wire:click="edit({{ $location->id }})"
                                        title="Edit"
                                        class="rounded-lg p-2 text-slate-400 transition hover:bg-blue-50 hover:text-blue-600"
                                    >

                                        <svg
                                            width="15"
                                            height="15"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path d="M12 20h9"/>
                                            <path d="M16.5 3.5a2.1 2.1 0 013 3L8 18l-4 1 1-4Z"/>
                                        </svg>

                                    </button>


                                    <!-- DELETE -->

                                    <button
                                        type="button"
                                        wire:click="delete({{ $location->id }})"
                                        wire:confirm="Apakah Anda yakin ingin menghapus location {{ $location->name }}?"
                                        title="Delete"
                                        class="rounded-lg p-2 text-slate-400 transition hover:bg-red-50 hover:text-red-600"
                                    >

                                        <svg
                                            width="15"
                                            height="15"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path d="M3 6h18"/>
                                            <path d="M8 6V4h8v2"/>
                                            <path d="M19 6l-1 14H6L5 6"/>
                                            <path d="M10 11v5"/>
                                            <path d="M14 11v5"/>
                                        </svg>

                                    </button>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="px-5 py-14 text-center"
                            >

                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">

                                    <svg
                                        width="22"
                                        height="22"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/>
                                        <circle cx="12" cy="10" r="2.5"/>
                                    </svg>

                                </div>

                                <p class="mt-3 text-sm font-medium text-slate-600">
                                    Tidak ada location
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Belum ada data yang cocok dengan pencarian.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <!-- PAGINATION -->

        @if ($locations->hasPages())

            <div class="border-t border-slate-100 px-5 py-4">

                {{ $locations->links() }}

            </div>

        @endif

    </div>

</div>