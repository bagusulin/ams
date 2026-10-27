<div
x-data
    x-on:scroll-to-asset-form.window="
        $nextTick(() => {
            document.getElementById('asset-form')?.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        })
    ">

    <!-- ===================================================== -->
    <!-- HEADER -->
    <!-- ===================================================== -->

    <div class="mb-6 flex flex-col justify-between gap-4 lg:flex-row lg:items-end">

        <div>

            <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">
                Master Data
            </p>

            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">
                Asset
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Kelola seluruh asset perusahaan seperti laptop, HP, motor, dan perangkat lainnya.
            </p>

        </div>


        <div class="flex flex-wrap items-center gap-3">

            @can('assets.import')
                <a
                    href="{{ route('assets.import.index') }}"
                    class="inline-flex items-center justify-center gap-2
                        rounded-lg border border-blue-200 bg-white px-4 py-2.5
                        text-sm font-semibold text-[#225199]
                        shadow-sm transition hover:bg-blue-50"
                >
                    <svg
                        width="17"
                        height="17"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        aria-hidden="true"
                    >
                        <path d="M12 16V4"/>
                        <path d="m7 9 5-5 5 5"/>
                        <path d="M4 16v4h16v-4"/>
                    </svg>

                    Import HP CSV
                </a>
            @endcan

            @can('assets.create')
                <button
                    type="button"
                    wire:click="create"
                    class="inline-flex items-center justify-center gap-2
                        rounded-lg bg-[#225199] px-4 py-2.5
                        text-sm font-semibold text-white shadow-sm
                        transition hover:bg-[#193f7a]"
                >
                    <svg
                        width="17"
                        height="17"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        aria-hidden="true"
                    >
                        <path d="M12 5v14"/>
                        <path d="M5 12h14"/>
                    </svg>

                    Add Asset
                </button>
            @endcan

        </div>

    </div>


    <!-- ===================================================== -->
    <!-- FLASH -->
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


    <!-- ===================================================== -->
    <!-- FILTER BAR -->
    <!-- ===================================================== -->

    <div class="mb-5 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">

        <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4">


            <!-- Search -->

            <div class="relative">

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
                    placeholder="Search asset code, brand, model..."
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2.5 pl-9 pr-3 text-xs text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                >

            </div>


            <!-- Category -->

            <select
                wire:model.live="categoryFilter"
                class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs text-slate-700 outline-none focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
            >

                <option value="">
                    Semua Category
                </option>

                @foreach ($categories as $category)

                    <option value="{{ $category->id }}">
                        {{ $category->name }}
                    </option>

                @endforeach

            </select>


            <!-- Location -->

            <select
                wire:model.live="locationFilter"
                class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs text-slate-700 outline-none focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
            >

                <option value="">
                    Semua Location
                </option>

                @foreach ($locations as $location)

                    <option value="{{ $location->id }}">
                        {{ $location->name }}
                    </option>

                @endforeach

            </select>


            <!-- Status -->

            <select
                wire:model.live="statusFilter"
                class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs text-slate-700 outline-none focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
            >

                <option value="">
                    Semua Status
                </option>

                <option value="available">
                    Available
                </option>

                <option value="assigned">
                    Assigned
                </option>

                <option value="maintenance">
                    Maintenance
                </option>

                <option value="lost">
                    Lost
                </option>

            </select>

        </div>

    </div>


    <!-- ===================================================== -->
    <!-- FORM -->
    <!-- ===================================================== -->

    @if ($showForm && (
        auth()->user()->can('assets.create')
        || ($editingId && auth()->user()->can('assets.update'))
    ))

        <div class="mb-5 scroll-mt-24 rounded-xl border border-blue-100 bg-white p-5 shadow-sm">

            <div class="mb-5 flex items-center justify-between border-b border-slate-100 pb-4">

                <div>

                    <h2 class="text-sm font-semibold text-slate-900">

                        {{ $editingId ? 'Edit Asset' : 'Tambah Asset' }}

                    </h2>

                    <p class="mt-1 text-xs text-slate-400">
                        Lengkapi informasi asset.
                    </p>

                </div>


                <button
                    type="button"
                    wire:click="cancel"
                    class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700"
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


                    <!-- Asset Code -->

                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">

                            Asset Code

                            <span class="text-red-500">*</span>

                        </label>

                        <input
                            type="text"
                            wire:model="asset_code"
                            placeholder="AST-LPT-00001"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm uppercase outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                        @error('asset_code')

                            <p class="mt-1 text-xs text-red-500">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    <!-- Category -->

                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">

                            Category

                            <span class="text-red-500">*</span>

                        </label>

                        <select
                            wire:model.live="category_id"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                            <option value="">
                                Pilih Category
                            </option>

                            @foreach ($categories as $category)

                                <option value="{{ $category->id }}">
                                    {{ $category->name }}
                                </option>

                            @endforeach

                        </select>

                        @if ($selectedCategory?->default_rental_years)
                            <p class="mt-1.5 text-xs text-blue-600">
                                Masa sewa default:
                                {{ $selectedCategory->default_rental_years }} tahun.
                                Tanggal akhir dihitung otomatis setelah tanggal mulai diisi.
                            </p>
                        @endif

                        @error('category_id')

                            <p class="mt-1 text-xs text-red-500">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    <!-- Location -->

                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Location
                        </label>

                        <select
                            wire:model="location_id"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                            <option value="">
                                Pilih Location
                            </option>

                            @foreach ($locations as $location)

                                <option value="{{ $location->id }}">
                                    {{ $location->name }}
                                </option>

                            @endforeach

                        </select>

                        @error('location_id')

                            <p class="mt-1 text-xs text-red-500">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    <!-- Vendor -->

                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Vendor
                        </label>

                        <select
                            wire:model="vendor_id"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                            <option value="">
                                Pilih Vendor
                            </option>

                            @foreach ($vendors as $vendor)

                                <option value="{{ $vendor->id }}">
                                    {{ $vendor->name }}
                                </option>

                            @endforeach

                        </select>

                        @error('vendor_id')

                            <p class="mt-1 text-xs text-red-500">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    <!-- Brand -->

                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Brand
                        </label>

                        <input
                            type="text"
                            wire:model="brand"
                            placeholder="HP, Lenovo, Samsung, Honda..."
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                    </div>


                    <!-- Model -->

                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Model
                        </label>

                        <input
                            type="text"
                            wire:model="model"
                            placeholder="ProBook 440 G9"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                    </div>


                    <!-- Serial -->

                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Serial Number
                        </label>

                        <input
                            type="text"
                            wire:model="serial_number"
                            placeholder="Serial Number"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                        @error('serial_number')

                            <p class="mt-1 text-xs text-red-500">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    <!-- Status -->

                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Status
                        </label>

                        <select
                            wire:model="status"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                            <option value="available">
                                Available
                            </option>

                            <option value="assigned">
                                Assigned
                            </option>

                            <option value="maintenance">
                                Maintenance
                            </option>

                            <option value="lost">
                                Lost
                            </option>

                        </select>

                    </div>


                    <!-- Condition -->

                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Condition
                        </label>

                        <select
                            wire:model="condition"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                            <option value="good">
                                Good
                            </option>

                            <option value="fair">
                                Fair
                            </option>

                            <option value="damaged">
                                Damaged
                            </option>

                        </select>

                    </div>


                    <!-- Purchase Date -->

                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Purchase Date
                        </label>

                        <input
                            type="date"
                            wire:model="purchase_date"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                    </div>


                    <!-- Rental Cost -->

                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Rental Cost
                        </label>

                        <input
                            type="number"
                            min="0"
                            step="0.01"
                            wire:model="rental_cost"
                            placeholder="0"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                        @error('rental_cost')

                            <p class="mt-1 text-xs text-red-500">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    <!-- Rental Start -->

                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Rental Start
                        </label>

                        <input
                            type="date"
                            wire:model.live="rental_start_date"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                    </div>


                    <!-- Rental End -->

                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Rental End (Otomatis)
                        </label>

                        <input
                            type="date"
                            wire:model="rental_end_date"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                        @error('rental_end_date')

                            <p class="mt-1.5 text-xs text-slate-500">
                                Tanggal akhir mengikuti masa sewa default kategori.
                                Jika kontrak berbeda, tanggal dapat disesuaikan.
                            </p>

                        @enderror

                    </div>


                    <!-- Notes -->

                    <div class="md:col-span-2">

                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Notes
                        </label>

                        <textarea
                            wire:model="notes"
                            rows="3"
                            placeholder="Keterangan tambahan..."
                            class="w-full resize-none rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        ></textarea>

                    </div>

                </div>


                <!-- BUTTON -->

                <div class="mt-6 flex justify-end gap-2 border-t border-slate-100 pt-5">

                    <button
                        type="button"
                        wire:click="cancel"
                        class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50"
                    >
                        Cancel
                    </button>


                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-60"
                    >

                        <span wire:loading.remove wire:target="save">

                            {{ $editingId ? 'Update Asset' : 'Save Asset' }}

                        </span>


                        <span wire:loading wire:target="save">
                            Saving...
                        </span>

                    </button>

                </div>

            </form>

        </div>

    @endif


    <!-- ===================================================== -->
    <!-- ASSET TABLE -->
    <!-- ===================================================== -->

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">


        <div class="border-b border-slate-200 px-5 py-4">

            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">

                <div>

                    <h2 class="text-sm font-semibold text-slate-900">
                        Asset List
                    </h2>

                    <p class="mt-1 text-xs text-slate-400">
                        {{ $assets->total() }} asset terdaftar
                    </p>

                </div>

            </div>

        </div>


        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead>

                    <tr class="border-b border-slate-100 bg-slate-50/70">

                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Asset
                        </th>

                        <th class="whitespace-nowrap px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            UserID
                        </th>

                        <th class="whitespace-nowrap px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            MSISDN
                        </th>

                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Category
                        </th>

                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Location
                        </th>

                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Status
                        </th>

                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Condition
                        </th>

                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Rental End
                        </th>

                        <th class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse ($assets as $asset)

                        @php

                            $statusClass = match ($asset->status) {

                                'available' => 'bg-emerald-50 text-emerald-700',

                                'assigned' => 'bg-blue-50 text-blue-700',

                                'maintenance' => 'bg-amber-50 text-amber-700',

                                'lost' => 'bg-rose-50 text-rose-700',

                                default => 'bg-slate-100 text-slate-600',

                            };


                            $conditionClass = match ($asset->condition) {

                                'good' => 'bg-emerald-50 text-emerald-700',

                                'fair' => 'bg-amber-50 text-amber-700',

                                'damaged' => 'bg-rose-50 text-rose-700',

                                default => 'bg-slate-100 text-slate-600',

                            };


                            $days = $asset->rental_days_remaining;

                        @endphp


                        <tr
                            wire:key="asset-{{ $asset->id }}"
                            class="border-b border-slate-100 last:border-0 hover:bg-slate-50/70"
                        >

                            <!-- ASSET -->

                            <td class="px-5 py-4">

                                <p class="text-xs font-semibold text-slate-800">
                                    {{ $asset->asset_code }}
                                </p>

                                <p class="mt-0.5 text-[11px] text-slate-400">

                                    {{ trim(($asset->brand ?? '') . ' ' . ($asset->model ?? '')) ?: '-' }}

                                </p>

                            </td>

                            {{-- USER ID --}}
                            <td class="whitespace-nowrap px-5 py-4 text-xs text-slate-700">
                                {{ $asset->mekaar_user_id ?: '-' }}
                            </td>

                            {{-- MSISDN --}}
                            <td class="whitespace-nowrap px-5 py-4 text-xs font-medium text-slate-700">
                                {{ $asset->msisdn ?: '-' }}
                            </td>

                            <!-- CATEGORY -->

                            <td class="px-5 py-4 text-xs text-slate-600">

                                {{ $asset->category?->name ?? '-' }}

                            </td>


                            <!-- LOCATION -->

                            <td class="px-5 py-4 text-xs text-slate-600">

                                {{ $asset->location?->name ?? '-' }}

                            </td>


                            <!-- STATUS -->

                            <td class="px-5 py-4">

                                <span class="inline-flex rounded-full px-2 py-1 text-[10px] font-semibold {{ $statusClass }}">

                                    {{ ucfirst($asset->status) }}

                                </span>

                            </td>


                            <!-- CONDITION -->

                            <td class="px-5 py-4">

                                <span class="inline-flex rounded-full px-2 py-1 text-[10px] font-semibold {{ $conditionClass }}">

                                    {{ ucfirst($asset->condition) }}

                                </span>

                            </td>


                            <!-- RENTAL -->

                            <td class="px-5 py-4">

                                @if ($asset->rental_end_date)

                                    <p class="text-xs text-slate-700">
                                        {{ $asset->rental_end_date->format('d M Y') }}
                                    </p>


                                    @if ($days < 0)

                                        <span class="text-[10px] font-semibold text-rose-600">
                                            Expired
                                        </span>

                                    @elseif ($days <= 7)

                                        <span class="text-[10px] font-semibold text-rose-600">
                                            {{ $days }} hari
                                        </span>

                                    @elseif ($days <= 30)

                                        <span class="text-[10px] font-semibold text-amber-600">
                                            {{ $days }} hari
                                        </span>

                                    @else

                                        <span class="text-[10px] text-slate-400">
                                            {{ $days }} hari
                                        </span>

                                    @endif

                                @else

                                    <span class="text-xs text-slate-400">
                                        -
                                    </span>

                                @endif

                            </td>


                            <!-- ACTION -->

                            <td class="px-5 py-4">

                                <div class="flex justify-end gap-1">


                                    <!-- DETAIL -->

                                    <button
                                        type="button"
                                        wire:click="show({{ $asset->id }})"
                                        title="Detail"
                                        class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                                    >

                                        <svg
                                            width="15"
                                            height="15"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <circle cx="12" cy="12" r="9"/>
                                            <path d="M12 10v6"/>
                                            <path d="M12 7h.01"/>
                                        </svg>

                                    </button>


                                    <!-- EDIT -->

                                    @can('assets.update')
                                        <button
                                                type="button"
                                                wire:click="edit({{ $asset->id }})"
                                                title="Edit"
                                                class="rounded-lg p-2 text-slate-400 hover:bg-blue-50 hover:text-blue-600"
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
                                    @endcan


                                    <!-- ARCHIVE -->

                                    @can('assets.delete')
                                        <button
                                            type="button"
                                            wire:click="archive({{ $asset->id }})"
                                            wire:confirm="Archive asset {{ $asset->asset_code }}?"
                                            title="Archive"
                                            class="rounded-lg p-2 text-slate-400 hover:bg-rose-50 hover:text-rose-600"
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
                                                <path d="M5 6l1 14h12l1-14"/>
                                                <path d="M9 10h6"/>
                                                <path d="M10 14h4"/>
                                            </svg>

                                        </button>
                                    @endcan

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="9"
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
                                        <rect x="3" y="4" width="18" height="14" rx="2"/>
                                        <path d="M8 20h8"/>
                                    </svg>

                                </div>

                                <p class="mt-3 text-sm font-medium text-slate-600">
                                    Belum ada asset
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Tambahkan asset pertama Anda.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <!-- PAGINATION -->

        @if ($assets->hasPages())

            <div class="border-t border-slate-100 px-5 py-4">

                {{ $assets->links() }}

            </div>

        @endif

    </div>


    <!-- ===================================================== -->
    <!-- DETAIL MODAL -->
    <!-- ===================================================== -->

    @if ($showDetail && $detailAsset)

        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm">

            <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white shadow-2xl">

                <!-- HEADER -->

                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">
                            Asset Detail
                        </p>

                        <h2 class="mt-1 text-lg font-bold text-slate-900">
                            {{ $detailAsset->asset_code }}
                        </h2>

                    </div>


                    <button
                        type="button"
                        wire:click="closeDetail"
                        class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700"
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


                <!-- CONTENT -->

                <div class="p-6">

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">


                        <!-- Category -->

                        <div class="rounded-lg bg-slate-50 p-4">

                            <p class="text-[10px] font-semibold uppercase text-slate-400">
                                Category
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-800">
                                {{ $detailAsset->category?->name ?? '-' }}
                            </p>

                        </div>


                        <!-- Location -->

                        <div class="rounded-lg bg-slate-50 p-4">

                            <p class="text-[10px] font-semibold uppercase text-slate-400">
                                Location
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-800">
                                {{ $detailAsset->location?->name ?? '-' }}
                            </p>

                        </div>


                        <!-- Vendor -->

                        <div class="rounded-lg bg-slate-50 p-4">

                            <p class="text-[10px] font-semibold uppercase text-slate-400">
                                Vendor
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-800">
                                {{ $detailAsset->vendor?->name ?? '-' }}
                            </p>

                        </div>


                        <!-- Brand -->

                        <div class="rounded-lg bg-slate-50 p-4">

                            <p class="text-[10px] font-semibold uppercase text-slate-400">
                                Brand
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-800">
                                {{ $detailAsset->brand ?? '-' }}
                            </p>

                        </div>


                        <!-- Model -->

                        <div class="rounded-lg bg-slate-50 p-4">

                            <p class="text-[10px] font-semibold uppercase text-slate-400">
                                Model
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-800">
                                {{ $detailAsset->model ?? '-' }}
                            </p>

                        </div>


                        <!-- Serial -->

                        <div class="rounded-lg bg-slate-50 p-4">

                            <p class="text-[10px] font-semibold uppercase text-slate-400">
                                Serial Number
                            </p>

                            <p class="mt-1 break-all text-sm font-semibold text-slate-800">
                                {{ $detailAsset->serial_number ?? '-' }}
                            </p>

                        </div>


                        <!-- Status -->

                        <div class="rounded-lg bg-slate-50 p-4">

                            <p class="text-[10px] font-semibold uppercase text-slate-400">
                                Status
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-800">
                                {{ ucfirst($detailAsset->status) }}
                            </p>

                        </div>


                        <!-- Condition -->

                        <div class="rounded-lg bg-slate-50 p-4">

                            <p class="text-[10px] font-semibold uppercase text-slate-400">
                                Condition
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-800">
                                {{ ucfirst($detailAsset->condition) }}
                            </p>

                        </div>


                        <!-- Purchase -->

                        <div class="rounded-lg bg-slate-50 p-4">

                            <p class="text-[10px] font-semibold uppercase text-slate-400">
                                Purchase Date
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-800">
                                {{ $detailAsset->purchase_date?->format('d M Y') ?? '-' }}
                            </p>

                        </div>


                        <!-- Rental Start -->

                        <div class="rounded-lg bg-slate-50 p-4">

                            <p class="text-[10px] font-semibold uppercase text-slate-400">
                                Rental Start
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-800">
                                {{ $detailAsset->rental_start_date?->format('d M Y') ?? '-' }}
                            </p>

                        </div>


                        <!-- Rental End -->

                        <div class="rounded-lg bg-slate-50 p-4">

                            <p class="text-[10px] font-semibold uppercase text-slate-400">
                                Rental End
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-800">
                                {{ $detailAsset->rental_end_date?->format('d M Y') ?? '-' }}
                            </p>

                        </div>


                        <!-- Rental Cost -->

                        <div class="rounded-lg bg-slate-50 p-4">

                            <p class="text-[10px] font-semibold uppercase text-slate-400">
                                Rental Cost
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-800">

                                @if ($detailAsset->rental_cost)

                                    Rp {{ number_format($detailAsset->rental_cost, 0, ',', '.') }}

                                @else

                                    -

                                @endif

                            </p>

                        </div>

                    </div>


                    <!-- NOTES -->

                    <div class="mt-4 rounded-lg border border-slate-200 p-4">

                        <p class="text-[10px] font-semibold uppercase text-slate-400">
                            Notes
                        </p>

                        <p class="mt-2 whitespace-pre-line text-sm text-slate-600">
                            {{ $detailAsset->notes ?: '-' }}
                        </p>

                    </div>

                </div>


                <!-- FOOTER -->

                <div class="flex justify-end border-t border-slate-200 px-6 py-4">

                    <button
                        type="button"
                        wire:click="closeDetail"
                        class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50"
                    >
                        Close
                    </button>

                </div>

            </div>

        </div>

    @endif

</div>