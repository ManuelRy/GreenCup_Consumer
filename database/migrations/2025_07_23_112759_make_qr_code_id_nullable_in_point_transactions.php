<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeQrCodeIdNullableInPointTransactions extends Migration
{
    /**
     * Run the migrations.
     * Simple column modification - no foreign keys to worry about!
     */
    public function up(): void
    {
        Schema::table('point_transactions', function (Blueprint $table) {
            // Make qr_code_id nullable (no foreign key constraints exist)
            $table->unsignedBigInteger('qr_code_id')->nullable()->change();
            
            // Add receipt_code reference for new system
            $table->string('receipt_code', 50)->nullable()->after('qr_code_id');
            $table->index('receipt_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('point_transactions', function (Blueprint $table) {
            // Remove the new column
            $table->dropIndex(['receipt_code']);
            $table->dropColumn('receipt_code');
            
            // Revert column back to non-nullable
            $table->unsignedBigInteger('qr_code_id')->nullable(false)->change();
        });
    }
}