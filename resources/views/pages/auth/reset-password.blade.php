<x-layouts::auth.login :title="__('Reset Kata Sandi')">
    <div class="flex flex-col gap-7">

        <div>
            <span
                class="inline-flex items-center rounded-full
                       bg-blue-50 px-3 py-1.5 text-xs
                       font-semibold tracking-wide text-[#225199]"
            >
                PNM AMS · PORTAL INTERNAL
            </span>

            <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-900">
                Atur ulang kata sandi
            </h1>

            <p class="mt-2 text-sm leading-6 text-slate-500">
                Buat kata sandi baru untuk akun AMS Anda.
            </p>
        </div>

        <x-auth-session-status
            class="text-center"
            :status="session('status')"
        />

        <form
            method="POST"
            action="{{ route('password.update') }}"
            class="flex flex-col gap-5"
        >
            @csrf

            <input
                type="hidden"
                name="token"
                value="{{ request()->route('token') }}"
            >

            <flux:input
                name="email"
                label="Alamat email"
                :value="request('email')"
                type="email"
                required
                readonly
                autocomplete="email"
            />

            <flux:input
                name="password"
                label="Kata sandi baru"
                type="password"
                required
                autofocus
                autocomplete="new-password"
                placeholder="Masukkan kata sandi baru"
                viewable
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
            />

            <flux:input
                name="password_confirmation"
                label="Konfirmasi kata sandi"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Ulangi kata sandi baru"
                viewable
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
            />

            <button
                type="submit"
                data-test="reset-password-button"
                class="flex h-12 w-full items-center justify-center
                       rounded-xl bg-[#225199] px-4 text-sm
                       font-semibold text-white shadow-sm transition
                       hover:bg-[#193f7a]
                       focus:outline-none focus:ring-4 focus:ring-blue-200"
            >
                Simpan kata sandi baru
            </button>
        </form>

        <p class="text-center text-sm text-slate-500">
            <a
                href="{{ route('login') }}"
                wire:navigate
                class="font-semibold text-[#225199] hover:text-[#193f7a]"
            >
                Kembali ke login
            </a>
        </p>

    </div>
</x-layouts::auth.login>