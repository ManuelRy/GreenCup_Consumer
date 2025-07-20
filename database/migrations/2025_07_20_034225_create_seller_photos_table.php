<?php
// database/migrations/2025_07_20_000006_create_seller_photos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerPhotosTable extends Migration
{
    public function up(): void
    {
        Schema::create('seller_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('url', 512);
            $table->string('caption')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_photos');
    }
}
