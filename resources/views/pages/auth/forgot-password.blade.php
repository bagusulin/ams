<x-layouts::auth.login :title="__('Lupa Kata Sandi')">
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
                Lupa kata sandi?
            </h1>

            <p class="mt-2 text-sm leading-6 text-slate-500">
                Masukkan alamat email akun Anda.
                Kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
            </p>
        </div>

        <x-auth-session-status
            class="text-center"
            :status="session('status')"
        />

        <form
            method="POST"
            action="{{ route('password.email') }}"
            class="flex flex-col gap-5"
        >
            @csrf

            <flux:input
                name="email"
                label="Alamat email"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="nama@pnm.co.id"
            />

            <button
                type="submit"
                data-test="email-password-reset-link-button"
                class="flex h-12 w-full items-center justify-center
                       rounded-xl bg-[#225199] px-4 text-sm
                       font-semibold text-white shadow-sm transition
                       hover:bg-[#193f7a]
                       focus:outline-none focus:ring-4 focus:ring-blue-200"
            >
                Kirim tautan reset
            </button>
        </form>

        <p class="text-center text-sm text-slate-500">
            Ingat kata sandi Anda?

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