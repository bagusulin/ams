<?php

namespace App\Livewire\AssetHistories;

use App\Models\AssetHistory;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $histories = AssetHistory::query()
            ->with([
                'asset',
                'user',
            ])
            ->when(
                $this->search !== '',
                function ($query) {

                    $search = '%' . $this->search . '%';

                    $query->where(function ($query) use ($search) {

                        $query
                            ->where(
                                'action',
                                'like',
                                $search
                            )
                            ->orWhere(
                                'description',
                                'like',
                                $search
                            )
                            ->orWhereHas(
                                'asset',
                                function ($query) use ($search) {
                                    $query->where(
                                        'asset_code',
                                        'like',
                                        $search
                                    );
                                }
                            )
                            ->orWhereHas(
                                'user',
                                function ($query) use ($search) {
                                    $query->where(
                                        'name',
                                        'like',
                                        $search
                                    );
                                }
                            );

                    });
                }
            )
            ->latest()
            ->paginate(15);

        return view(
            'livewire.asset-histories.index',
            [
                'histories' => $histories,
            ]
        );
    }
}