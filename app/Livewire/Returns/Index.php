<?php

namespace App\Livewire\Returns;

use App\Support\Assets\AssetAccess;
use Illuminate\Support\Facades\Gate;
use App\Models\AssetAssignment;
use App\Models\AssetHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $search = '';

    /*
    |--------------------------------------------------------------------------
    | Modal
    |--------------------------------------------------------------------------
    */
    public bool $showForm = false;
    public bool $showDetail = false;

    /*
    |--------------------------------------------------------------------------
    | Selected Assignment
    |--------------------------------------------------------------------------
    */
    public ?int $detailId = null;

    /*
    |--------------------------------------------------------------------------
    | Form Return
    |--------------------------------------------------------------------------
    */
    public string $return_date = '';
    public string $notes = '';

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */
    protected function rules(): array
    {
        return [
            'return_date' => [
                'required',
                'date',
            ],

            'notes' => [
                'nullable',
                'string',
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
        $this->return_date = now()->format('Y-m-d');
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

        Gate::authorize('returns.view');

        $search = trim($this->search);

        $assignments = AssetAssignment::query()
            ->with([
                'asset.category',
                'asset.location',
                'user',
                'location',
            ])
            ->whereNull('returned_at')
            ->whereHas('asset', function ($assetQuery) use ($user) {
                AssetAccess::assetsQuery($user, $assetQuery);
            })
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . $search . '%';

                $query->where(function ($q) use ($like) {
                    $q->whereHas('asset', function ($assetQuery) use ($like) {
                        $assetQuery->where(function ($fields) use ($like) {
                            $fields
                                ->where('asset_code', 'like', $like)
                                ->orWhere('brand', 'like', $like)
                                ->orWhere('model', 'like', $like)
                                ->orWhere('serial_number', 'like', $like);
                        });
                    })->orWhereHas('user', function ($userQuery) use ($like) {
                        $userQuery
                            ->where('name', 'like', $like)
                            ->orWhere('email', 'like', $like);
                    });
                });
            })
            ->latest('assigned_at')
            ->latest('id')
            ->paginate(10);

        return view(
            'livewire.returns.index',
            [
                'assignments' => $assignments,
            ]
        );
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }


    /*
    |--------------------------------------------------------------------------
    | Buka Form Return
    |--------------------------------------------------------------------------
    */
    public function openForm(int $id): void
    {
        $user = Auth::user();

        abort_unless($user, 403);

        Gate::authorize('returns.create');

        $assignment = AssetAssignment::query()
            ->with('asset.category')
            ->whereNull('returned_at')
            ->whereHas('asset', function ($assetQuery) use ($user) {
                AssetAccess::assetsQuery($user, $assetQuery);
            })
            ->findOrFail($id);

        $this->detailId = $assignment->id;

        $this->return_date = now()->format('Y-m-d');
        $this->notes = '';

        $this->resetValidation();

        $this->showForm = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Tutup Form Return
    |--------------------------------------------------------------------------
    */
    public function closeForm(): void
    {
        $this->showForm = false;

        $this->detailId = null;

        $this->return_date = now()->format('Y-m-d');
        $this->notes = '';

        $this->resetValidation();
    }

    /*
    |--------------------------------------------------------------------------
    | Buka Detail Assignment
    |--------------------------------------------------------------------------
    */
    public function openDetail(int $id): void
    {
        $user = Auth::user();

        abort_unless($user, 403);

        Gate::authorize('returns.view');

        $assignment = AssetAssignment::query()
            ->with([
                'asset.category',
                'asset.location',
                'user',
                'location',
            ])
            ->whereHas('asset', function ($assetQuery) use ($user) {
                AssetAccess::assetsQuery($user, $assetQuery);
            })
            ->findOrFail($id);

        $this->detailId = $assignment->id;
        $this->showDetail = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Tutup Detail
    |--------------------------------------------------------------------------
    */
    public function closeDetail(): void
    {
        $this->showDetail = false;

        $this->detailId = null;

        $this->resetValidation();
    }

    /*
    |--------------------------------------------------------------------------
    | Proses Return Asset
    |--------------------------------------------------------------------------
    */
    
    public function returnAsset(int $assignmentId): void
    {
        $user = Auth::user();

        abort_unless($user, 403);

        Gate::authorize('returns.create');

        $validated = $this->validate();

        try {
            DB::transaction(function () use ($assignmentId, $validated, $user) {
                // Ambil dan kunci assignment yang asetnya boleh diakses.
                $assignment = AssetAssignment::query()
                    ->with('asset.category')
                    ->whereHas('asset', function ($assetQuery) use ($user) {
                        AssetAccess::assetsQuery($user, $assetQuery);
                    })
                    ->lockForUpdate()
                    ->findOrFail($assignmentId);

                if ($assignment->returned_at !== null) {
                    throw ValidationException::withMessages([
                        'return_date' => 'Assignment ini sudah dikembalikan.',
                    ]);
                }

                $asset = $assignment->asset()
                    ->with('category')
                    ->lockForUpdate()
                    ->firstOrFail();

                if (! AssetAccess::canManageCategory(
                    $user,
                    $asset->category?->code
                )) {
                    abort(403);
                }

                $oldValues = [
                    'status' => $asset->status,
                    'location_id' => $asset->location_id,
                    'assignment_id' => $assignment->id,
                    'user_id' => $assignment->user_id,
                ];

                $assignment->update([
                    'returned_at' => $validated['return_date'],
                    'notes' => $validated['notes'] ?: $assignment->notes,
                ]);

                // Aset kembali tersedia setelah proses return.
                $asset->update([
                    'status' => 'available',
                ]);

                $freshAsset = $asset->fresh();

                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'user_id' => Auth::id(),
                    'action' => 'returned',
                    'description' => 'Asset dikembalikan dari assignment.',
                    'old_values' => $oldValues,
                    'new_values' => [
                        'status' => $freshAsset->status,
                        'returned_at' => $assignment->returned_at?->format('Y-m-d'),
                        'assignment_id' => $assignment->id,
                        'user_id' => $assignment->user_id,
                    ],
                ]);
            });

            $this->closeForm();
            $this->resetPage();

            session()->flash(
                'success',
                'Asset berhasil dikembalikan.'
            );

        } catch (ValidationException $e) {
            throw $e;

        } catch (\Throwable $e) {
            report($e);

            session()->flash(
                'error',
                'Pengembalian asset gagal diproses. Silakan coba lagi.'
            );
        }
    }

}