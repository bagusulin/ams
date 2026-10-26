<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetHistory;
use App\Models\Location;
use App\Models\Vendor;
use App\Support\Assets\AssetAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssetCsvImportController extends Controller
{
    private const CSV_HEADERS = [
        'Area',
        'kodecabang',
        'Unit',
        'UserID',
        'MSISDN',
        'IMEI',
        'InitialMekaar',
        'No Tagging',
        'Keterangan',
        'Vendor',
        'LokasiHP (CBG/Mekaar)',
        'KeteranganTambahan',
        'Real lokasi HP',
        'Jenis HP',
    ];

    private const REQUIRED_HEADERS = [
        'area',
        'kodecabang',
        'unit',
        'userid',
        'msisdn',
        'imei',
        'initialmekaar',
        'notagging',
        'keterangan',
        'vendor',
        'lokasihpcbgmekaar',
        'keterangantambahan',
        'reallokasihp',
        'jenishp',
    ];

    public function index(): View
    {
        Gate::authorize('assets.import');

        abort_unless(
            AssetAccess::canManageCategory(Auth::user(), 'HPN'),
            403
        );

        return view('assets.import');
    }

    public function template(): StreamedResponse
    {
        Gate::authorize('assets.import');

        abort_unless(
            AssetAccess::canManageCategory(Auth::user(), 'HPN'),
            403
        );

        return response()->streamDownload(function () {
            $output = fopen('php://output', 'w');

            // BOM membantu Excel membaca UTF-8 dengan benar.
            fwrite($output, "\xEF\xBB\xBF");

            // Delimiter template sama seperti file contoh: titik koma.
            fputcsv($output, self::CSV_HEADERS, ';', '"', '');

            fclose($output);
        }, 'template_import_hp_ams.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('assets.import');

        $user = Auth::user();

        abort_unless($user, 403);

        // Import ini khusus HP (kode kategori HPN).
        abort_unless(
            AssetAccess::canManageCategory($user, 'HPN'),
            403
        );

        $validated = $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:10240',
            ],
        ]);

        $handle = fopen($validated['file']->getRealPath(), 'rb');

        if ($handle === false) {
            return back()->withErrors([
                'file' => 'File CSV tidak dapat dibaca.',
            ]);
        }

        $firstLine = fgets($handle);

        if ($firstLine === false) {
            fclose($handle);

            return back()->withErrors([
                'file' => 'File CSV kosong.',
            ]);
        }

        $delimiter = substr_count($firstLine, ';') >= substr_count($firstLine, ',')
            ? ';'
            : ',';

        rewind($handle);

        $rawHeaders = fgetcsv($handle, null, $delimiter, '"', '');

        if ($rawHeaders === false) {
            fclose($handle);

            return back()->withErrors([
                'file' => 'Header CSV tidak dapat dibaca.',
            ]);
        }

        $headers = array_map(
            fn ($header) => $this->normalizeHeader((string) $header),
            $rawHeaders
        );

        $missingHeaders = array_values(array_diff(
            self::REQUIRED_HEADERS,
            $headers
        ));

        if ($missingHeaders !== []) {
            fclose($handle);

            return back()->withErrors([
                'file' => 'Header CSV belum sesuai template. Kolom yang tidak ditemukan: '
                    . implode(', ', $missingHeaders)
                    . '. Gunakan template Import HP AMS yang disediakan.',
            ]);
        }

        $records = [];
        $errors = [];
        $normalizationWarnings = [];
        $seenTags = [];
        $seenImeis = [];
        $rowNumber = 1;
        $branchMappings = config('mekaar_branches', []);

        while (($values = fgetcsv($handle, null, $delimiter, '"', '')) !== false) {
            $rowNumber++;

            if (! array_filter(
                $values,
                fn ($value) => trim((string) $value) !== ''
            )) {
                continue;
            }

            $row = [];

            foreach ($headers as $index => $key) {
                if ($key !== '') {
                    $row[$key] = trim((string) ($values[$index] ?? ''));
                }
            }

            $requiredValues = [
                'kodecabang' => 'kodecabang',
                'userid' => 'UserID',
                'msisdn' => 'MSISDN',
                'imei' => 'IMEI',
                'notagging' => 'No Tagging',
                'keterangan' => 'Keterangan',
                'vendor' => 'Vendor',
                'reallokasihp' => 'Real lokasi HP',
                'jenishp' => 'Jenis HP',
            ];

            foreach ($requiredValues as $key => $label) {
                if (($row[$key] ?? '') === '') {
                    $errors[] = "Baris {$rowNumber}: kolom {$label} wajib diisi.";
                }
            }

            if (count($errors) > 0 && ! isset($row['kodecabang'])) {
                continue;
            }

            $branchCode = trim($row['kodecabang'] ?? '');
            $branchMapping = $branchMappings[$branchCode] ?? null;

            if (! is_array($branchMapping)) {
                $errors[] = "Baris {$rowNumber}: kodecabang '{$branchCode}' tidak ada di referensi Nama dan wilayah Mekaar.";
                continue;
            }

            $realLocation = trim($row['reallokasihp'] ?? '');
            $realLocationMapping = $this->findBranchByIdentifier(
                $realLocation,
                $branchMappings
            );

            if (! $realLocationMapping) {
                $errors[] = "Baris {$rowNumber}: Real lokasi HP '{$realLocation}' tidak ditemukan di referensi cabang.";
                continue;
            }

            $taggingNumber = trim($row['notagging'] ?? '');
            $imei = trim($row['imei'] ?? '');
            $description = trim($row['keterangan'] ?? '');
            $deviceType = trim($row['jenishp'] ?? '');
            $rentalStartDate = $this->extractRentalStartDate($description);

            if (! $rentalStartDate) {
                $suffix = substr($description, -7);
                $errors[] = "Baris {$rowNumber}: tanggal mulai sewa tidak dapat dibaca dari 7 karakter terakhir kolom Keterangan ('{$suffix}'). Contoh: PenggantianMTMNov2024.";
                continue;
            }

            if ($taggingNumber !== '') {
                if (isset($seenTags[$taggingNumber])) {
                    $errors[] = "Baris {$rowNumber}: No Tagging '{$taggingNumber}' duplikat dengan baris {$seenTags[$taggingNumber]} dalam file.";
                } else {
                    $seenTags[$taggingNumber] = $rowNumber;
                }
            }

            if ($imei !== '') {
                if (isset($seenImeis[$imei])) {
                    $errors[] = "Baris {$rowNumber}: IMEI '{$imei}' duplikat dengan baris {$seenImeis[$imei]} dalam file.";
                } else {
                    $seenImeis[$imei] = $rowNumber;
                }
            }

            $inputInitial = trim($row['initialmekaar'] ?? '');
            $canonicalInitial = trim($branchMapping['initial'] ?? '');

            if (
                $inputInitial !== ''
                && strcasecmp($inputInitial, $canonicalInitial) !== 0
            ) {
                $normalizationWarnings[$branchCode] =
                    "Kode cabang {$branchCode}: InitialMekaar pada CSV '{$inputInitial}' akan dinormalisasi menjadi '{$canonicalInitial}' sesuai file referensi.";
            }

            $records[] = [
                'row_number' => $rowNumber,
                'branch_mapping' => $branchMapping,
                'real_location_mapping' => $realLocationMapping,
                'asset_code' => $taggingNumber,
                'tagging_number' => $taggingNumber,
                'mekaar_user_id' => trim($row['userid'] ?? ''),
                'msisdn' => trim($row['msisdn'] ?? ''),
                'imei' => $imei,
                'rental_description' => $description,
                'vendor_name' => trim($row['vendor'] ?? ''),
                'phone_location_type' => trim($row['lokasihpcbgmekaar'] ?? ''),
                'additional_notes' => trim($row['keterangantambahan'] ?? ''),
                'real_location' => $realLocation,
                'device_type' => $deviceType,
                'rental_start_date' => $rentalStartDate,
            ];
        }

        fclose($handle);

        if ($records === []) {
            $errors[] = 'Tidak ada baris data yang dapat diimpor.';
        }

        // Periksa duplikasi terhadap aset aktif maupun aset yang diarsipkan.
        $tagNumbers = array_values(array_filter(
            array_column($records, 'asset_code'),
            fn ($value) => $value !== ''
        ));

        $imeis = array_values(array_filter(
            array_column($records, 'imei'),
            fn ($value) => $value !== ''
        ));

        $existingTags = $tagNumbers === []
            ? []
            : Asset::withTrashed()
                ->whereIn('asset_code', $tagNumbers)
                ->pluck('asset_code')
                ->all();

        $existingSerialNumbers = $imeis === []
            ? []
            : Asset::withTrashed()
                ->whereIn('serial_number', $imeis)
                ->pluck('serial_number')
                ->all();

        $existingImeis = $imeis === []
            ? []
            : Asset::withTrashed()
                ->whereIn('imei', $imeis)
                ->pluck('imei')
                ->all();

        $existingTags = array_flip($existingTags);
        $existingSerialNumbers = array_flip($existingSerialNumbers);
        $existingImeis = array_flip($existingImeis);

        foreach ($records as $record) {
            $row = $record['row_number'];

            if (isset($existingTags[$record['asset_code']])) {
                $errors[] = "Baris {$row}: No Tagging '{$record['asset_code']}' sudah ada di database.";
            }

            if (isset($existingSerialNumbers[$record['imei']])) {
                $errors[] = "Baris {$row}: IMEI '{$record['imei']}' sudah terdaftar sebagai serial number.";
            }

            if (isset($existingImeis[$record['imei']])) {
                $errors[] = "Baris {$row}: IMEI '{$record['imei']}' sudah ada di kolom IMEI database.";
            }
        }

        if ($errors !== []) {
            return back()->withErrors([
                'file' => "Import dibatalkan; tidak ada aset yang disimpan.\n"
                    . implode("\n", array_slice(array_unique($errors), 0, 30))
                    . (count($errors) > 30 ? "\nMasih ada error lainnya. Perbaiki file lalu impor ulang." : ''),
            ]);
        }

        $category = AssetCategory::query()
            ->where('code', 'HPN')
            ->where('is_active', true)
            ->first();

        if (! $category) {
            return back()->withErrors([
                'file' => 'Kategori Handphone dengan kode HPN belum tersedia atau tidak aktif. Periksa Master Kategori terlebih dahulu.',
            ]);
        }

        try {
            DB::transaction(function () use (
                $records,
                $category,
                $user
            ) {
                foreach ($records as $record) {
                    $branch = $record['branch_mapping'];
                    $realBranch = $record['real_location_mapping'];

                    // Lokasi fisik diambil dari kolom Real lokasi HP,
                    // lalu dicocokkan ke referensi cabang.
                    $location = Location::query()
                        ->where('code', $realBranch['initial'])
                        ->orWhere('code', $realBranch['branch_code'])
                        ->orWhere('name', $realBranch['branch'])
                        ->first();

                    if (! $location) {
                        $location = Location::create([
                            'name' => $realBranch['branch'],
                            'code' => $realBranch['initial'],
                            'address' => $realBranch['area'],
                            'is_active' => true,
                        ]);
                    }

                    $vendor = Vendor::firstOrCreate(
                        ['name' => $record['vendor_name']],
                        ['is_active' => true]
                    );

                    $deviceType = $record['device_type'];
                    $parts = preg_split(
                        '/\s+/',
                        $deviceType,
                        2,
                        PREG_SPLIT_NO_EMPTY
                    );

                    $brand = $parts[0] ?? null;
                    $model = $parts[1] ?? $deviceType;

                    $noteParts = [];

                    if ($record['rental_description'] !== '') {
                        $noteParts[] = 'Keterangan: ' . $record['rental_description'];
                    }

                    if ($record['additional_notes'] !== '') {
                        $noteParts[] = 'Keterangan tambahan: ' . $record['additional_notes'];
                    }

                    if ($record['phone_location_type'] !== '') {
                        $noteParts[] = 'Lokasi HP: ' . $record['phone_location_type'];
                    }

                    if ($record['real_location'] !== '') {
                        $noteParts[] = 'Real lokasi HP: ' . $record['real_location'];
                    }

                    $asset = Asset::create([
                        'asset_code' => $record['asset_code'],
                        'category_id' => $category->id,
                        'location_id' => $location->id,
                        'vendor_id' => $vendor->id,
                        'brand' => $brand,
                        'model' => $model,
                        'serial_number' => $record['imei'],
                        'status' => 'available',
                        'condition' => 'good',
                        'purchase_date' => null,
                        'rental_start_date' => $record['rental_start_date'],
                        'rental_end_date' => null,
                        'rental_cost' => null,
                        'notes' => implode("\n", $noteParts) ?: null,

                        'area_id' => $branch['area_id'],
                        'area_name' => $branch['area'],
                        'branch_code' => $branch['branch_code'],
                        'branch_unit' => $branch['branch'],
                        'mekaar_user_id' => $record['mekaar_user_id'],
                        'msisdn' => $record['msisdn'],
                        'imei' => $record['imei'],
                        'initial_mekaar' => $branch['initial'],
                        'tagging_number' => $record['tagging_number'],
                        'rental_description' => $record['rental_description'],
                        'phone_location_type' => $record['phone_location_type'] ?: null,
                        'additional_notes' => $record['additional_notes'] ?: null,
                        'real_location' => $record['real_location'] ?: null,
                        'device_type' => $deviceType,
                    ]);

                    AssetHistory::create([
                        'asset_id' => $asset->id,
                        'user_id' => $user->id,
                        'action' => 'imported',
                        'description' => 'Aset HP diimpor melalui file CSV.',
                        'old_values' => null,
                        'new_values' => [
                            'asset_code' => $asset->asset_code,
                            'imei' => $asset->imei,
                            'msisdn' => $asset->msisdn,
                            'branch_code' => $asset->branch_code,
                            'import_source' => 'CSV',
                        ],
                    ]);
                }
            });
        } catch (\Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'file' => 'Import dibatalkan karena ada kesalahan saat menyimpan ke database. Tidak ada baris yang disimpan. Periksa log Laravel untuk detail teknis.',
            ]);
        }

        return redirect()
            ->route('assets.import.index')
            ->with('import_result', [
                'imported' => count($records),
                'warnings' => array_values($normalizationWarnings),
            ]);
    }

    private function normalizeHeader(string $header): string
    {
        $header = preg_replace('/^\xEF\xBB\xBF/', '', $header);
        $header = mb_strtolower(trim($header));

        return preg_replace('/[^a-z0-9]+/u', '', $header);
    }

    private function findBranchByIdentifier(
        string $identifier,
        array $branches
    ): ?array {
        $needle = mb_strtolower(trim($identifier));

        if ($needle === '') {
            return null;
        }

        foreach ($branches as $branch) {
            foreach (['initial', 'branch_code', 'branch'] as $field) {
                $candidate = mb_strtolower(
                    trim((string) ($branch[$field] ?? ''))
                );

                if ($candidate !== '' && $candidate === $needle) {
                    return $branch;
                }
            }
        }

        return null;
    }

    private function extractRentalStartDate(string $description): ?string
    {
        // Tanggal hanya tersedia berupa 3 huruf bulan + 4 digit tahun.
        // Karena tanggal harinya tidak ada, digunakan hari pertama bulan.
        $suffix = substr(trim($description), -7);

        if (! preg_match('/^([A-Za-z]{3})(\d{4})$/', $suffix, $matches)) {
            return null;
        }

        $months = [
            'jan' => 1,
            'feb' => 2,
            'mar' => 3,
            'apr' => 4,
            'mei' => 5,
            'may' => 5,
            'jun' => 6,
            'jul' => 7,
            'agu' => 8,
            'aug' => 8,
            'sep' => 9,
            'okt' => 10,
            'oct' => 10,
            'nov' => 11,
            'des' => 12,
            'dec' => 12,
        ];

        $month = $months[mb_strtolower($matches[1])] ?? null;
        $year = (int) $matches[2];

        if (! $month || $year < 2000 || $year > 2100) {
            return null;
        }

        return sprintf('%04d-%02d-01', $year, $month);
    }
}
