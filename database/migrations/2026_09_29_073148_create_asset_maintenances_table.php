<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asset_id')
                ->constrained('assets')
                ->cascadeOnDelete();

            $table->date('maintenance_date');

            $table->string('type');

            $table->text('description')->nullable();

            $table->text('action_taken')->nullable();

            $table->decimal('cost', 15, 2)->default(0);

            $table->string('status')->default('completed');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_maintenances');
    }
};