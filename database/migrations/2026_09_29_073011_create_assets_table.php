<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();

            $table->string('asset_code')->unique();

            $table->foreignId('category_id')
                ->constrained('asset_categories')
                ->restrictOnDelete();

            $table->foreignId('location_id')
                ->nullable()
                ->constrained('locations')
                ->nullOnDelete();

            $table->foreignId('vendor_id')
                ->nullable()
                ->constrained('vendors')
                ->nullOnDelete();

            $table->string('brand')->nullable();
            $table->string('model')->nullable();

            $table->string('serial_number')
                ->nullable()
                ->unique();

            $table->string('status')->default('available');

            $table->string('condition')->default('good');

            $table->date('purchase_date')->nullable();

            $table->date('rental_start_date')->nullable();
            $table->date('rental_end_date')->nullable();

            $table->decimal('rental_cost', 15, 2)
                ->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};