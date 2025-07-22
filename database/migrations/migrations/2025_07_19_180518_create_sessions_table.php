<?php
// Replace the content of: 2025_07_19_180518_create_sessions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if table already exists before creating (since Laravel default creates one)
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id', 191)->primary();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
                $table->timestamps();
            });
        } else {
            // If sessions table exists, just add missing columns
            Schema::table('sessions', function (Blueprint $table) {
                if (!Schema::hasColumn('sessions', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->index()->after('id');
                }
                if (!Schema::hasColumn('sessions', 'created_at')) {
                    $table->timestamps();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
}