<?php

namespace App\Livewire\Assignments;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetHistory;
use App\Models\Location;
use App\Models\User;
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

    public bool $showForm = false;
    public bool $showDetail = false;

    public string $search = '';

    public ?int $editingId = null;
    public ?int $detailId = null;

    public string $asset_id = '';
    public string $user_id = '';
    public string $location_id = '';
    public string $assigned_at = '';
    public string $notes = '';

    protected function rules(): array
    {
        return [
            'asset_id' => ['required', 'integer', 'exists:assets,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'assigned_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function mount(): void
    {
        $this->assigned_at = now()->format('Y-m-d');
    }
    
    public function render()
    {
        $user = Auth::user();

        abort_unless($user, 403);

        Gate::authorize('assignments.view');

        $search = trim($this->search);

        $assignments = AssetAssignment::query()
            ->with([
                'asset.category',
                'asset.location',
                'user',
                'location',
            ])
            ->whereHas('asset', function ($assetQuery) use ($user) {
                AssetAccess::assetsQuery($user, $assetQuery);
            })
            ->whereNull('returned_at')
            ->when($search !== '', function ($query) use ($search, $user) {
                $like = '%' . $search . '%';

                $query->where(function ($q) use ($like, $user) {
                    $q->whereHas('asset', function ($assetQuery) use ($like, $user) {
                        AssetAccess::assetsQuery($user, $assetQuery);

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

        $availableAssets = AssetAccess::assetsQuery(
            $user,
            Asset::query()->with('category')
        )
            ->where('status', 'available')
            ->orderBy('asset_code')
            ->get();

        $users = User::query()
            ->orderBy('name')
            ->get();

        $locations = Location::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.assignments.index', compact(
            'assignments',
            'availableAssets',
            'users',
            'locations',
        ));
    }


    public function openCreate(): void
    {
        Gate::authorize('assignments.create');

        $this->resetForm();

        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetValidation();
    }

    public function openDetail(int $id): void
    {
        $user = Auth::user();

        abort_unless($user, 403);

        Gate::authorize('assignments.view');

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

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->detailId = null;
    }

    public function save(): void
    {
        $user = Auth::user();

        abort_unless($user, 403);
        Gate::authorize('assignments.create');
        $validated = $this->validate();

        try {
            DB::transaction(function () use ($validated, $user) {

                /*
                 * Lock asset supaya tidak ada dua proses assignment
                 * terhadap asset yang sama secara bersamaan.
                 */
                $asset = AssetAccess::assetsQuery($user,
                    Asset::query()->with('category')
                )
                    ->lockForUpdate()
                    ->findOrFail($validated['asset_id']);

                /*
                 * Asset wajib berstatus available.
                 */
                if ($asset->status !== 'available') {
                    throw ValidationException::withMessages([
                        'asset_id' => 'Asset tersebut tidak tersedia untuk assignment.',
                    ]);
                }

                /*
                 * Double check active assignment.
                 */
                $hasActiveAssignment = AssetAssignment::query()
                    ->where('asset_id', $asset->id)
                    ->whereNull('returned_at')
                    ->exists();

                if ($hasActiveAssignment) {
                    throw ValidationException::withMessages([
                        'asset_id' => 'Asset tersebut masih memiliki assignment aktif.',
                    ]);
                }

                /*
                 * Buat assignment.
                 */
                $assignment = AssetAssignment::create([
                    'asset_id' => $asset->id,
                    'user_id' => $validated['user_id'],
                    'location_id' => $validated['location_id'] ?: null,
                    'assigned_at' => $validated['assigned_at'],
                    'notes' => $validated['notes'] ?: null,
                ]);

                /*
                 * Update status asset.
                 */
                $oldValues = [
                    'status' => $asset->status,
                    'location_id' => $asset->location_id,
                ];

                $asset->update([
                    'status' => 'assigned',
                    'location_id' => $validated['location_id'] ?: $asset->location_id,
                ]);

                /*
                 * Simpan history.
                 */
                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'user_id' => Auth::id(),
                    'action' => 'assigned',
                    'description' => 'Asset di-assign kepada user.',
                    'old_values' => $oldValues,
                    'new_values' => [
                        'status' => $asset->fresh()->status,
                        'location_id' => $asset->fresh()->location_id,
                        'assignment_id' => $assignment->id,
                        'assigned_to_user_id' => $assignment->user_id,
                        'assigned_at' => $assignment->assigned_at?->format('Y-m-d'),
                    ],
                ]);
            });

            $this->closeForm();

            session()->flash(
                'success',
                'Asset berhasil di-assign.'
            );

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);

            session()->flash(
                'error',
                'Assignment gagal diproses. Silakan coba lagi.'
            );
        }
    }

    public function resetForm(): void
    {
        $this->reset([
            'editingId',
            'asset_id',
            'user_id',
            'location_id',
            'notes',
        ]);

        $this->assigned_at = now()->format('Y-m-d');

        $this->resetValidation();
    }
}