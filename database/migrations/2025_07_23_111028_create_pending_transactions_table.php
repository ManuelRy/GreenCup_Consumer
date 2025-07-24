<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePendingTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pending_transactions', function (Blueprint $table) {
            $table->id();
            
            // Receipt identification
            $table->string('receipt_code', 50)->unique();
            
            // Store reference
            $table->foreignId('seller_id')
                  ->constrained('sellers')
                  ->cascadeOnDelete();
            
            // Transaction data
            $table->json('items'); // Array of purchased items with details
            $table->integer('total_points')->default(0);
            $table->integer('total_quantity')->default(1);
            
            // Status tracking
            $table->enum('status', ['pending', 'claimed', 'expired'])
                  ->default('pending');
            
            // Timing
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('claimed_at')->nullable();
            
            // Consumer tracking (when claimed)
            $table->foreignId('claimed_by_consumer_id')
                  ->nullable()
                  ->constrained('consumers')
                  ->nullOnDelete();
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('receipt_code');
            $table->index('seller_id');
            $table->index('status');
            $table->index('expires_at');
            $table->index(['seller_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_transactions');
    }
}