<div>

    <!-- HEADER -->

    <div class="mb-6">

        <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">
            Transactions
        </p>

        <h1 class="mt-1 text-2xl font-bold text-slate-900">
            Return Asset
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Kelola asset yang telah dikembalikan oleh user.
        </p>

    </div>


    @if (session()->has('success'))

        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3">

            <p class="text-sm text-emerald-700">
                {{ session('success') }}
            </p>

        </div>

    @endif


    <!-- RETURN FORM -->
    @can('returns.create')
    @if ($showForm)

        <div class="mb-5 rounded-xl border border-blue-100 bg-white p-5 shadow-sm">

            <h2 class="text-sm font-semibold text-slate-900">
                Return Asset
            </h2>

            <p class="mt-1 text-xs text-slate-400">
                Masukkan tanggal pengembalian asset.
            </p>


            <form
                wire:submit="returnAsset({{ $detailId }})"
                class="mt-5"
            >

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Return Date
                        </label>

                        <input
                            type="date"
                            wire:model="return_date"
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm"
                        >

                        @error('return_date')

                            <p class="mt-1 text-xs text-red-500">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    <div>

                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                            Notes
                        </label>

                        <input
                            type="text"
                            wire:model="notes"
                            placeholder="Kondisi saat dikembalikan..."
                            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm"
                        >

                    </div>

                </div>


                <div class="mt-5 flex justify-end gap-2">

                    <button
                        type="button"
                        wire:click="closeForm"
                        class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm"
                    >
                        Cancel
                    </button>


                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white"
                    >
                        Confirm Return
                    </button>

                </div>

            </form>

        </div>

    @endif
    @endcan


    <!-- ACTIVE ASSET -->

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

        <div class="flex flex-col justify-between gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center">

            <div>

                <h2 class="text-sm font-semibold text-slate-900">
                    Asset Currently Assigned
                </h2>

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

                        <th class="px-5 py-3 text-right text-[10px] font-bold uppercase text-slate-400">
                            Action
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


                            <td class="px-5 py-4 text-xs text-slate-700">
                                {{ $assignment->user?->name }}
                            </td>


                            <td class="px-5 py-4 text-xs text-slate-600">
                                {{ $assignment->location?->name ?? $assignment->asset?->location?->name ?? '-' }}
                            </td>


                            <td class="px-5 py-4 text-xs text-slate-600">
                                {{ $assignment->assigned_at?->format('d M Y') }}
                            </td>


                            <td class="px-5 py-4 text-right">
                                @can('returns.create')
                                <button
                                    type="button"
                                    wire:click="openForm({{ $assignment->id }})"
                                    class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100"
                                >
                                    Return
                                </button>
                                @endcan

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="px-5 py-12 text-center"
                            >

                                <p class="text-sm text-slate-500">
                                    Tidak ada asset yang sedang digunakan.
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