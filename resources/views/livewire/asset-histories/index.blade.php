<div>

    <div class="mb-6">

        <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">
            Monitoring
        </p>

        <h1 class="mt-1 text-2xl font-bold text-slate-900">
            Asset History
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Riwayat perubahan dan transaksi asset.
        </p>

    </div>


    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-5 py-4">

            <input
                type="text"
                wire:model.live.debounce.400ms="search"
                placeholder="Search asset, user, action..."
                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs sm:w-80"
            >

        </div>


        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead>

                    <tr class="border-b border-slate-100 bg-slate-50">

                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase text-slate-400">
                            Date
                        </th>

                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase text-slate-400">
                            Asset
                        </th>

                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase text-slate-400">
                            User
                        </th>

                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase text-slate-400">
                            Action
                        </th>

                        <th class="px-5 py-3 text-left text-[10px] font-bold uppercase text-slate-400">
                            Description
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse ($histories as $history)

                        <tr class="border-b border-slate-100 last:border-0">

                            <td class="px-5 py-4 text-xs text-slate-500">
                                {{ $history->created_at?->format('d M Y H:i') }}
                            </td>


                            <td class="px-5 py-4">

                                <p class="text-xs font-semibold text-slate-800">
                                    {{ $history->asset?->asset_code ?? '-' }}
                                </p>

                            </td>


                            <td class="px-5 py-4 text-xs text-slate-600">
                                {{ $history->user?->name ?? '-' }}
                            </td>


                            <td class="px-5 py-4">

                                @php

                                    $actionClass = match ($history->action) {

                                        'assigned'
                                            => 'bg-blue-50 text-blue-700',

                                        'returned'
                                            => 'bg-emerald-50 text-emerald-700',

                                        'created'
                                            => 'bg-purple-50 text-purple-700',

                                        'updated'
                                            => 'bg-amber-50 text-amber-700',

                                        default
                                            => 'bg-slate-100 text-slate-600',

                                    };

                                @endphp


                                <span class="inline-flex rounded-full px-2 py-1 text-[10px] font-semibold {{ $actionClass }}">

                                    {{ ucfirst($history->action) }}

                                </span>

                            </td>


                            <td class="max-w-md px-5 py-4">

                                <p class="truncate text-xs text-slate-500">
                                    {{ $history->description ?: '-' }}
                                </p>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="px-5 py-12 text-center"
                            >

                                <p class="text-sm text-slate-500">
                                    Belum ada history.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if ($histories->hasPages())

            <div class="border-t border-slate-100 px-5 py-4">

                {{ $histories->links() }}

            </div>

        @endif

    </div>

</div>