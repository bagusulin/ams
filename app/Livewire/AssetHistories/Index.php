<?php

namespace App\Livewire\AssetHistories;

use App\Models\AssetHistory;
use Livewire\Component;
use Livewire\WithPagination;
use App\Support\Assets\AssetAccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
        $user = Auth::user();

        abort_unless($user, 403);

        Gate::authorize('asset-history.view');

        $histories = AssetHistory::query()
            ->with([
                'asset.category',
                'user',
            ])
            ->whereHas('asset', function ($assetQuery) use ($user) {
                AssetAccess::assetsQuery($user, $assetQuery);
            })
            ->when(
                $this->search !== '',
                function ($query) {
                    $search = '%' . trim($this->search) . '%';

                    $query->where(function ($q) use ($search) {
                        $q->where('action', 'like', $search)
                            ->orWhere('description', 'like', $search)
                            ->orWhereHas('asset', function ($assetQuery) use ($search) {
                                $assetQuery->where(function ($fields) use ($search) {
                                    $fields
                                        ->where('asset_code', 'like', $search)
                                        ->orWhere('brand', 'like', $search)
                                        ->orWhere('model', 'like', $search)
                                        ->orWhere('serial_number', 'like', $search);
                                });
                            })
                            ->orWhereHas('user', function ($userQuery) use ($search) {
                                $userQuery->where('name', 'like', $search);
                            });
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