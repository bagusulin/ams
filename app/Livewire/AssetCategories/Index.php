<?php

namespace App\Livewire\AssetCategories;

use Illuminate\Support\Facades\Gate;
use App\Models\AssetCategory;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    public string $search = '';


    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $code = '';

    public string $description = '';

    public string $default_rental_years = '';

    public bool $is_active = true;


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'code' => [
                'required',
                'string',
                'max:20',

                Rule::unique('asset_categories', 'code')
                    ->ignore($this->editingId),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'is_active' => [
                'boolean',
            ],

            'default_rental_years' => [
                'nullable',
                'integer',
                'min:1',
                'max:10',
            ],
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    public function updatedSearch(): void
    {
        $this->resetPage();
    }


    /*
    |--------------------------------------------------------------------------
    | Open Create Form
    |--------------------------------------------------------------------------
    */

    public function create(): void
    {
        Gate::authorize('asset-categories.create');
        $this->resetForm();
        $this->showForm = true;
    }


    /*
    |--------------------------------------------------------------------------
    | Open Edit Form
    |--------------------------------------------------------------------------
    */

    public function edit(int $id): void
    {
        Gate::authorize('asset-categories.edit');
        $category = AssetCategory::findOrFail($id);

        $this->editingId = $category->id;

        $this->name = $category->name;

        $this->code = $category->code;

        $this->default_rental_years = $category->default_rental_years !== null
            ? (string) $category->default_rental_years
            : '';

        $this->is_active = (bool) $category->is_active;

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
        Gate::authorize(
            $this->editingId
                ? 'asset-categories.update'
                : 'asset-categories.create'
        );
        $validated = $this->validate();

        if ($this->editingId) {

            $category = AssetCategory::findOrFail(
                $this->editingId
            );

            $category->update($validated);

            session()->flash(
                'success',
                'Asset category berhasil diperbarui.'
            );

        } else {

            AssetCategory::create($validated);

            session()->flash(
                'success',
                'Asset category berhasil ditambahkan.'
            );
        }

        $this->resetForm();
    }


    /*
    |--------------------------------------------------------------------------
    | Toggle Active
    |--------------------------------------------------------------------------
    */

    public function toggleStatus(int $id): void
    {
        Gate::authorize('asset-categories.update');
        $category = AssetCategory::findOrFail($id);

        $category->update([
            'is_active' => !$category->is_active,
        ]);

        session()->flash(
            'success',
            'Status asset category berhasil diperbarui.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(int $id): void
    {
        Gate::authorize('asset-categories.delete');
        $category = AssetCategory::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Jangan hapus kategori yang sudah digunakan Asset
        |--------------------------------------------------------------------------
        */

        if ($category->assets()->exists()) {

            session()->flash(
                'error',
                'Kategori tidak dapat dihapus karena sudah digunakan oleh asset.'
            );

            return;
        }

        $category->delete();

        session()->flash(
            'success',
            'Asset category berhasil dihapus.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Close Form
    |--------------------------------------------------------------------------
    */

    public function cancel(): void
    {
        $this->resetForm();
    }


    /*
    |--------------------------------------------------------------------------
    | Reset Form
    |--------------------------------------------------------------------------
    */

    private function resetForm(): void
    {
        $this->editingId = null;

        $this->name = '';

        $this->code = '';

        $this->description = '';

        $this->default_rental_years = '';

        $this->is_active = true;

        $this->showForm = false;

        $this->resetValidation();
    }


    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $categories = AssetCategory::query()
            ->when(
                $this->search !== '',
                function ($query) {
                    $search = '%' . $this->search . '%';

                    $query->where(function ($query) use ($search) {
                        $query
                            ->where('name', 'like', $search)
                            ->orWhere('code', 'like', $search)
                            ->orWhere('description', 'like', $search);
                    });
                }
            )
            ->latest()
            ->paginate(10);

        return view(
            'livewire.asset-categories.index',
            [
                'categories' => $categories,
            ]
        );
    }

    public function updatedName(): void
    {
        $this->applySuggestedRentalYears();
    }

    public function updatedCode(): void
    {
        $this->applySuggestedRentalYears();
    }

    private function applySuggestedRentalYears(): void
    {
        $code = strtoupper(trim($this->code));

        $codePeriods = [
            'NBK' => 3,
            'LPT' => 3,
            'HPN' => 2,
            'MTR' => 3,
            'MBL' => 4,
            'CTV' => 2,
            'PRN' => 2,
            'SCN' => 3,
        ];

        if (isset($codePeriods[$code])) {
            $this->default_rental_years = (string) $codePeriods[$code];

            return;
        }

        $name = mb_strtolower($this->name);
        $normalizedName = preg_replace('/[^a-z0-9]/', '', $name);

        $namePeriods = [
            'notebook' => 3,
            'laptop' => 3,
            'handphone' => 2,
            'hp' => 2,
            'sepedamotor' => 3,
            'motor' => 3,
            'mobil' => 4,
            'cctv' => 2,
            'printer' => 2,
            'scanner' => 3,
        ];

        foreach ($namePeriods as $keyword => $years) {
            if (str_contains($normalizedName, $keyword)) {
                $this->default_rental_years = (string) $years;

                return;
            }
        }
    }
}