<?php
// database/migrations/xxxx_xx_xx_xxxxxx_populate_ranks_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class PopulateRanksTable extends Migration
{
    public function up(): void
    {
        // Clear existing ranks if any
        DB::table('ranks')->truncate();
        
        // Insert your rank structure
        DB::table('ranks')->insert([
            [
                'name' => 'Standard',
                'min_points' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bronze',
                'min_points' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Silver',
                'min_points' => 500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gold',
                'min_points' => 1000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Platinum',
                'min_points' => 2000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('ranks')->truncate();
    }
}