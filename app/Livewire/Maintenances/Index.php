<?php

namespace App\Livewire\Maintenances;

use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\AssetMaintenance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;
use App\Support\Assets\AssetAccess;
use Illuminate\Support\Facades\Gate;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    /*
    |--------------------------------------------------------------------------
    | Modal State
    |--------------------------------------------------------------------------
    */
    public bool $showForm = false;
    public bool $showDetail = false;

    /*
    |--------------------------------------------------------------------------
    | Form State
    |--------------------------------------------------------------------------
    */
    public ?int $editingId = null;
    public ?int $detailId = null;

    public string $asset_id = '';
    public string $maintenance_date = '';
    public string $type = '';
    public string $description = '';
    public string $action_taken = '';
    public string $cost = '0';
    public string $status = 'in_progress';

    /*
    |--------------------------------------------------------------------------
    | Filter
    |--------------------------------------------------------------------------
    */
    public string $search = '';
    public string $statusFilter = 'all';

    /*
    |--------------------------------------------------------------------------
    | Constants
    |--------------------------------------------------------------------------
    */
    private const STATUSES = [
        'pending',
        'in_progress',
        'completed',
        'cancelled',
    ];

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */
    protected function rules(): array
    {
        return [
            'asset_id' => [
                'required',
                'integer',
                'exists:assets,id',
            ],

            'maintenance_date' => [
                'required',
                'date',
            ],

            'type' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'action_taken' => [
                'nullable',
                'string',
            ],

            'cost' => [
                'required',
                'numeric',
                'min:0',
            ],

            'status' => [
                'required',
                'in:pending,in_progress,completed,cancelled',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Mount
    |--------------------------------------------------------------------------
    */
    public function mount(): void
    {
        $this->resetForm();
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */
    
    public function render()
    {
        $user = Auth::user();

        abort_unless($user, 403);

        Gate::authorize('maintenances.view');

        $maintenances = AssetMaintenance::query()
            ->with([
                'asset.category',
                'asset.location',
            ])
            ->whereHas('asset', function ($assetQuery) use ($user) {
                AssetAccess::assetsQuery($user, $assetQuery);
            })
            ->when($this->search !== '', function ($query) {
                $search = '%' . trim($this->search) . '%';

                $query->where(function ($q) use ($search) {
                    $q->where('type', 'like', $search)
                        ->orWhere('description', 'like', $search)
                        ->orWhereHas('asset', function ($assetQuery) use ($search) {
                            $assetQuery->where(function ($fields) use ($search) {
                                $fields
                                    ->where('asset_code', 'like', $search)
                                    ->orWhere('brand', 'like', $search)
                                    ->orWhere('model', 'like', $search)
                                    ->orWhere('serial_number', 'like', $search);
                            });
                        });
                });
            })
            ->when(
                $this->statusFilter !== 'all',
                function ($query) {
                    $query->where('status', $this->statusFilter);
                }
            )
            ->latest('maintenance_date')
            ->latest('id')
            ->paginate(10);

        $currentAssetId = $this->asset_id
            ? (int) $this->asset_id
            : null;

        $availableAssets = AssetAccess::assetsQuery(
            $user,
            Asset::query()->with('category')
        )
            ->where(function ($query) use ($currentAssetId) {
                $query->where('status', 'available');

                if ($currentAssetId) {
                    $query->orWhere('id', $currentAssetId);
                }
            })
            ->orderBy('asset_code')
            ->get();

        $detailMaintenance = null;

        if ($this->detailId) {
            $detailMaintenance = AssetMaintenance::query()
                ->with([
                    'asset.category',
                    'asset.location',
                    'asset.vendor',
                ])
                ->whereHas('asset', function ($assetQuery) use ($user) {
                    AssetAccess::assetsQuery($user, $assetQuery);
                })
                ->find($this->detailId);
        }

        return view(
            'livewire.maintenances.index',
            [
                'maintenances' => $maintenances,
                'availableAssets' => $availableAssets,
                'detailMaintenance' => $detailMaintenance,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Search / Filter Update
    |--------------------------------------------------------------------------
    */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Open Create Form
    |--------------------------------------------------------------------------
    */
    public function openCreate(): void
    {
        Gate::authorize('maintenances.create');
        $this->resetForm();
        $this->showForm = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Open Edit Form
    |--------------------------------------------------------------------------
    */
    public function openEdit(int $id): void
    {
        $user = Auth::user();

        abort_unless($user, 403);

        Gate::authorize('maintenances.update');

        $maintenance = AssetMaintenance::query()
            ->with('asset.category')
            ->whereHas('asset', function ($assetQuery) use ($user) {
                AssetAccess::assetsQuery($user, $assetQuery);
            })
            ->findOrFail($id);

        abort_unless(
            AssetAccess::canManageCategory(
                $user,
                $maintenance->asset?->category?->code
            ),
            403
        );

        $this->editingId = $maintenance->id;
        $this->asset_id = (string) $maintenance->asset_id;

        $this->maintenance_date =
            $maintenance->maintenance_date?->format('Y-m-d')
            ?? now()->format('Y-m-d');

        $this->type = $maintenance->type ?? '';
        $this->description = $maintenance->description ?? '';
        $this->action_taken = $maintenance->action_taken ?? '';
        $this->cost = (string) ($maintenance->cost ?? '0');
        $this->status = $maintenance->status ?? 'in_progress';

        $this->resetValidation();

        $this->showForm = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Open Detail
    |--------------------------------------------------------------------------
    */
    public function openDetail(int $id): void
    {
        $user = Auth::user();

        abort_unless($user, 403);

        Gate::authorize('maintenances.view');

        AssetMaintenance::query()
            ->whereHas('asset', function ($assetQuery) use ($user) {
                AssetAccess::assetsQuery($user, $assetQuery);
            })
            ->findOrFail($id);

        $this->detailId = $id;
        $this->showDetail = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Close Form
    |--------------------------------------------------------------------------
    */
    public function closeForm(): void
    {
        $this->showForm = false;

        $this->resetForm();
    }

    /*
    |--------------------------------------------------------------------------
    | Close Detail
    |--------------------------------------------------------------------------
    */
    public function closeDetail(): void
    {
        $this->showDetail = false;

        $this->detailId = null;
    }

    /*
    |--------------------------------------------------------------------------
    | Save
    |--------------------------------------------------------------------------
    */
    public function save(): void
    {
        $user = Auth::user();

        abort_unless($user, 403);

        Gate::authorize(
            $this->editingId
                ? 'maintenances.update'
                : 'maintenances.create'
        );

        $validated = $this->validate();

        try {
            if ($this->editingId) {
                $this->updateMaintenance(
                    $this->editingId,
                    $validated
                );
            } else {
                $this->createMaintenance($validated);
            }

            $this->closeForm();

        } catch (ValidationException $e) {
            throw $e;

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw $e;

        } catch (\Throwable $e) {
            report($e);

            session()->flash(
                'error',
                'Data maintenance gagal diproses. Silakan coba lagi.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Create Maintenance
    |--------------------------------------------------------------------------
    */
    private function createMaintenance(array $validated): void
    {
        $user = Auth::user();
        abort_unless($user, 403);
        DB::transaction(function () use ($validated, $user) {

            /*
             * Lock asset.
             */
            $asset = AssetAccess::assetsQuery(
                $user,
                Asset::query()->with('category')
            )
                ->lockForUpdate()
                ->findOrFail($validated['asset_id']);

            abort_unless(
                AssetAccess::canManageCategory(
                    $user,
                    $asset->category?->code
                ),
                403
            );

            /*
             * Asset harus available.
             */
            if ($asset->status !== 'available') {
                throw ValidationException::withMessages([
                    'asset_id' =>
                        'Asset harus berstatus available untuk dibuatkan maintenance baru.',
                ]);
            }

            /*
             * Pastikan belum ada maintenance aktif.
             */
            $activeMaintenanceExists = AssetMaintenance::query()
                ->where('asset_id', $asset->id)
                ->whereIn('status', [
                    'pending',
                    'in_progress',
                ])
                ->exists();

            if ($activeMaintenanceExists) {
                throw ValidationException::withMessages([
                    'asset_id' =>
                        'Asset tersebut masih memiliki maintenance aktif.',
                ]);
            }

            /*
             * Buat maintenance.
             */
            $maintenance = AssetMaintenance::create([
                'asset_id' => $asset->id,
                'maintenance_date' =>
                    $validated['maintenance_date'],
                'type' =>
                    $validated['type'],
                'description' =>
                    $validated['description'] ?: null,
                'action_taken' =>
                    $validated['action_taken'] ?: null,
                'cost' =>
                    $validated['cost'],
                'status' =>
                    $validated['status'],
            ]);

            /*
             * Jika maintenance aktif,
             * asset berubah menjadi maintenance.
             */
            $newAssetStatus = in_array(
                $validated['status'],
                ['pending', 'in_progress'],
                true
            )
                ? 'maintenance'
                : 'available';

            $oldValues = [
                'status' => $asset->status,
            ];

            $asset->update([
                'status' => $newAssetStatus,
            ]);

            /*
             * History.
             */
            AssetHistory::create([
                'asset_id' => $asset->id,
                'user_id' => Auth::id(),
                'action' => 'maintenance_created',
                'description' =>
                    'Maintenance asset dibuat.',
                'old_values' => $oldValues,
                'new_values' => [
                    'maintenance_id' =>
                        $maintenance->id,

                    'maintenance_status' =>
                        $maintenance->status,

                    'asset_status' =>
                        $newAssetStatus,

                    'type' =>
                        $maintenance->type,
                ],
            ]);
        });

        session()->flash(
            'success',
            'Maintenance berhasil dibuat.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Maintenance
    |--------------------------------------------------------------------------
    */
    private function updateMaintenance(
        int $maintenanceId,
        array $validated
    ): void {
        $user = Auth::user();
        abort_unless($user, 403);
        DB::transaction(function () use (
            $maintenanceId,
            $validated,
            $user
        ) {

            /*
             * Lock maintenance.
             */
            $maintenance = AssetMaintenance::query()
                ->whereHas('asset', function ($assetQuery) use ($user) {
                    AssetAccess::assetsQuery($user, $assetQuery);
                })
                ->lockForUpdate()
                ->findOrFail($maintenanceId);
            /*
             * Asset tidak boleh diganti ketika edit.
             */
            if (
                (int) $validated['asset_id']
                !== (int) $maintenance->asset_id
            ) {
                throw ValidationException::withMessages([
                    'asset_id' =>
                        'Asset pada maintenance tidak dapat diganti. Buat maintenance baru untuk asset lain.',
                ]);
            }

            /*
             * Lock asset.
             */
            $asset = AssetAccess::assetsQuery(
                $user,
                Asset::query()->with('category')
            )
                ->lockForUpdate()
                ->findOrFail($maintenance->asset_id);

            abort_unless(
                AssetAccess::canManageCategory(
                    $user,
                    $asset->category?->code
                ),
                403
            );

            $oldMaintenanceStatus = $maintenance->status;
            $newMaintenanceStatus = $validated['status'];

            /*
             * Pastikan tidak ada maintenance aktif lain.
             */
            if (
                in_array(
                    $newMaintenanceStatus,
                    ['pending', 'in_progress'],
                    true
                )
            ) {
                $activeOtherMaintenance = AssetMaintenance::query()
                    ->where('asset_id', $asset->id)
                    ->whereIn('status', [
                        'pending',
                        'in_progress',
                    ])
                    ->where('id', '!=', $maintenance->id)
                    ->exists();

                if ($activeOtherMaintenance) {
                    throw ValidationException::withMessages([
                        'status' =>
                            'Asset tersebut sudah memiliki maintenance aktif lainnya.',
                    ]);
                }
            }

            /*
             * -----------------------------------------------------------------
             * Validasi transisi STATUS
             * -----------------------------------------------------------------
             */

            $oldIsActive = in_array(
                $oldMaintenanceStatus,
                ['pending', 'in_progress'],
                true
            );

            $newIsActive = in_array(
                $newMaintenanceStatus,
                ['pending', 'in_progress'],
                true
            );

            /*
             * Maintenance aktif harus memiliki asset
             * dengan status maintenance.
             */
            if (
                $oldIsActive
                && $newIsActive
                && $asset->status !== 'maintenance'
            ) {
                throw ValidationException::withMessages([
                    'status' =>
                        'Status asset tidak konsisten dengan maintenance aktif.',
                ]);
            }

            /*
             * Dari active -> completed/cancelled
             *
             * Asset harus masih maintenance.
             */
            if (
                $oldIsActive
                && !$newIsActive
                && $asset->status !== 'maintenance'
            ) {
                throw ValidationException::withMessages([
                    'status' =>
                        'Maintenance tidak dapat diselesaikan karena status asset sudah berubah.',
                ]);
            }

            /*
             * Dari completed/cancelled -> active
             *
             * Asset harus available.
             */
            if (
                !$oldIsActive
                && $newIsActive
                && $asset->status !== 'available'
            ) {
                throw ValidationException::withMessages([
                    'status' =>
                        'Maintenance tidak dapat diaktifkan kembali karena asset tidak berstatus available.',
                ]);
            }

            /*
             * Nilai lama untuk history.
             */
            $oldValues = [
                'asset_id' =>
                    $maintenance->asset_id,

                'maintenance_date' =>
                    $maintenance->maintenance_date?->format('Y-m-d'),

                'type' =>
                    $maintenance->type,

                'description' =>
                    $maintenance->description,

                'action_taken' =>
                    $maintenance->action_taken,

                'cost' =>
                    $maintenance->cost,

                'status' =>
                    $maintenance->status,

                'asset_status' =>
                    $asset->status,
            ];

            /*
             * Update maintenance.
             */
            $maintenance->update([
                /*
                 * asset_id sengaja TIDAK diupdate.
                 */
                'maintenance_date' =>
                    $validated['maintenance_date'],

                'type' =>
                    $validated['type'],

                'description' =>
                    $validated['description'] ?: null,

                'action_taken' =>
                    $validated['action_taken'] ?: null,

                'cost' =>
                    $validated['cost'],

                'status' =>
                    $validated['status'],
            ]);

            /*
             * Update status asset hanya ketika
             * status maintenance memang berubah
             * antara active dan terminal.
             */
            if (
                !$oldIsActive
                && $newIsActive
            ) {
                $asset->update([
                    'status' => 'maintenance',
                ]);
            }

            if (
                $oldIsActive
                && !$newIsActive
            ) {
                $asset->update([
                    'status' => 'available',
                ]);
            }

            /*
             * Ambil asset terbaru.
             */
            $freshAsset = $asset->fresh();

            /*
             * History.
             */
            AssetHistory::create([
                'asset_id' =>
                    $asset->id,

                'user_id' =>
                    Auth::id(),

                'action' =>
                    'maintenance_updated',

                'description' =>
                    'Data maintenance diperbarui.',

                'old_values' =>
                    $oldValues,

                'new_values' => [
                    'asset_id' =>
                        $maintenance->asset_id,

                    'maintenance_id' =>
                        $maintenance->id,

                    'maintenance_date' =>
                        $maintenance->maintenance_date?->format('Y-m-d'),

                    'type' =>
                        $maintenance->type,

                    'description' =>
                        $maintenance->description,

                    'action_taken' =>
                        $maintenance->action_taken,

                    'cost' =>
                        $maintenance->cost,

                    'status' =>
                        $maintenance->status,

                    'asset_status' =>
                        $freshAsset->status,
                ],
            ]);
        });

        session()->flash(
            'success',
            'Maintenance berhasil diperbarui.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Maintenance
    |--------------------------------------------------------------------------
    */
    public function deleteMaintenance(int $id): void
    {
        $user = Auth::user();

        abort_unless($user, 403);

        Gate::authorize('maintenances.delete');

        try {
            DB::transaction(function () use ($id, $user) {
                $maintenance = AssetMaintenance::query()
                    ->whereHas('asset', function ($assetQuery) use ($user) {
                        AssetAccess::assetsQuery($user, $assetQuery);
                    })
                    ->lockForUpdate()
                    ->findOrFail($id);

                if (in_array(
                    $maintenance->status,
                    ['pending', 'in_progress'],
                    true
                )) {
                    throw ValidationException::withMessages([
                        'delete' =>
                            'Maintenance aktif tidak dapat dihapus. Selesaikan atau batalkan maintenance terlebih dahulu.',
                    ]);
                }

                $asset = AssetAccess::assetsQuery(
                    $user,
                    Asset::query()->with('category')
                )
                    ->lockForUpdate()
                    ->findOrFail($maintenance->asset_id);

                abort_unless(
                    AssetAccess::canManageCategory(
                        $user,
                        $asset->category?->code
                    ),
                    403
                );

                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'user_id' => Auth::id(),
                    'action' => 'maintenance_deleted',
                    'description' => 'Data maintenance dihapus.',
                    'old_values' => [
                        'maintenance_id' => $maintenance->id,
                        'type' => $maintenance->type,
                        'status' => $maintenance->status,
                        'cost' => $maintenance->cost,
                    ],
                    'new_values' => null,
                ]);

                $maintenance->delete();
            });

            session()->flash(
                'success',
                'Data maintenance berhasil dihapus.'
            );

        } catch (ValidationException $e) {
            throw $e;

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            throw $e;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw $e;

        } catch (\Throwable $e) {
            report($e);

            session()->flash(
                'error',
                'Data maintenance gagal dihapus.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Reset Form
    |--------------------------------------------------------------------------
    */
    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'detailId',
            'asset_id',
            'type',
            'description',
            'action_taken',
        ]);

        $this->maintenance_date =
            now()->format('Y-m-d');

        $this->cost = '0';

        $this->status = 'in_progress';

        $this->resetValidation();
    }
}