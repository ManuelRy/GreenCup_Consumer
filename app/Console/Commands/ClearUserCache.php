<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearUserCache extends Command
{
    protected $signature = 'cache:clear-user {user_id?}';
    protected $description = 'Clear cache for a specific user or all users';

    public function handle()
    {
        $userId = $this->argument('user_id');

        if ($userId) {
            Cache::forget("dashboard_activity_{$userId}");
            $this->info("Cleared cache for user {$userId}");
        } else {
            Cache::forget('stores_list');
            Cache::forget('photo_stats');
            $this->info('Cleared global caches');
        }

        return 0;
    }
}
