<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddTestScores extends Command
{
    protected $signature = 'test:add-scores';
    protected $description = 'Add test scores to sellers for ranking demonstration';

    public function handle()
    {
        $this->info('🔧 Adding test scores to sellers...');

        // Get all sellers
        $sellers = DB::table('sellers')->select('id', 'business_name')->get();

        if ($sellers->count() < 3) {
            $this->error('❌ Need at least 3 sellers to add test scores. Run: php artisan db:seed');
            return 1;
        }

        // Define test scores for different rank levels
        $testScores = [
            2500, // Platinum
            1200, // Gold
            750,  // Silver
            150,  // Bronze
            50,   // Standard
        ];

        $updated = 0;
        foreach ($sellers as $index => $seller) {
            if ($index >= count($testScores)) break;

            $score = $testScores[$index];

            // Update seller's total_points
            DB::table('sellers')
                ->where('id', $seller->id)
                ->update(['total_points' => $score]);

            // Create a point transaction record
            DB::table('point_transactions')->insert([
                'seller_id' => $seller->id,
                'consumer_id' => 1, // Assuming we have consumer ID 1
                'points' => $score,
                'units_scanned' => 1, // Add required field
                'type' => 'earn',
                'description' => 'Test score for ranking demonstration',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $rankText = $this->getRankText($score);
            $this->info("✅ {$seller->business_name}: {$score} points ({$rankText})");
            $updated++;
        }

        $this->info("\n🎉 Updated {$updated} sellers with test scores!");
        $this->info("🧪 You can now test the ranking system on the map page!");

        return 0;
    }

    private function getRankText($points)
    {
        $numPoints = floatval($points);
        if ($numPoints >= 2000) return 'Platinum';
        if ($numPoints >= 1000) return 'Gold';
        if ($numPoints >= 500) return 'Silver';
        if ($numPoints >= 100) return 'Bronze';
        return 'Standard';
    }
}
