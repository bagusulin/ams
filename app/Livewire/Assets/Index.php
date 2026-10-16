<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Location;
use App\Models\Vendor;
use App\Support\Assets\AssetAccess;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    /*
    |--------------------------------------------------------------------------
    | Search & Filter
    |--------------------------------------------------------------------------
    */

    public string $search = '';

    public string $categoryFilter = '';

    public string $locationFilter = '';

    public string $statusFilter = '';


    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    public bool $showForm = false;

    public ?int $editingId = null;

    public ?int $detailId = null;


    /*
    |--------------------------------------------------------------------------
    | Asset Fields
    |--------------------------------------------------------------------------
    */

    public string $asset_code = '';

    public string $category_id = '';

    public string $location_id = '';

    public string $vendor_id = '';

    public string $brand = '';

    public string $model = '';

    public string $serial_number = '';

    public string $status = 'available';

    public string $condition = 'good';

    public string $purchase_date = '';

    public string $rental_start_date = '';

    public string $rental_end_date = '';

    public string $rental_cost = '';

    public string $notes = '';


    /*
    |--------------------------------------------------------------------------
    | Detail Modal
    |--------------------------------------------------------------------------
    */

    public bool $showDetail = false;


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    protected function rules(): array
    {
        return [
            'asset_code' => [
                'required',
                'string',
                'max:255',

                Rule::unique('assets', 'asset_code')
                    ->ignore($this->editingId),
            ],

            'category_id' => [
                'required',
                'exists:asset_categories,id',
            ],

            'location_id' => [
                'nullable',
                'exists:locations,id',
            ],

            'vendor_id' => [
                'nullable',
                'exists:vendors,id',
            ],

            'brand' => [
                'nullable',
                'string',
                'max:255',
            ],

            'model' => [
                'nullable',
                'string',
                'max:255',
            ],

            'serial_number' => [
                'nullable',
                'string',
                'max:255',

                Rule::unique('assets', 'serial_number')
                    ->ignore($this->editingId),
            ],

            'status' => [
                'required',
                Rule::in([
                    'available',
                    'assigned',
                    'maintenance',
                    'lost',
                ]),
            ],

            'condition' => [
                'required',
                Rule::in([
                    'good',
                    'fair',
                    'damaged',
                ]),
            ],

            'purchase_date' => [
                'nullable',
                'date',
            ],

            'rental_start_date' => [
                'nullable',
                'date',
            ],

            'rental_end_date' => [
                'nullable',
                'date',
                'after_or_equal:rental_start_date',
            ],

            'rental_cost' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Filter Reset
    |--------------------------------------------------------------------------
    */

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatedLocationFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }


    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(): void
    {
        Gate::authorize('assets.create');

        $this->resetForm();
        $this->showForm = true;
    }


    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(int $id): void
    {
        $user = Auth::user();

        Gate::authorize('assets.update');

        $asset = AssetAccess::assetsQuery($user)
            ->with(['category', 'location', 'vendor'])
            ->findOrFail($id);

        abort_unless(
            AssetAccess::canManageCategory(
                $user,
                $asset->category?->code
            ),
            403
        );

        $this->editingId = $asset->id;

        $this->asset_code = $asset->asset_code;
        $this->category_id = (string) $asset->category_id;

        $this->location_id = $asset->location_id
            ? (string) $asset->location_id
            : '';

        $this->vendor_id = $asset->vendor_id
            ? (string) $asset->vendor_id
            : '';

        $this->brand = $asset->brand ?? '';
        $this->model = $asset->model ?? '';
        $this->serial_number = $asset->serial_number ?? '';

        $this->status = $asset->status;
        $this->condition = $asset->condition;

        $this->purchase_date = $asset->purchase_date
            ? $asset->purchase_date->format('Y-m-d')
            : '';

        $this->rental_start_date = $asset->rental_start_date
            ? $asset->rental_start_date->format('Y-m-d')
            : '';

        $this->rental_end_date = $asset->rental_end_date
            ? $asset->rental_end_date->format('Y-m-d')
            : '';

        $this->rental_cost = $asset->rental_cost
            ? (string) $asset->rental_cost
            : '';

        $this->notes = $asset->notes ?? '';

        $this->resetValidation();

        $this->showForm = true;
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

        $permission = $this->editingId
            ? 'assets.update'
            : 'assets.create';

        Gate::authorize($permission);

        // Rapikan input.
        $this->asset_code = strtoupper(trim($this->asset_code));
        $this->brand = trim($this->brand);
        $this->model = trim($this->model);
        $this->serial_number = trim($this->serial_number);
        $this->notes = trim($this->notes);

        // Validasi field sesuai rules() yang sudah ada.
        $validated = $this->validate();

        // Pastikan kategori benar-benar ada.
        $category = AssetCategory::query()
            ->findOrFail($validated['category_id']);

        // Kategori tidak aktif tidak boleh dipakai untuk menyimpan.
        if (! $category->is_active) {
            throw ValidationException::withMessages([
                'category_id' => 'Kategori aset tidak aktif.',
            ]);
        }

        // Cek kategori sesuai role Admin, IT, atau PPI.
        if (! AssetAccess::canManageCategory(
            $user,
            $category->code
        )) {
            throw ValidationException::withMessages([
                'category_id' =>
                    'Anda tidak memiliki akses untuk mengelola kategori aset ini.',
            ]);
        }

        // Konversi string kosong menjadi null.
        $validated['location_id'] =
            $validated['location_id'] ?: null;

        $validated['vendor_id'] =
            $validated['vendor_id'] ?: null;

        $validated['purchase_date'] =
            $validated['purchase_date'] ?: null;

        $validated['rental_start_date'] =
            $validated['rental_start_date'] ?: null;

        $validated['rental_end_date'] =
            $validated['rental_end_date'] ?: null;

        $validated['rental_cost'] =
            $validated['rental_cost'] !== ''
                ? $validated['rental_cost']
                : null;

        if ($this->editingId) {
            // Hanya aset dalam cakupan role yang boleh diubah.
            $asset = AssetAccess::assetsQuery($user)
                ->findOrFail($this->editingId);

            abort_unless(
                AssetAccess::canManageCategory(
                    $user,
                    $asset->category?->code
                ),
                403
            );

            $asset->update($validated);

            session()->flash(
                'success',
                'Asset berhasil diperbarui.'
            );
        } else {
            Asset::create($validated);

            session()->flash(
                'success',
                'Asset berhasil ditambahkan.'
            );
        }

        $this->resetForm();
    }


    public function show(int $id): void
    {
        $user = Auth::user();

        abort_unless(
            $user && Gate::forUser($user)->allows('assets.view'),
            403
        );

        AssetAccess::assetsQuery($user)
            ->findOrFail($id);

        $this->detailId = $id;

        $this->showDetail = true;
    }


    public function closeDetail(): void
    {
        $this->detailId = null;

        $this->showDetail = false;
    }


    /*
    |--------------------------------------------------------------------------
    | Archive
    |--------------------------------------------------------------------------
    */

    public function archive(int $id): void
    {
        $user = Auth::user();

        Gate::authorize('assets.delete');

        $asset = AssetAccess::assetsQuery($user)
            ->findOrFail($id);

        abort_unless(
            AssetAccess::canManageCategory(
                $user,
                $asset->category?->code
            ),
            403
        );

        $asset->delete();

        session()->flash(
            'success',
            'Asset berhasil di-archive.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Reset Form
    |--------------------------------------------------------------------------
    */

    private function resetForm(): void
    {
        $this->editingId = null;

        $this->asset_code = '';

        $this->category_id = '';

        $this->location_id = '';

        $this->vendor_id = '';

        $this->brand = '';

        $this->model = '';

        $this->serial_number = '';

        $this->status = 'available';

        $this->condition = 'good';

        $this->purchase_date = '';

        $this->rental_start_date = '';

        $this->rental_end_date = '';

        $this->rental_cost = '';

        $this->notes = '';

        $this->showForm = false;

        $this->resetValidation();
    }


    /*
    |--------------------------------------------------------------------------
    | Cancel
    |--------------------------------------------------------------------------
    */

    public function cancel(): void
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
        /*
        |--------------------------------------------------------------------------
        | Assets
        |--------------------------------------------------------------------------
        */

        Gate::authorize('assets.view');

        $assets = AssetAccess::assetsQuery(
            $user,
            Asset::query()->with([
                'category',
                'location',
                'vendor',
            ])
        )
            ->when(
                $this->search !== '',
                function ($query) {

                    $search = '%' . $this->search . '%';

                    $query->where(function ($query) use ($search) {

                        $query
                            ->where(
                                'asset_code',
                                'like',
                                $search
                            )
                            ->orWhere(
                                'brand',
                                'like',
                                $search
                            )
                            ->orWhere(
                                'model',
                                'like',
                                $search
                            )
                            ->orWhere(
                                'serial_number',
                                'like',
                                $search
                            );

                    });
                }
            )
            ->when(
                $this->categoryFilter !== '',
                function ($query) {
                    $query->where(
                        'category_id',
                        $this->categoryFilter
                    );
                }
            )
            ->when(
                $this->locationFilter !== '',
                function ($query) {
                    $query->where(
                        'location_id',
                        $this->locationFilter
                    );
                }
            )
            ->when(
                $this->statusFilter !== '',
                function ($query) {
                    $query->where(
                        'status',
                        $this->statusFilter
                    );
                }
            )
            ->latest()
            ->paginate(10);


        /*
        |--------------------------------------------------------------------------
        | Dropdown Data
        |--------------------------------------------------------------------------
        */

        $categories = AssetAccess::categoriesQueryFor($user)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();


        $locations = Location::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();


        $vendors = Vendor::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Detail Asset
        |--------------------------------------------------------------------------
        */

        $detailAsset = $this->showDetail && $this->detailId
            ? AssetAccess::assetsQuery(
                $user,
                Asset::query()->with([
                    'category',
                    'location',
                    'vendor',
                ])
            )->find($this->detailId)
            : null;


        return view(
            'livewire.assets.index',
            [
                'assets' => $assets,
                'categories' => $categories,
                'locations' => $locations,
                'vendors' => $vendors,
                'detailAsset' => $detailAsset,
            ]
        );
    }
}