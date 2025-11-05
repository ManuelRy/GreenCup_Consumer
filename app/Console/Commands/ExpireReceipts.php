<?php

namespace App\Console\Commands;

use App\Models\PendingTransaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireReceipts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'receipts:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically expire receipts that have passed their expiration time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired receipts...');

        try {
            // Find all pending receipts that have expired
            $expiredCount = PendingTransaction::where('status', 'pending')
                ->where('expires_at', '<', Carbon::now())
                ->whereNotNull('expires_at')
                ->update(['status' => 'expired']);

            if ($expiredCount > 0) {
                $this->info("Successfully expired {$expiredCount} receipt(s)");
                Log::info("Expired {$expiredCount} receipt(s)", [
                    'command' => 'receipts:expire',
                    'timestamp' => Carbon::now()->toDateTimeString()
                ]);
            } else {
                $this->info('No receipts to expire');
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error expiring receipts: ' . $e->getMessage());
            Log::error('Receipt expiration error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Command::FAILURE;
        }
    }
}
