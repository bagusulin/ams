
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // User ID boleh kosong untuk assignment berbasis lokasi cabang.
        // Tetap pertahankan tipe BIGINT UNSIGNED dan foreign key yang ada.
        DB::statement(
            'ALTER TABLE `asset_assignments`
             MODIFY `user_id` BIGINT UNSIGNED NULL'
        );
    }

    public function down(): void
    {
        // Jangan kembali ke NOT NULL jika masih ada assignment
        // yang memang tidak ditautkan ke user AMS.
        if (
            DB::table('asset_assignments')
                ->whereNull('user_id')
                ->exists()
        ) {
            throw new \RuntimeException(
                'Tidak dapat membatalkan migration: masih ada assignment dengan user_id NULL.'
            );
        }

        DB::statement(
            'ALTER TABLE `asset_assignments`
             MODIFY `user_id` BIGINT UNSIGNED NOT NULL'
        );
    }
};
