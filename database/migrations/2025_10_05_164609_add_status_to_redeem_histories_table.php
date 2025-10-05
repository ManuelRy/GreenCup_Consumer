<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('redeem_histories', function (Blueprint $table) {
            // Add status column: 'pending', 'approved', 'rejected'
            $table->string('status', 20)->default('pending')->after('is_redeemed');

            // Add approved_at and rejected_at timestamps
            $table->timestamp('approved_at')->nullable()->after('status');
            $table->timestamp('rejected_at')->nullable()->after('approved_at');

            // Add rejection reason (optional)
            $table->text('rejection_reason')->nullable()->after('rejected_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('redeem_histories', function (Blueprint $table) {
            $table->dropColumn(['status', 'approved_at', 'rejected_at', 'rejection_reason']);
        });
    }
};
