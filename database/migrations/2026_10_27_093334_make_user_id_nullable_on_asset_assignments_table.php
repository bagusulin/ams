<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_assignments', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        if (DB::table('asset_assignments')
            ->whereNull('user_id')
            ->exists()) {
            throw new RuntimeException(
                'Migration tidak dapat dibatalkan karena terdapat assignment berbasis lokasi tanpa user AMS.'
            );
        }

        Schema::table('asset_assignments', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')
                ->nullable(false)
                ->change();
        });
    }
};