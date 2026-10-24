
<x-layouts::auth.login :title="__('Log in')">

    <div class="flex flex-col gap-7">

        {{-- Heading --}}
        <div>
            <div
                class="mb-4 inline-flex items-center rounded-full
                       bg-blue-50 px-3 py-1.5 text-xs font-semibold
                       tracking-wide text-[#225199]"
            >
                PNM AMS · PORTAL INTERNAL
            </div>

            <h1 class="text-3xl font-bold tracking-tight text-slate-900">
                Selamat datang kembali
            </h1>

            <p class="mt-2 text-sm leading-6 text-slate-500">
                Masuk menggunakan akun Anda untuk melanjutkan
                ke Asset Management System.
            </p>
        </div>

        {{-- Notifikasi login --}}
        <x-auth-session-status
            class="text-center"
            :status="session('status')"
        />

        {{-- FORM LOGIN --}}
        <form
            method="POST"
            action="{{ route('login.store') }}"
            class="flex flex-col gap-5"
        >
            @csrf

            {{-- Email --}}
            <flux:input
                name="email"
                label="Alamat email"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="username"
                placeholder="nama@pnm.co.id"
            />

            {{-- Password --}}
            <div class="relative">
                <flux:input
                    name="password"
                    label="Kata sandi"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="Masukkan kata sandi"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link
                        class="absolute end-0 top-0 text-xs font-medium
                               text-[#225199] hover:text-[#193f7a]"
                        :href="route('password.request')"
                        wire:navigate
                    >
                        Lupa kata sandi?
                    </flux:link>
                @endif
            </div>

            {{-- Ingat saya --}}
            <div class="flex items-center justify-between gap-4">
                <flux:checkbox
                    name="remember"
                    label="Ingat saya"
                    :checked="old('remember')"
                />
            </div>

            {{-- Tombol login --}}
            <button
                type="submit"
                data-test="login-button"
                class="flex h-12 w-full items-center justify-center
                       gap-2 rounded-xl bg-[#225199] px-4
                       text-sm font-semibold text-white
                       shadow-sm transition
                       hover:bg-[#193f7a]
                       focus:outline-none focus:ring-4 focus:ring-blue-200
                       active:scale-[0.99]"
            >
                Masuk ke AMS

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    class="size-4"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M4.5 12h15m-6-6 6 6-6 6"
                    />
                </svg>
            </button>

        </form>

        <div class="flex items-center gap-3">
            <div class="h-px flex-1 bg-slate-200"></div>

            <span class="text-xs text-slate-400">
                Secure sign in
            </span>

            <div class="h-px flex-1 bg-slate-200"></div>
        </div>

        <p class="text-center text-xs leading-5 text-slate-500">
            Gunakan akun yang telah terdaftar pada sistem AMS.
            Jika mengalami kendala, hubungi administrator.
        </p>

    </div>

</x-layouts::auth.login>
