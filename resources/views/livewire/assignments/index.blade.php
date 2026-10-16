<div>

    <!-- HEADER -->

    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">

        <div>

            <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">
                Transactions
            </p>

            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">
                Assignment
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Kelola pemberian asset kepada user.
            </p>

        </div>


        @can('assignments.create')
        <button
            type="button"
            wire:click="openCreate"
            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
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

            New Assignment

        </button>
        @endcan

    </div>


    <!-- MESSAGE -->

    @if (session()->has('success'))

        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3">

            <p class="text-sm text-emerald-700">
                {{ session('success') }}
            </p>

        </div>

    @endif


    <!-- FORM -->
    @can('assignments.create')
    @if ($showForm)

        <div class="mb-5 rounded-xl border border-blue-100 bg-white p-5 shadow-sm">

            <div class="mb-5 border-b border-slate-100 pb-4">

                <h2 class="text-sm font-semibold text-slate-900">
                    New Assignment
                </h2>

                <p class="mt-1 text-xs text-slate-400">
                    Pilih asset dan user yang menerima asset.
                </p>

            </div>


            <form wire:submit="save">

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">


                    <!-- ASSET -->

                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Asset *
                        </label>

                        <select
                            wire:model="asset_id"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm"
                        >

                            <option value="">
                                Pilih Asset
                            </option>

                            @foreach ($availableAssets as $asset)

                                <option value="{{ $asset->id }}">

                                    {{ $asset->asset_code }}
                                    —
                                    {{ $asset->category?->name }}

                                </option>

                            @endforeach

                        </select>

                        @error('asset_id')

                            <p class="mt-1 text-xs text-red-500">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    <!-- USER -->

                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                            User *
                        </label>

                        <select
                            wire:model="user_id"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm"
                        >

                            <option value="">
                                Pilih User
                            </option>

                            @foreach ($users as $user)

                                <option value="{{ $user->id }}">
                                    {{ $user->name }}
                                    —
                                    {{ $user->email }}
                                </option>

                            @endforeach

                        </select>

                        @error('user_id')

                            <p class="mt-1 text-xs text-red-500">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    <!-- LOCATION -->

                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Location
                        </label>

                        <select
                            wire:model="location_id"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm"
                        >

                            <option value="">
                                Gunakan lokasi asset
                            </option>

                            @foreach ($locations as $location)

                                <option value="{{ $location->id }}">
                                    {{ $location->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <!-- DATE -->

                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Assigned Date *
                        </label>

                        <input
                            type="date"
                            wire:model="assigned_at"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm"
                        >

                        @error('assigned_at')

                            <p class="mt-1 text-xs text-red-500">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    <!-- NOTES -->

                    <div class="md:col-span-2">

                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Notes
                        </label>

                        <textarea
                            wire:model="notes"
                            rows="3"
                            placeholder="Keterangan assignment..."
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm"
                        ></textarea>

                    </div>

                </div>


                <div class="mt-6 flex justify-end gap-2 border-t border-slate-100 pt-5">

                    <button
                        type="button"
                        wire:click="cancel"
                        class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600"
                    >
                        Cancel
                    </button>


                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white"
                    >
                        Save Assignment
                    </button>

                </div>

            </form>

        </div>

    @endif
    @endcan


    <!-- ACTIVE ASSIGNMENT -->

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

        <div class="flex flex-col justify-between gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center">

            <div>

                <h2 class="text-sm font-semibold text-slate-900">
                    Active Assignment
                </h2>

                <p class="mt-1 text-xs text-slate-400">
                    Asset yang saat ini sedang digunakan.
                </p>

            </div>


            <input
                type="text"
                wire:model.live.debounce.400ms="search"
                placeholder="Search asset / user..."
                class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs"
            >

        </div>


        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead>

                    <tr class="border-b border-slate-100 bg-slate-50">

                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase text-slate-400">
                            Asset
                        </th>

                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase text-slate-400">
                            User
                        </th>

                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase text-slate-400">
                            Location
                        </th>

                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase text-slate-400">
                            Assigned
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse ($assignments as $assignment)

                        <tr class="border-b border-slate-100 last:border-0">

                            <td class="px-5 py-4">

                                <p class="text-xs font-semibold text-slate-800">
                                    {{ $assignment->asset?->asset_code }}
                                </p>

                                <p class="text-[11px] text-slate-400">
                                    {{ $assignment->asset?->brand }}
                                    {{ $assignment->asset?->model }}
                                </p>

                            </td>


                            <td class="px-5 py-4">

                                <p class="text-xs font-semibold text-slate-700">
                                    {{ $assignment->user?->name }}
                                </p>

                                <p class="text-[11px] text-slate-400">
                                    {{ $assignment->user?->email }}
                                </p>

                            </td>


                            <td class="px-5 py-4 text-xs text-slate-600">
                                {{ $assignment->location?->name ?? $assignment->asset?->location?->name ?? '-' }}
                            </td>


                            <td class="px-5 py-4 text-xs text-slate-600">
                                {{ $assignment->assigned_at?->format('d M Y') }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="4"
                                class="px-5 py-12 text-center"
                            >

                                <p class="text-sm text-slate-500">
                                    Belum ada assignment aktif.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if ($assignments->hasPages())

            <div class="border-t border-slate-100 px-5 py-4">

                {{ $assignments->links() }}

            </div>

        @endif

    </div>

</div>