<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_assignments', function (Blueprint $table) {
            $table->index(
                ['asset_id', 'returned_at'],
                'asset_assignments_asset_returned_index'
            );

            $table->index(
                ['user_id', 'returned_at'],
                'asset_assignments_user_returned_index'
            );

            $table->index(
                ['assigned_at', 'returned_at'],
                'asset_assignments_dates_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('asset_assignments', function (Blueprint $table) {
            $table->dropIndex('asset_assignments_asset_returned_index');
            $table->dropIndex('asset_assignments_user_returned_index');
            $table->dropIndex('asset_assignments_dates_index');
        });
    }
};