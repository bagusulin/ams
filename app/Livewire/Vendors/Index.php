<?php

namespace App\Livewire\Vendors;

use Illuminate\Support\Facades\Gate;
use App\Models\Vendor;
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

    public string $contact_person = '';

    public string $phone = '';

    public string $email = '';

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

            'contact_person' => [
                'nullable',
                'string',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
                'max:1000',
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
        Gate::authorize('vendors.create');
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
        Gate::authorize('vendors.edit');
        $vendor = Vendor::findOrFail($id);

        $this->editingId = $vendor->id;

        $this->name = $vendor->name;

        $this->contact_person = $vendor->contact_person ?? '';

        $this->phone = $vendor->phone ?? '';

        $this->email = $vendor->email ?? '';

        $this->address = $vendor->address ?? '';

        $this->is_active = (bool) $vendor->is_active;

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
                ? 'vendors.update'
                : 'vendors.create'
        );
        // Rapikan input
        $this->name = trim($this->name);

        $this->contact_person = trim(
            $this->contact_person
        );

        $this->phone = trim(
            $this->phone
        );

        $this->email = trim(
            $this->email
        );

        $this->address = trim(
            $this->address
        );


        // Validasi
        $validated = $this->validate();


        // Update
        if ($this->editingId) {

            $vendor = Vendor::findOrFail(
                $this->editingId
            );

            $vendor->update($validated);

            session()->flash(
                'success',
                'Vendor berhasil diperbarui.'
            );

        }

        // Create
        else {

            Vendor::create($validated);

            session()->flash(
                'success',
                'Vendor berhasil ditambahkan.'
            );
        }


        $this->resetForm();
    }


    /*
    |--------------------------------------------------------------------------
    | Toggle Status
    |--------------------------------------------------------------------------
    */

    public function toggleStatus(int $id): void
    {
        Gate::authorize('vendors.update');
        $vendor = Vendor::findOrFail($id);

        $vendor->update([
            'is_active' => ! $vendor->is_active,
        ]);

        session()->flash(
            'success',
            'Status vendor berhasil diperbarui.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(int $id): void
    {
        Gate::authorize('vendors.delete');
        $vendor = Vendor::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Jangan hapus vendor yang sudah digunakan asset
        |--------------------------------------------------------------------------
        */

        if ($vendor->assets()->exists()) {

            session()->flash(
                'error',
                'Vendor tidak dapat dihapus karena sudah digunakan oleh asset.'
            );

            return;
        }

        $vendor->delete();

        session()->flash(
            'success',
            'Vendor berhasil dihapus.'
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

        $this->contact_person = '';

        $this->phone = '';

        $this->email = '';

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
        $vendors = Vendor::query()
            ->when(
                $this->search !== '',
                function ($query) {

                    $search = '%' . $this->search . '%';

                    $query->where(function ($query) use ($search) {

                        $query
                            ->where('name', 'like', $search)
                            ->orWhere(
                                'contact_person',
                                'like',
                                $search
                            )
                            ->orWhere(
                                'phone',
                                'like',
                                $search
                            )
                            ->orWhere(
                                'email',
                                'like',
                                $search
                            )
                            ->orWhere(
                                'address',
                                'like',
                                $search
                            );

                    });
                }
            )
            ->latest()
            ->paginate(10);

        return view(
            'livewire.vendors.index',
            [
                'vendors' => $vendors,
            ]
        );
    }
}