<?php

namespace App\Livewire\Returns;

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
        $assignments = AssetAssignment::with([
            'asset.category',
            'asset.location',
            'user',
            'location',
        ])
            ->whereNull('returned_at')
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

    /*
    |--------------------------------------------------------------------------
    | Buka Form Return
    |--------------------------------------------------------------------------
    */
    public function openForm(int $id): void
    {
        $assignment = AssetAssignment::query()
            ->whereNull('returned_at')
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
        AssetAssignment::with([
            'asset.category',
            'asset.location',
            'user',
            'location',
        ])
            ->whereNull('returned_at')
            ->findOrFail($id);

        $this->detailId = $id;

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
        /*
        |--------------------------------------------------------------------------
        | Validasi Form
        |--------------------------------------------------------------------------
        */
        $validated = $this->validate();

        try {

            /*
            |--------------------------------------------------------------------------
            | Transaction
            |--------------------------------------------------------------------------
            |
            | Semua proses return harus berhasil seluruhnya.
            | Jika salah satu gagal, semuanya di-rollback.
            |
            */
            DB::transaction(function () use ($assignmentId, $validated) {

                /*
                |--------------------------------------------------------------------------
                | Lock Assignment
                |--------------------------------------------------------------------------
                */
                $assignment = AssetAssignment::query()
                    ->lockForUpdate()
                    ->findOrFail($assignmentId);

                /*
                |--------------------------------------------------------------------------
                | Pastikan belum pernah dikembalikan
                |--------------------------------------------------------------------------
                */
                if ($assignment->returned_at !== null) {
                    throw ValidationException::withMessages([
                        'return_date' => 'Assignment ini sudah dikembalikan.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Lock Asset
                |--------------------------------------------------------------------------
                |
                | Mencegah asset diproses bersamaan dari dua request.
                |
                */
                $asset = $assignment->asset()
                    ->lockForUpdate()
                    ->firstOrFail();

                /*
                |--------------------------------------------------------------------------
                | Simpan nilai lama untuk History
                |--------------------------------------------------------------------------
                */
                $oldValues = [
                    'status' => $asset->status,
                    'location_id' => $asset->location_id,
                    'assignment_id' => $assignment->id,
                    'user_id' => $assignment->user_id,
                ];

                /*
                |--------------------------------------------------------------------------
                | Update Assignment
                |--------------------------------------------------------------------------
                */
                $assignment->update([
                    'returned_at' => $validated['return_date'],
                    'notes' => $validated['notes']
                        ?: $assignment->notes,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Update Status Asset
                |--------------------------------------------------------------------------
                |
                | Setelah dikembalikan, asset menjadi available.
                |
                */
                $asset->update([
                    'status' => 'available',
                ]);

                /*
                |--------------------------------------------------------------------------
                | Ambil data asset terbaru
                |--------------------------------------------------------------------------
                */
                $freshAsset = $asset->fresh();

                /*
                |--------------------------------------------------------------------------
                | Simpan History
                |--------------------------------------------------------------------------
                */
                AssetHistory::create([
                    'asset_id' => $asset->id,

                    'user_id' => Auth::id(),

                    'action' => 'returned',

                    'description' =>
                        'Asset dikembalikan dari assignment.',

                    'old_values' => $oldValues,

                    'new_values' => [
                        'status' => $freshAsset->status,

                        'returned_at' =>
                            $assignment->returned_at?->format('Y-m-d'),

                        'assignment_id' =>
                            $assignment->id,

                        'user_id' =>
                            $assignment->user_id,
                    ],
                ]);
            });

            /*
            |--------------------------------------------------------------------------
            | Tutup Form
            |--------------------------------------------------------------------------
            */
            $this->closeForm();

            /*
            |--------------------------------------------------------------------------
            | Pesan sukses
            |--------------------------------------------------------------------------
            */
            session()->flash(
                'success',
                'Asset berhasil dikembalikan.'
            );

        } catch (ValidationException $e) {

            /*
            |--------------------------------------------------------------------------
            | Validation error
            |--------------------------------------------------------------------------
            |
            | Biarkan Livewire menampilkan pesan validasi.
            |
            */
            throw $e;

        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | Error lainnya
            |--------------------------------------------------------------------------
            */
            report($e);

            session()->flash(
                'error',
                'Pengembalian asset gagal diproses. Silakan coba lagi.'
            );
        }
    }
}