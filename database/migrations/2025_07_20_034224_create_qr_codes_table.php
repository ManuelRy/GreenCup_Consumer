<?php
// database/migrations/2025_07_20_034224_create_qr_codes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQrCodesTable extends Migration
{
    public function up(): void
    {
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            
            // Seller reference (nullable for consumer QR codes)
            $table->foreignId('seller_id')
                  ->nullable()
                  ->constrained()
                  ->cascadeOnDelete();
            
            // Item reference (nullable for consumer QR codes)
            $table->foreignId('item_id')
                  ->nullable()
                  ->constrained('items')
                  ->cascadeOnDelete();
            
            // Consumer reference (nullable for seller QR codes)
            $table->foreignId('consumer_id')
                  ->nullable()
                  ->constrained('consumers')
                  ->cascadeOnDelete();
            
            // QR code type to distinguish purposes
            $table->enum('type', ['seller_item', 'consumer_profile'])
                  ->default('seller_item');
            
            $table->string('code')->unique();
            $table->boolean('active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
}