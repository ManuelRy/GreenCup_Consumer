<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ConsumerPoint;
use App\Models\PointTransaction;
use Illuminate\Support\Facades\DB;

class FixNegativePoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'points:fix-negative {--dry-run : Show what would be fixed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix consumers with negative points by recalculating from transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        } else {
            $this->warn('⚠️  LIVE MODE - Database will be updated');
        }

        $this->info('Checking for consumers with negative points...');

        // Get all consumer_points with negative coins
        $negativePoints = ConsumerPoint::where('coins', '<', 0)->get();

        if ($negativePoints->isEmpty()) {
            $this->info('✅ No consumers with negative points found!');
            return Command::SUCCESS;
        }

        $this->warn("Found {$negativePoints->count()} consumer-seller records with negative points");

        $fixed = 0;
        $errors = 0;

        foreach ($negativePoints as $cp) {
            try {
                $this->info("\n📊 Processing Consumer ID: {$cp->consumer_id}, Seller ID: {$cp->seller_id}");
                $this->line("   Current: Earned={$cp->earned}, Spent={$cp->spent}, Coins={$cp->coins}");

                // Recalculate from transactions
                $earned = PointTransaction::where('consumer_id', $cp->consumer_id)
                    ->where('seller_id', $cp->seller_id)
                    ->where('type', 'earn')
                    ->sum('points') ?? 0;

                $spent = PointTransaction::where('consumer_id', $cp->consumer_id)
                    ->where('seller_id', $cp->seller_id)
                    ->where('type', 'spend')
                    ->sum('points') ?? 0;

                $rejected = PointTransaction::where('consumer_id', $cp->consumer_id)
                    ->where('seller_id', $cp->seller_id)
                    ->where('type', 'rejected')
                    ->sum('points') ?? 0;

                $refunded = PointTransaction::where('consumer_id', $cp->consumer_id)
                    ->where('seller_id', $cp->seller_id)
                    ->where('type', 'refund')
                    ->sum('points') ?? 0;

                // Calculate correctly: (earned - rejected) - (spent - refunded)
                $netEarned = $earned - $rejected;
                $netSpent = $spent - $refunded;
                $correctCoins = $netEarned - $netSpent;

                // If still negative, set to 0 (means more was spent than earned - data inconsistency)
                if ($correctCoins < 0) {
                    $this->error("   ❌ Transaction mismatch detected!");
                    $this->line("      Earned: {$earned}, Rejected: {$rejected}, Net Earned: {$netEarned}");
                    $this->line("      Spent: {$spent}, Refunded: {$refunded}, Net Spent: {$netSpent}");
                    $this->warn("   Setting coins to 0 to prevent negative balance");
                    $correctCoins = 0;

                    // Adjust values to balance
                    $netSpent = $netEarned;
                }

                $this->info("   Recalculated: NetEarned={$netEarned}, NetSpent={$netSpent}, Coins={$correctCoins}");

                if (!$dryRun) {
                    $cp->update([
                        'earned' => $netEarned,
                        'spent' => $netSpent,
                        'coins' => $correctCoins,
                    ]);
                    $this->info("   ✅ Fixed!");
                }

                $fixed++;

            } catch (\Exception $e) {
                $this->error("   ❌ Error: {$e->getMessage()}");
                $errors++;
            }
        }

        $this->newLine();
        if ($dryRun) {
            $this->info("🔍 DRY RUN COMPLETE");
            $this->info("Would fix: {$fixed} records");
        } else {
            $this->info("✅ FIXED: {$fixed} records");
        }

        if ($errors > 0) {
            $this->error("❌ ERRORS: {$errors} records");
        }

        // Show summary of consumers with issues
        $this->newLine();
        $this->info('📈 Checking global consumer balances...');

        $consumers = ConsumerPoint::select('consumer_id')
            ->groupBy('consumer_id')
            ->get();

        foreach ($consumers as $consumer) {
            $totalEarned = PointTransaction::where('consumer_id', $consumer->consumer_id)
                ->where('type', 'earn')
                ->sum('points') ?? 0;

            $totalSpent = PointTransaction::where('consumer_id', $consumer->consumer_id)
                ->where('type', 'spend')
                ->sum('points') ?? 0;

            $totalRejected = PointTransaction::where('consumer_id', $consumer->consumer_id)
                ->where('type', 'rejected')
                ->sum('points') ?? 0;

            $totalRefunded = PointTransaction::where('consumer_id', $consumer->consumer_id)
                ->where('type', 'refund')
                ->sum('points') ?? 0;

            $netEarned = $totalEarned - $totalRejected;
            $netSpent = $totalSpent - $totalRefunded;
            $balance = $netEarned - $netSpent;

            if ($balance < 0) {
                $this->error("Consumer {$consumer->consumer_id}: Global balance is negative ({$balance})");
                $this->line("  Earned: {$totalEarned}, Rejected: {$totalRejected}, Net: {$netEarned}");
                $this->line("  Spent: {$totalSpent}, Refunded: {$totalRefunded}, Net: {$netSpent}");
            }
        }

        return Command::SUCCESS;
    }
}
