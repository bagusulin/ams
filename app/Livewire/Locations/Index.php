<?php

namespace App\Livewire\Locations;

use Illuminate\Support\Facades\Gate;
use App\Models\Location;
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

    public string $address = '';

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

                Rule::unique('locations', 'code')
                    ->ignore($this->editingId),
            ],

            'address' => [
                'nullable',
                'string',
                'max:255',
            ],

            'is_active' => [
                'boolean',
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
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(): void
    {
        Gate::authorize('locations.create');
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
        Gate::authorize('locations.edit');
        $location = Location::findOrFail($id);

        $this->editingId = $location->id;

        $this->name = $location->name;

        $this->code = $location->code;

        $this->address = $location->address ?? '';

        $this->is_active = (bool) $location->is_active;

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
                ? 'locations.update'
                : 'locations.create'
        );

        /*
        |--------------------------------------------------------------------------
        | Rapikan input
        |--------------------------------------------------------------------------
        */

        $this->name = trim($this->name);

        $this->code = strtoupper(trim($this->code));

        $this->address = trim($this->address);


        /*
        |--------------------------------------------------------------------------
        | Validate
        |--------------------------------------------------------------------------
        */

        $validated = $this->validate();


        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        if ($this->editingId) {

            $location = Location::findOrFail(
                $this->editingId
            );

            $location->update($validated);

            session()->flash(
                'success',
                'Location berhasil diperbarui.'
            );

        } else {

            Location::create($validated);

            session()->flash(
                'success',
                'Location berhasil ditambahkan.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Reset
        |--------------------------------------------------------------------------
        */

        $this->resetForm();
    }


    /*
    |--------------------------------------------------------------------------
    | Toggle Status
    |--------------------------------------------------------------------------
    */

    public function toggleStatus(int $id): void
    {
        Gate::authorize('locations.update');
        $location = Location::findOrFail($id);

        $location->update([
            'is_active' => !$location->is_active,
        ]);

        session()->flash(
            'success',
            'Status location berhasil diperbarui.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(int $id): void
    {
        Gate::authorize('locations.delete');
        $location = Location::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Jangan hapus location yang sudah digunakan asset
        |--------------------------------------------------------------------------
        */

        if ($location->assets()->exists()) {

            session()->flash(
                'error',
                'Location tidak dapat dihapus karena sudah digunakan oleh asset.'
            );

            return;
        }

        $location->delete();

        session()->flash(
            'success',
            'Location berhasil dihapus.'
        );
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
    | Reset Form
    |--------------------------------------------------------------------------
    */

    private function resetForm(): void
    {
        $this->editingId = null;

        $this->name = '';

        $this->code = '';

        $this->address = '';

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
        $locations = Location::query()
            ->when(
                $this->search !== '',
                function ($query) {
                    $search = '%' . $this->search . '%';

                    $query->where(function ($query) use ($search) {
                        $query
                            ->where('name', 'like', $search)
                            ->orWhere('code', 'like', $search)
                            ->orWhere('address', 'like', $search);
                    });
                }
            )
            ->latest()
            ->paginate(10);

        return view(
            'livewire.locations.index',
            [
                'locations' => $locations,
            ]
        );
    }
}