@extends('layouts.admin')

@section('pageTitle', 'Import HP CSV')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">

    <div>
        <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold tracking-wide text-[#225199] dark:bg-blue-950 dark:text-blue-300">
            <span class="size-2 rounded-full bg-[#225199]"></span>
            PNM AMS · IMPORT DATA
        </div>

        <h1 class="mt-3 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
            Import HP dari CSV
        </h1>

        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            Tambahkan data handphone secara massal menggunakan template CSV AMS.
        </p>
    </div>

    
    @if (session('import_result'))
        @php($result = session('import_result'))

        <div class="space-y-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-950 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-100">

            <div>
                <p class="font-semibold">
                    @if (($result['imported'] ?? 0) > 0)
                        Import selesai diproses.
                    @else
                        Tidak ada aset baru yang ditambahkan.
                    @endif
                </p>

                <p class="mt-1 text-xs text-emerald-800 dark:text-emerald-200">
                    Baris duplikat dan baris yang tidak valid dilewati.
                    Baris lain yang valid tetap diproses.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div class="rounded-lg border border-emerald-200 bg-white p-3 dark:border-emerald-900 dark:bg-slate-900">
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Berhasil ditambahkan
                    </p>

                    <p class="mt-1 text-2xl font-bold text-emerald-700 dark:text-emerald-300">
                        {{ number_format($result['imported'] ?? 0) }}
                    </p>
                </div>

                <div class="rounded-lg border border-amber-200 bg-white p-3 dark:border-amber-900 dark:bg-slate-900">
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Baris dilewati
                    </p>

                    <p class="mt-1 text-2xl font-bold text-amber-700 dark:text-amber-300">
                        {{ number_format($result['skipped_count'] ?? 0) }}
                    </p>
                </div>
            </div>

            @if (! empty($result['skipped_rows']))
                <div>
                    <p class="font-semibold">
                        Detail baris yang dilewati:
                    </p>

                    <ul class="mt-2 max-h-72 space-y-1 overflow-y-auto rounded-lg border border-amber-200 bg-white p-3 text-xs text-slate-700 dark:border-amber-900 dark:bg-slate-900 dark:text-slate-200">
                        @foreach ($result['skipped_rows'] as $skippedRow)
                            <li>{{ $skippedRow }}</li>
                        @endforeach
                    </ul>

                    @if (! empty($result['skipped_more']))
                        <p class="mt-2 text-xs text-amber-800 dark:text-amber-200">
                            Detail yang ditampilkan dibatasi sampai 50 baris.
                            Jumlah total baris yang dilewati:
                            {{ $result['skipped_count'] }}.
                        </p>
                    @endif
                </div>
            @endif

            @if (! empty($result['warnings']))
                <div>
                    <p class="font-semibold">
                        Penyesuaian berdasarkan referensi cabang:
                    </p>

                    <ul class="mt-2 list-inside list-disc space-y-1 text-xs">
                        @foreach ($result['warnings'] as $warning)
                            <li>{{ $warning }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </div>
    @endif


    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 dark:border-red-900 dark:bg-red-950 dark:text-red-200">
            <p class="font-semibold">Import belum dilakukan.</p>
            <ul class="mt-2 list-inside list-disc space-y-1 whitespace-pre-line">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_0.85fr]">

        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900 sm:p-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
                Unggah file CSV
            </h2>

            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Pilih file CSV yang berisi data HP yang akan ditambahkan.
            </p>

            <form
                action="{{ route('assets.import.store') }}"
                method="POST"
                enctype="multipart/form-data"
                class="mt-6 space-y-5"
            >
                @csrf

                <div>
                    <label for="file" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                        File CSV
                    </label>

                    <input
                        id="file"
                        name="file"
                        type="file"
                        accept=".csv,text/csv"
                        required
                        class="block w-full cursor-pointer rounded-lg border border-slate-300 bg-white text-sm text-slate-700 file:mr-4 file:border-0 file:bg-blue-50 file:px-4 file:py-3 file:text-sm file:font-semibold file:text-[#225199] hover:file:bg-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200 dark:file:bg-slate-800 dark:file:text-blue-300"
                    >

                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                        Format CSV, maksimal 10 MB. Pemisah titik koma atau koma akan dideteksi otomatis.
                    </p>
                </div>

                <div class="rounded-lg border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-900 dark:border-blue-900 dark:bg-blue-950 dark:text-blue-200">
                    <p class="font-semibold">Sebelum melakukan import</p>
                    <ul class="mt-1 list-inside list-disc">
                        <li>Kolom No diabaikan.</li>
                        <li>Kode cabang dicocokkan dengan referensi Nama dan wilayah Mekaar.</li>
                        <li>Tagging dan IMEI yang sudah ada akan dilewati.</li>
                        <li>Baris duplikat atau tidak valid dilewati; baris valid lainnya tetap diimpor.</li>
                        <li>Jika struktur CSV salah atau terjadi kegagalan transaksi database, import dibatalkan untuk menjaga konsistensi data.</li>
                    </ul>
                </div>

                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#225199] px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#193f7a]"
                >
                    Import Data HP
                </button>
            </form>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900 sm:p-6">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
                Download template
            </h2>

            <p class="mt-1 text-sm leading-6 text-slate-500 dark:text-slate-400">
                Gunakan template kosong ini agar nama kolom dan urutan data sesuai dengan importer AMS.
            </p>

            <a
                href="{{ route('assets.import.template') }}"
                class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-lg border border-[#225199] px-4 py-3 text-sm font-semibold text-[#225199] transition hover:bg-blue-50 dark:border-blue-700 dark:text-blue-300 dark:hover:bg-slate-800"
            >
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <path d="m7 10 5 5 5-5"/>
                    <path d="M12 15V3"/>
                </svg>
                Download Template CSV
            </a>

            <div class="mt-6 border-t border-slate-100 pt-5 dark:border-slate-700">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">
                    Aturan pengolahan data
                </h3>

                <ul class="mt-2 space-y-2 text-sm leading-6 text-slate-600 dark:text-slate-300">
                    <li><strong>Area, Unit, InitialMekaar:</strong> disesuaikan memakai kode cabang di file referensi.</li>
                    <li><strong>MSISDN dan IMEI:</strong> disimpan sebagai teks agar angka tidak berubah.</li>
                    <li><strong>No Tagging:</strong> dipakai sebagai kode aset.</li>
                    <li><strong>Keterangan:</strong> 7 karakter terakhir dibaca sebagai bulan dan tahun sewa; tanggal disimpan sebagai hari pertama bulan tersebut.</li>
                    <li><strong>Real lokasi HP:</strong> dicocokkan dengan referensi cabang untuk menentukan lokasi aset.</li>
                    <li><strong>Vendor:</strong> menggunakan vendor yang ada atau dibuat jika belum tersedia.</li>
                </ul>
            </div>
        </section>
    </div>

</div>
@endsection
