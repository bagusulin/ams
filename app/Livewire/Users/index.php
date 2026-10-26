<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    use WithPagination;

    private const ROLE_LABELS = [
        'admin' => 'Administrator',
        'it' => 'IT',
        'ppi' => 'PPI',
        'viewer' => 'Viewer',
    ];

    public string $search = '';

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $role = 'viewer';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($this->editingId),
            ],

            'role' => [
                'required',
                Rule::in(array_keys(self::ROLE_LABELS)),
                Rule::exists('roles', 'name')
                    ->where('guard_name', 'web'),
            ],

            'password' => [
                $this->editingId ? 'nullable' : 'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        Gate::authorize('users.create');
        Gate::authorize('roles.manage');

        $this->resetForm();

        $this->showForm = true;
    }

    public function editUser(int $id): void
    {
        Gate::authorize('users.update');
        Gate::authorize('roles.manage');

        $user = User::with('roles')->findOrFail($id);

        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->password_confirmation = '';

        $this->role = $user->getRoleNames()->first() ?? 'viewer';

        $this->resetValidation();

        $this->showForm = true;
    }

    public function save(): void
    {
        Gate::authorize(
            $this->editingId
                ? 'users.update'
                : 'users.create'
        );

        Gate::authorize('roles.manage');

        $validated = $this->validate();

        $currentUser = Auth::user();

        abort_unless($currentUser, 403);

        if ($this->editingId) {
            $user = User::findOrFail($this->editingId);

            // Jangan izinkan Admin menurunkan role akunnya sendiri.
            if (
                $user->id === $currentUser->id
                && $user->hasRole('admin')
                && $validated['role'] !== 'admin'
            ) {
                throw ValidationException::withMessages([
                    'role' =>
                        'Role Admin pada akun yang sedang digunakan tidak dapat diturunkan.',
                ]);
            }

            // Selalu pertahankan minimal satu Admin.
            if (
                $user->hasRole('admin')
                && $validated['role'] !== 'admin'
                && $this->adminCount() <= 1
            ) {
                throw ValidationException::withMessages([
                    'role' =>
                        'Minimal harus ada satu user dengan role Admin.',
                ]);
            }

            $data = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];

            // Password kosong berarti tidak mengubah password.
            if (filled($validated['password'] ?? null)) {
                $data['password'] = $validated['password'];
            }

            $user->update($data);

            // Setiap user dalam AMS menggunakan satu role utama.
            $user->syncRoles([$validated['role']]);

            session()->flash(
                'success',
                'Data user berhasil diperbarui.'
            );
        } else {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);

            $user->syncRoles([$validated['role']]);

            session()->flash(
                'success',
                'User baru berhasil ditambahkan.'
            );
        }

        $this->resetForm();
    }

    public function deleteUser(int $id): void
    {
        Gate::authorize('users.delete');
        Gate::authorize('roles.manage');

        $currentUser = Auth::user();

        abort_unless($currentUser, 403);

        $user = User::with('roles')->findOrFail($id);

        // Jangan izinkan user menghapus akunnya sendiri.
        if ($user->id === $currentUser->id) {
            throw ValidationException::withMessages([
                'delete' =>
                    'Anda tidak dapat menghapus akun yang sedang digunakan.',
            ]);
        }

        // Jangan menghapus Admin terakhir.
        if (
            $user->hasRole('admin')
            && $this->adminCount() <= 1
        ) {
            throw ValidationException::withMessages([
                'delete' =>
                    'Admin terakhir tidak dapat dihapus.',
            ]);
        }

        // Jaga relasi assignment dan riwayat audit.
        if (
            $user->assignments()->exists()
            || $user->assetHistories()->exists()
        ) {
            session()->flash(
                'error',
                'User tidak dapat dihapus karena memiliki assignment atau riwayat aset. Data tersebut harus tetap terjaga.'
            );

            return;
        }

        $user->delete();

        session()->flash(
            'success',
            'User berhasil dihapus.'
        );

        $this->resetPage();
    }

    private function adminCount(): int
    {
        return User::whereHas('roles', function ($query) {
            $query
                ->where('name', 'admin')
                ->where('guard_name', 'web');
        })->count();
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'name',
            'email',
            'password',
            'password_confirmation',
            'showForm',
        ]);

        $this->role = 'viewer';

        $this->resetValidation();
    }

    public function closeForm(): void
    {
        $this->resetForm();
    }

    public function render()
    {
        Gate::authorize('users.view');

        $users = User::query()
            ->with('roles')
            ->when(
                trim($this->search) !== '',
                function ($query) {
                    $search = '%' . trim($this->search) . '%';

                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', $search)
                            ->orWhere('email', 'like', $search);
                    });
                }
            )
            ->orderBy('name')
            ->paginate(10);

        $roles = Role::query()
            ->where('guard_name', 'web')
            ->whereIn('name', array_keys(self::ROLE_LABELS))
            ->orderBy('name')
            ->get();

        return view(
            'livewire.users.index',
            [
                'users' => $users,
                'roles' => $roles,
                'roleLabels' => self::ROLE_LABELS,
            ]
        );
    }
}
