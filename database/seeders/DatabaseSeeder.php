<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Note: PopulateRanksTable is a migration, not a seeder
        // The ranks data is inserted when you run: php artisan migrate
        
        // Call our new sample data seeder
        $this->call(SampleReceiptDataSeeder::class);
        
        $this->command->info('🎉 Database seeded successfully!');
        $this->command->info('📱 You can now test receipt scanning with these codes:');
        $this->command->info('   • TEST123 (easy test)');
        $this->command->info('   • QUICK99 (quick test)'); 
        $this->command->info('   • GBC001 (coffee shop)');
        $this->command->info('   • ECO123 (market)');
        $this->command->info('   • BAM456 (bamboo cafe)');
        $this->command->info('   • demo123 (frontend demo - no backend needed)');
    }
}