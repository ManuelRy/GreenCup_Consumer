<?php
// database/migrations/2025_07_20_000005_create_seller_rank_history_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ← Rename this class (added the “s” on Histories)
class CreateSellerRankHistoriesTable extends Migration
{
    public function up(): void
    {
        Schema::create('seller_rank_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('rank_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->timestamp('achieved_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_rank_history');
    }
}
