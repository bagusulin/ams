<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_categories', function (Blueprint $table) {
            $table->unsignedTinyInteger('default_rental_years')
                ->nullable()
                ->after('code');
        });

        // Backfill kategori yang sudah ada.
        // NBK dan LPT sama-sama mewakili Notebook/Laptop.
        $periods = [
            'NBK' => 3,
            'LPT' => 3,
            'HPN' => 2,
            'MTR' => 3,
            'MBL' => 4,
            'CTV' => 2,
            'PRN' => 2,
            'SCN' => 3,
        ];

        foreach ($periods as $code => $years) {
            DB::table('asset_categories')
                ->where('code', $code)
                ->update([
                    'default_rental_years' => $years,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('asset_categories', function (Blueprint $table) {
            $table->dropColumn('default_rental_years');
        });
    }
};