
@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head', ['title' => $title])

    <meta name="theme-color" content="#225199">
</head>

<body class="min-h-screen bg-white antialiased text-slate-900">

    <main class="grid min-h-svh lg:grid-cols-[1.05fr_0.95fr]">

        {{-- PANEL BRANDING --}}
        <section
            class="relative hidden min-h-svh overflow-hidden
                   bg-[#225199] px-10 py-10 text-white
                   lg:flex lg:flex-col lg:justify-between
                   xl:px-16 xl:py-12"
        >
            {{-- Dekorasi latar --}}
            <div
                class="pointer-events-none absolute -left-32 -top-32
                       size-96 rounded-full border border-white/10"
            ></div>

            <div
                class="pointer-events-none absolute -bottom-40 -right-24
                       size-[28rem] rounded-full border border-white/10"
            ></div>

            {{-- Branding bagian atas --}}
            <div class="relative z-10 flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-bold tracking-[0.2em]">
                        PNM
                    </p>

                    <p class="mt-1 text-xs tracking-[0.16em] text-blue-100">
                        ASSET MANAGEMENT SYSTEM
                    </p>
                </div>

                <span
                    class="rounded-full border border-white/25
                           px-3 py-1.5 text-xs font-semibold tracking-wider"
                >
                    AMS
                </span>
            </div>

            {{-- Logo asli --}}
            <div class="relative z-10 flex flex-1 items-center justify-center py-10">
                <img
                    src="{{ asset('images/logo_ams.png') }}"
                    alt="Logo PNM AMS"
                    class="w-full max-w-[440px] rounded-2xl object-contain"
                >
            </div>

            {{-- Pesan branding bagian bawah --}}
            <div class="relative z-10">
                <p class="text-2xl font-bold tracking-tight xl:text-3xl">
                    Pengelolaan aset, lebih teratur.
                </p>

                <p class="mt-3 max-w-md text-sm leading-6 text-blue-100">
                    Pantau inventaris, penugasan, pemeliharaan,
                    dan masa sewa aset melalui satu sistem.
                </p>

                <div
                    class="mt-8 flex items-center gap-2 text-xs
                           uppercase tracking-[0.16em] text-blue-200"
                >
                    <span class="size-2 rounded-full bg-emerald-300"></span>
                    Internal Asset Management System
                </div>
            </div>
        </section>

        {{-- PANEL LOGIN --}}
        <section
            class="flex min-h-svh flex-col justify-center
                   px-6 py-10 sm:px-10
                   lg:px-12 xl:px-20"
        >
            <div class="mx-auto w-full max-w-md">

                {{-- Logo tampil pada layar kecil --}}
                <div class="mb-8 flex justify-center lg:hidden">
                    <img
                        src="{{ asset('images/logo_ams.png') }}"
                        alt="Logo PNM AMS"
                        class="w-full max-w-[190px] rounded-2xl"
                    >
                </div>

                {{ $slot }}

            </div>

            <p class="mt-12 text-center text-xs leading-5 text-slate-400">
                PNM AMS · Akses hanya untuk pengguna terdaftar
            </p>
        </section>

    </main>

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
</body>
</html>
