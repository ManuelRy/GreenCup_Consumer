<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddPerformanceIndexesToSellers extends Migration
{
    /**
     * Run the migrations - Add indexes for faster queries
     */
    public function up(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            // Add composite index for active stores with location
            $table->index(['is_active', 'latitude', 'longitude'], 'idx_sellers_active_location');
            
            // Add index for points-based queries
            $table->index('total_points', 'idx_sellers_points');
            
            // Add composite index for active stores ordered by points
            $table->index(['is_active', 'total_points'], 'idx_sellers_active_points');
        });
        
        // Add any missing indexes to other tables
        if (Schema::hasTable('point_transactions')) {
            Schema::table('point_transactions', function (Blueprint $table) {
                // Check if indexes don't already exist
                $indexes = DB::select("SHOW INDEX FROM point_transactions");
                $existingIndexes = collect($indexes)->pluck('Key_name')->toArray();
                
                if (!in_array('idx_pt_seller_type', $existingIndexes)) {
                    $table->index(['seller_id', 'type'], 'idx_pt_seller_type');
                }
            });
        }
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropIndex('idx_sellers_active_location');
            $table->dropIndex('idx_sellers_points'); 
            $table->dropIndex('idx_sellers_active_points');
        });
        
        if (Schema::hasTable('point_transactions')) {
            Schema::table('point_transactions', function (Blueprint $table) {
                $table->dropIndex('idx_pt_seller_type');
            });
        }
    }
}