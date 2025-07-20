<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class QRTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test sellers
        $sellerId1 = DB::table('sellers')->insertGetId([
            'business_name' => 'Green Coffee Shop',
            'email' => 'greencoffee@example.com',
            'description' => 'Eco-friendly coffee shop serving organic, locally-sourced coffee and snacks.',
            'working_hours' => 'Mon-Fri: 7:00 AM - 8:00 PM, Sat-Sun: 8:00 AM - 9:00 PM',
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $sellerId2 = DB::table('sellers')->insertGetId([
            'business_name' => 'EcoMart Grocery',
            'email' => 'ecomart@example.com',
            'description' => 'Your neighborhood green grocery store with fresh organic produce.',
            'working_hours' => 'Daily: 6:00 AM - 10:00 PM',
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $sellerId3 = DB::table('sellers')->insertGetId([
            'business_name' => 'Sustainable Smoothies',
            'email' => 'smoothies@example.com',
            'description' => 'Fresh smoothies made with organic fruits and vegetables.',
            'working_hours' => 'Mon-Sat: 8:00 AM - 6:00 PM',
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Create seller locations
        DB::table('seller_locations')->insert([
            [
                'seller_id' => $sellerId1,
                'address' => '123 Green Street, Downtown',
                'latitude' => 40.7128,
                'longitude' => -74.0060,
                'is_primary' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'seller_id' => $sellerId2,
                'address' => '456 Eco Avenue, Midtown',
                'latitude' => 40.7589,
                'longitude' => -73.9851,
                'is_primary' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'seller_id' => $sellerId3,
                'address' => '789 Organic Boulevard, Uptown',
                'latitude' => 40.7831,
                'longitude' => -73.9712,
                'is_primary' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Create test items
        $itemId1 = DB::table('items')->insertGetId([
            'name' => 'Coffee Cup (Recyclable)',
            'points_per_unit' => 50,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $itemId2 = DB::table('items')->insertGetId([
            'name' => 'Reusable Shopping Bag',
            'points_per_unit' => 100,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $itemId3 = DB::table('items')->insertGetId([
            'name' => 'Organic Smoothie',
            'points_per_unit' => 75,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $itemId4 = DB::table('items')->insertGetId([
            'name' => 'Eco-friendly Sandwich',
            'points_per_unit' => 120,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $itemId5 = DB::table('items')->insertGetId([
            'name' => 'Green Tea',
            'points_per_unit' => 30,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Create QR codes
        $qrCodes = [
            [
                'seller_id' => $sellerId1,
                'item_id' => $itemId1,
                'code' => 'GREENCOFFEE001',
                'active' => true,
                'expires_at' => now()->addMonths(6),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'seller_id' => $sellerId1,
                'item_id' => $itemId4,
                'code' => 'GREENCOFFEE002',
                'active' => true,
                'expires_at' => now()->addMonths(6),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'seller_id' => $sellerId1,
                'item_id' => $itemId5,
                'code' => 'GREENCOFFEE003',
                'active' => true,
                'expires_at' => now()->addMonths(6),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'seller_id' => $sellerId2,
                'item_id' => $itemId2,
                'code' => 'ECOMART001',
                'active' => true,
                'expires_at' => now()->addMonths(6),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'seller_id' => $sellerId2,
                'item_id' => $itemId1,
                'code' => 'ECOMART002',
                'active' => true,
                'expires_at' => now()->addMonths(6),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'seller_id' => $sellerId3,
                'item_id' => $itemId3,
                'code' => 'SMOOTHIE001',
                'active' => true,
                'expires_at' => now()->addMonths(6),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'seller_id' => $sellerId3,
                'item_id' => $itemId1,
                'code' => 'SMOOTHIE002',
                'active' => true,
                'expires_at' => now()->addMonths(6),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('qr_codes')->insert($qrCodes);

        // Create test consumer
        $consumerId = DB::table('consumers')->insertGetId([
            'full_name' => 'Test Consumer',
            'email' => 'testconsumer@example.com',
            'phone_number' => '+1234567890',
            'gender' => 'other',
            'date_of_birth' => '1990-01-01',
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Create some test transactions for the consumer
        DB::table('point_transactions')->insert([
            [
                'consumer_id' => $consumerId,
                'seller_id' => $sellerId1,
                'qr_code_id' => 1,
                'units_scanned' => 1,
                'points' => 50,
                'type' => 'earn',
                'description' => 'Test transaction - Coffee Cup',
                'scanned_at' => now()->subDays(5),
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5)
            ],
            [
                'consumer_id' => $consumerId,
                'seller_id' => $sellerId2,
                'qr_code_id' => 4,
                'units_scanned' => 1,
                'points' => 100,
                'type' => 'earn',
                'description' => 'Test transaction - Shopping Bag',
                'scanned_at' => now()->subDays(3),
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3)
            ],
            [
                'consumer_id' => $consumerId,
                'seller_id' => $sellerId3,
                'qr_code_id' => 6,
                'units_scanned' => 1,
                'points' => 75,
                'type' => 'earn',
                'description' => 'Test transaction - Smoothie',
                'scanned_at' => now()->subDays(1),
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1)
            ]
        ]);

        $this->command->info('✅ QR Test Data created successfully!');
        $this->command->info('📱 Test QR Codes you can scan:');
        $this->command->info('   - GREENCOFFEE001 (Coffee Cup - 50 pts)');
        $this->command->info('   - ECOMART001 (Shopping Bag - 100 pts)');
        $this->command->info('   - SMOOTHIE001 (Smoothie - 75 pts)');
        $this->command->info('   - GREENCOFFEE002 (Sandwich - 120 pts)');
        $this->command->info('🧪 Test Consumer: testconsumer@example.com / password');
        $this->command->info('💰 Current points for test consumer: 225 pts');
    }
}