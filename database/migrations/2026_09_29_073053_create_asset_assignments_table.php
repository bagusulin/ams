<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asset_id')
                ->constrained('assets')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('location_id')
                ->nullable()
                ->constrained('locations')
                ->nullOnDelete();

            $table->date('assigned_at');

            $table->date('returned_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_assignments');
    }
};