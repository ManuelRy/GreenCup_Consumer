<?php
// database/migrations/2025_07_20_000007_create_seller_locations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerLocationsTable extends Migration
{
    public function up(): void
    {
        Schema::create('seller_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('address');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->boolean('is_primary')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_locations');
    }
}
