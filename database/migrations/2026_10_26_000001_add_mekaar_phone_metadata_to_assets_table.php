<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('area_id', 30)->nullable();
            $table->string('area_name', 120)->nullable();
            $table->string('branch_code', 30)->nullable()->index();
            $table->string('branch_unit', 180)->nullable();
            $table->string('mekaar_user_id', 100)->nullable()->index();
            $table->string('msisdn', 30)->nullable()->index();
            $table->string('imei', 50)->nullable()->index();
            $table->string('initial_mekaar', 100)->nullable();
            $table->string('tagging_number', 255)->nullable();
            $table->string('rental_description', 255)->nullable();
            $table->string('phone_location_type', 100)->nullable();
            $table->text('additional_notes')->nullable();
            $table->string('real_location', 180)->nullable();
            $table->string('device_type', 180)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropIndex('assets_branch_code_index');
            $table->dropIndex('assets_mekaar_user_id_index');
            $table->dropIndex('assets_msisdn_index');
            $table->dropIndex('assets_imei_index');

            $table->dropColumn([
                'area_id',
                'area_name',
                'branch_code',
                'branch_unit',
                'mekaar_user_id',
                'msisdn',
                'imei',
                'initial_mekaar',
                'tagging_number',
                'rental_description',
                'phone_location_type',
                'additional_notes',
                'real_location',
                'device_type',
            ]);
        });
    }
};
