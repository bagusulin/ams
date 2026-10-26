
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">
                Administrasi
            </p>

            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                Manajemen User
            </h1>

            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Kelola akun pengguna dan role akses AMS.
            </p>
        </div>

        @can('users.create')
            <button
                type="button"
                wire:click="openCreate"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#225199] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#193f7a]"
            >
                <svg
                    class="size-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    aria-hidden="true"
                >
                    <path d="M12 5v14M5 12h14"/>
                </svg>

                Tambah User
            </button>
        @endcan
    </div>

    {{-- FLASH MESSAGE --}}
    @if (session()->has('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-900 dark:bg-red-950 dark:text-red-300">
            {{ session('error') }}
        </div>
    @endif

    @error('delete')
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-900 dark:bg-red-950 dark:text-red-300">
            {{ $message }}
        </div>
    @enderror

    {{-- SUMMARY --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-blue-50 text-[#225199] dark:bg-blue-950 dark:text-blue-300">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>

                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Total User
                    </p>

                    <p class="text-2xl font-bold text-slate-900 dark:text-white">
                        {{ number_format($users->total()) }}
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M9 12l2 2 4-4"/>
                        <circle cx="12" cy="12" r="10"/>
                    </svg>
                </div>

                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Status Pengelolaan
                    </p>

                    <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">
                        Role & Permission
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-violet-50 text-violet-700 dark:bg-violet-950 dark:text-violet-300">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>

                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Role Tersedia
                    </p>

                    <p class="text-2xl font-bold text-slate-900 dark:text-white">
                        {{ $roles->count() }}
                    </p>
                </div>
            </div>
        </div>

    </div>

    {{-- FORM TAMBAH / EDIT --}}
    @if ($showForm)
        @can('roles.manage')
            <div class="rounded-xl border border-blue-100 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900 sm:p-6">

                <div class="mb-5 flex items-start justify-between gap-4 border-b border-slate-100 pb-4 dark:border-slate-700">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
                            {{ $editingId ? 'Edit User' : 'Tambah User Baru' }}
                        </h2>

                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Lengkapi informasi akun dan pilih role pengguna.
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="closeForm"
                        class="rounded-lg px-3 py-1.5 text-sm text-slate-500 transition hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800"
                    >
                        Tutup
                    </button>
                </div>

                <form wire:submit="save" class="space-y-5">

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                        <div>
                            <label for="user-name" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">
                                Nama lengkap
                            </label>

                            <input
                                id="user-name"
                                type="text"
                                wire:model="name"
                                autocomplete="name"
                                required
                                maxlength="255"
                                placeholder="Masukkan nama lengkap"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                            >

                            @error('name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="user-email" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">
                                Alamat email
                            </label>

                            <input
                                id="user-email"
                                type="email"
                                wire:model="email"
                                autocomplete="email"
                                required
                                maxlength="255"
                                placeholder="nama@perusahaan.co.id"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                            >

                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="user-role" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">
                                Role pengguna
                            </label>

                            <select
                                id="user-role"
                                wire:model="role"
                                required
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                            >
                                @foreach ($roles as $roleOption)
                                    <option value="{{ $roleOption->name }}">
                                        {{ $roleLabels[$roleOption->name] ?? ucfirst($roleOption->name) }}
                                    </option>
                                @endforeach
                            </select>

                            @error('role')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="hidden md:block"></div>

                        <div>
                            <label for="user-password" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">
                                {{ $editingId ? 'Password baru (opsional)' : 'Password' }}
                            </label>

                            <input
                                id="user-password"
                                type="password"
                                wire:model="password"
                                autocomplete="{{ $editingId ? 'new-password' : 'new-password' }}"
                                minlength="8"
                                {{ $editingId ? '' : 'required' }}
                                placeholder="{{ $editingId ? 'Kosongkan jika tidak diubah' : 'Minimal 8 karakter' }}"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                            >

                            @error('password')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="user-password-confirmation" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">
                                Konfirmasi password
                            </label>

                            <input
                                id="user-password-confirmation"
                                type="password"
                                wire:model="password_confirmation"
                                autocomplete="new-password"
                                minlength="8"
                                {{ $editingId ? '' : 'required' }}
                                placeholder="Ulangi password"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                            >

                            @error('password_confirmation')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <div class="rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm leading-6 text-blue-800 dark:border-blue-900 dark:bg-blue-950 dark:text-blue-200">
                        <strong>Informasi akses:</strong>
                        Admin memiliki akses penuh. IT tidak dapat mengelola kategori Mobil dan Sepeda Motor. PPI tidak dapat mengelola kategori Notebook dan Handphone. Viewer hanya dapat melihat data.
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 dark:border-slate-700 sm:flex-row sm:justify-end">
                        <button
                            type="button"
                            wire:click="closeForm"
                            class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="save"
                            class="rounded-lg bg-[#225199] px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#193f7a] disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <span wire:loading.remove wire:target="save">
                                {{ $editingId ? 'Simpan Perubahan' : 'Simpan User' }}
                            </span>

                            <span wire:loading wire:target="save">
                                Menyimpan...
                            </span>
                        </button>
                    </div>

                </form>
            </div>
        @endcan
    @endif

    {{-- DAFTAR USER --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">

        <div class="flex flex-col justify-between gap-4 border-b border-slate-100 p-5 dark:border-slate-700 sm:flex-row sm:items-center">

            <div>
                <h2 class="font-semibold text-slate-900 dark:text-white">
                    Daftar Pengguna
                </h2>

                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                    {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }}
                    dari {{ $users->total() }} user
                </p>
            </div>

            <div class="w-full sm:max-w-xs">
                <input
                    type="search"
                    wire:model.live.debounce.350ms="search"
                    placeholder="Cari nama atau email..."
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                >
            </div>
        </div>

        <div class="overflow-x-auto">

            <table class="w-full min-w-[720px] text-left text-sm">

                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-800 dark:text-slate-300">
                    <tr>
                        <th class="px-5 py-3.5">Pengguna</th>
                        <th class="px-5 py-3.5">Role</th>
                        <th class="px-5 py-3.5">Tanggal dibuat</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">

                    @forelse ($users as $user)
                        <tr wire:key="user-{{ $user->id }}" class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">

                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">

                                    <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-blue-50 text-sm font-bold text-[#225199] dark:bg-blue-950 dark:text-blue-300">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>

                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-900 dark:text-white">
                                            {{ $user->name }}

                                            @if ($user->id === auth()->id())
                                                <span class="ml-1 text-xs font-normal text-blue-600 dark:text-blue-300">
                                                    (Anda)
                                                </span>
                                            @endif
                                        </p>

                                        <p class="mt-0.5 truncate text-xs text-slate-500 dark:text-slate-400">
                                            {{ $user->email }}
                                        </p>
                                    </div>

                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse ($user->getRoleNames() as $roleName)
                                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-[#225199] dark:bg-blue-950 dark:text-blue-300">
                                            {{ $roleLabels[$roleName] ?? ucfirst($roleName) }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-slate-400">
                                            Belum memiliki role
                                        </span>
                                    @endforelse
                                </div>
                            </td>

                            <td class="px-5 py-4 text-slate-500 dark:text-slate-400">
                                {{ $user->created_at?->format('d M Y') ?? '-' }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">

                                    @can('users.update')
                                        <button
                                            type="button"
                                            wire:click="editUser({{ $user->id }})"
                                            class="rounded-lg border border-blue-200 px-3 py-1.5 text-xs font-semibold text-[#225199] transition hover:bg-blue-50 dark:border-blue-900 dark:text-blue-300 dark:hover:bg-blue-950"
                                        >
                                            Edit
                                        </button>
                                    @endcan

                                    @can('users.delete')
                                        <button
                                            type="button"
                                            wire:click="deleteUser({{ $user->id }})"
                                            wire:confirm="Yakin ingin menghapus user {{ $user->name }}? Tindakan ini tidak dapat dibatalkan."
                                            class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 dark:border-red-900 dark:text-red-300 dark:hover:bg-red-950"
                                        >
                                            Hapus
                                        </button>
                                    @endcan

                                    @cannot('users.update')
                                        @cannot('users.delete')
                                            <span class="text-xs text-slate-400">
                                                Lihat saja
                                            </span>
                                        @endcannot
                                    @endcannot

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center">
                                <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-slate-100 text-slate-400 dark:bg-slate-800">
                                    <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <circle cx="11" cy="11" r="8"/>
                                        <path d="m21 21-4.35-4.35"/>
                                    </svg>
                                </div>

                                <p class="mt-3 text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    User tidak ditemukan
                                </p>

                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                    Ubah kata pencarian atau tambahkan user baru.
                                </p>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>

        </div>

        @if ($users->hasPages())
            <div class="border-t border-slate-100 px-5 py-4 dark:border-slate-700">
                {{ $users->links() }}
            </div>
        @endif

    </div>

</div>
