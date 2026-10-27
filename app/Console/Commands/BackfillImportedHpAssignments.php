<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetHistory;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillImportedHpAssignments extends Command
{
    protected $signature =
        'assets:backfill-imported-hp-assignments
         {--dry-run : Hanya menghitung data yang akan diproses}';

    protected $description =
        'Membuat assignment berbasis lokasi untuk HP CSV yang sudah diimpor.';

    public function handle(): int
    {
        $actor = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->orderBy('id')->first();

        if (! $actor) {
            $this->error('User Admin tidak ditemukan.');

            return self::FAILURE;
        }

        $query = Asset::query()
            ->whereHas('category', function ($categoryQuery) {
                $categoryQuery->where('code', 'HPN');
            })
            ->whereNotNull('real_location')
            ->where('real_location', '<>', '')
            ->whereNotNull('mekaar_user_id')
            ->where('mekaar_user_id', '<>', '')
            ->whereNotNull('location_id')
            ->where('status', 'available')
            ->whereDoesntHave('assignments', function ($assignmentQuery) {
                $assignmentQuery->whereNull('returned_at');
            });

        $total = (clone $query)->count();

        if ($this->option('dry-run')) {
            $this->info(
                "Dry run selesai. {$total} aset memenuhi syarat untuk backfill."
            );

            return self::SUCCESS;
        }

        $created = 0;

        $query->select('id')->orderBy('id')->chunkById(
            100,
            function ($rows) use ($actor, &$created) {
                foreach ($rows as $row) {
                    DB::transaction(function () use (
                        $row,
                        $actor,
                        &$created
                    ) {
                        $asset = Asset::query()
                            ->lockForUpdate()
                            ->find($row->id);

                        if (! $asset) {
                            return;
                        }

                        // Cek ulang untuk menghindari assignment ganda.
                        if (
                            $asset->status !== 'available'
                            || ! $asset->location_id
                            || $asset->assignments()
                                ->whereNull('returned_at')
                                ->exists()
                        ) {
                            return;
                        }

                        $assignment = AssetAssignment::create([
                            'asset_id' => $asset->id,
                            'user_id' => null,
                            'location_id' => $asset->location_id,
                            'assigned_at' => today()->toDateString(),
                            'returned_at' => null,
                            'notes' => implode("\n", [
                                'Assignment otomatis dari backfill HP CSV.',
                                'Real lokasi HP: ' . $asset->real_location,
                                'UserID Mekaar: ' . $asset->mekaar_user_id,
                                'MSISDN: ' . ($asset->msisdn ?? '-'),
                                'Tanggal mulai sewa: '
                                    . ($asset->rental_start_date?->format('Y-m-d') ?? '-'),
                            ]),
                        ]);

                        $asset->update([
                            'status' => 'assigned',
                        ]);

                        AssetHistory::create([
                            'asset_id' => $asset->id,
                            'user_id' => $actor->id,
                            'action' => 'assigned',
                            'description' =>
                                'Assignment otomatis ke lokasi '
                                . $asset->real_location
                                . ' melalui backfill HP CSV.',
                            'old_values' => [
                                'status' => 'available',
                            ],
                            'new_values' => [
                                'status' => 'assigned',
                                'assignment_id' => $assignment->id,
                                'location_id' => $asset->location_id,
                                'real_location' => $asset->real_location,
                                'mekaar_user_id' => $asset->mekaar_user_id,
                                'msisdn' => $asset->msisdn,
                                'import_source' => 'CSV backfill',
                            ],
                        ]);

                        $created++;
                    });
                }
            }
        );

        $this->info(
            "Backfill selesai. {$created} aset berhasil dibuatkan assignment."
        );

        return self::SUCCESS;
    }
}
