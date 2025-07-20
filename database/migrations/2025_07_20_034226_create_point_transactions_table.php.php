<?php
// Replace the entire content of: 2025_07_20_034226_create_point_transactions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePointTransactionsTable extends Migration
{
    public function up(): void
    {
        // Check if table already exists before creating
        if (!Schema::hasTable('point_transactions')) {
            Schema::create('point_transactions', function (Blueprint $table) {
                $table->id();
                
                // Consumer reference with explicit foreign key
                $table->unsignedBigInteger('consumer_id');
                $table->foreign('consumer_id')
                      ->references('id')
                      ->on('consumers')
                      ->onDelete('cascade');
                
                // Seller reference with explicit foreign key  
                $table->unsignedBigInteger('seller_id');
                $table->foreign('seller_id')
                      ->references('id')
                      ->on('sellers')
                      ->onDelete('cascade');
                
                // QR Code reference with explicit foreign key
                $table->unsignedBigInteger('qr_code_id');
                $table->foreign('qr_code_id')
                      ->references('id')
                      ->on('qr_codes')
                      ->onDelete('cascade');
                
                // Transaction data
                $table->integer('units_scanned');
                $table->integer('points');
                $table->enum('type', ['earn', 'spend'])->default('earn');
                $table->string('description')->nullable();
                $table->timestamp('scanned_at')->useCurrent();
                $table->timestamps();
            });
        }
        // If table exists, we skip creation and let migration pass
    }

    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
}