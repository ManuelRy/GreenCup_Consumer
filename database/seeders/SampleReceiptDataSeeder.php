<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SampleReceiptDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, ensure we have some sellers
        $this->seedSellers();
        
        // Then create sample pending transactions (receipts)
        $this->seedPendingTransactions();
        
        // Add some items to the items table if needed
        $this->seedItems();
    }
    
    /**
     * Seed sample sellers
     */
    private function seedSellers(): void
    {
        $sellers = [
            [
                'business_name' => 'Green Bean Coffee',
                'email' => 'contact@greenbean.com',
                'description' => 'Organic coffee and sustainable practices',
                'working_hours' => '6:00 AM - 8:00 PM',
                'address' => '123 Sisowath Quay, Phnom Penh',
                'latitude' => 11.5625,
                'longitude' => 104.9309,
                'phone' => '+855 12 345 678',
                'password' => Hash::make('password123'),
                'is_active' => true,
                'total_points' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_name' => 'Eco Market Fresh',
                'email' => 'hello@ecomarket.kh',
                'description' => 'Fresh organic produce and zero-waste shopping',
                'working_hours' => '7:00 AM - 9:00 PM',
                'address' => '456 Street 240, Phnom Penh',
                'latitude' => 11.5564,
                'longitude' => 104.9282,
                'phone' => '+855 23 456 789',
                'password' => Hash::make('password123'),
                'is_active' => true,
                'total_points' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_name' => 'Bamboo Cafe',
                'email' => 'info@bamboocafe.com',
                'description' => 'Sustainable dining with bamboo utensils',
                'working_hours' => '8:00 AM - 10:00 PM',
                'address' => '789 Norodom Blvd, Phnom Penh',
                'latitude' => 11.5500,
                'longitude' => 104.9200,
                'phone' => '+855 17 567 890',
                'password' => Hash::make('password123'),
                'is_active' => true,
                'total_points' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($sellers as $seller) {
            // Only insert if doesn't exist
            if (!DB::table('sellers')->where('email', $seller['email'])->exists()) {
                DB::table('sellers')->insert($seller);
            }
        }
    }
    
    /**
     * Seed sample pending transactions (receipts)
     */
    private function seedPendingTransactions(): void
    {
        // Get seller IDs
        $sellers = DB::table('sellers')->pluck('id', 'business_name');
        
        if ($sellers->isEmpty()) {
            $this->command->error('No sellers found. Please run sellers seeder first.');
            return;
        }
        
        $pendingTransactions = [
            // Green Bean Coffee receipts
            [
                'receipt_code' => 'GBC001',
                'seller_id' => $sellers->get('Green Bean Coffee'),
                'items' => json_encode([
                    [
                        'name' => 'Organic Coffee',
                        'quantity' => 2,
                        'points_per_unit' => 2
                    ],
                    [
                        'name' => 'Reusable Cup',
                        'quantity' => 1,
                        'points_per_unit' => 5
                    ]
                ]),
                'total_points' => 9, // (2*2) + (1*5)
                'total_quantity' => 3,
                'status' => 'pending',
                'expires_at' => now()->addDays(7),
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],
            [
                'receipt_code' => 'GBC002',
                'seller_id' => $sellers->get('Green Bean Coffee'),
                'items' => json_encode([
                    [
                        'name' => 'Latte',
                        'quantity' => 1,
                        'points_per_unit' => 3
                    ],
                    [
                        'name' => 'Eco Straw',
                        'quantity' => 1,
                        'points_per_unit' => 2
                    ]
                ]),
                'total_points' => 5,
                'total_quantity' => 2,
                'status' => 'pending',
                'expires_at' => now()->addDays(5),
                'created_at' => now()->subHours(4),
                'updated_at' => now()->subHours(4),
            ],
            
            // Eco Market receipts
            [
                'receipt_code' => 'ECO123',
                'seller_id' => $sellers->get('Eco Market Fresh'),
                'items' => json_encode([
                    [
                        'name' => 'Organic Vegetables',
                        'quantity' => 1,
                        'points_per_unit' => 4
                    ],
                    [
                        'name' => 'Bamboo Bag',
                        'quantity' => 1,
                        'points_per_unit' => 3
                    ],
                    [
                        'name' => 'Glass Container',
                        'quantity' => 2,
                        'points_per_unit' => 2
                    ]
                ]),
                'total_points' => 11, // 4 + 3 + (2*2)
                'total_quantity' => 4,
                'status' => 'pending',
                'expires_at' => now()->addDays(3),
                'created_at' => now()->subHours(1),
                'updated_at' => now()->subHours(1),
            ],
            
            // Bamboo Cafe receipts
            [
                'receipt_code' => 'BAM456',
                'seller_id' => $sellers->get('Bamboo Cafe'),
                'items' => json_encode([
                    [
                        'name' => 'Green Smoothie',
                        'quantity' => 1,
                        'points_per_unit' => 3
                    ],
                    [
                        'name' => 'Bamboo Utensil Set',
                        'quantity' => 1,
                        'points_per_unit' => 6
                    ]
                ]),
                'total_points' => 9,
                'total_quantity' => 2,
                'status' => 'pending',
                'expires_at' => now()->addDays(6),
                'created_at' => now()->subMinutes(30),
                'updated_at' => now()->subMinutes(30),
            ],
            
            // Test codes for easy testing
            [
                'receipt_code' => 'TEST123',
                'seller_id' => $sellers->get('Green Bean Coffee'),
                'items' => json_encode([
                    [
                        'name' => 'Test Coffee',
                        'quantity' => 1,
                        'points_per_unit' => 2
                    ],
                    [
                        'name' => 'Test Muffin',
                        'quantity' => 1,
                        'points_per_unit' => 1
                    ]
                ]),
                'total_points' => 3,
                'total_quantity' => 2,
                'status' => 'pending',
                'expires_at' => now()->addDays(30), // Long expiry for testing
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'receipt_code' => 'QUICK99',
                'seller_id' => $sellers->get('Eco Market Fresh'),
                'items' => json_encode([
                    [
                        'name' => 'Quick Item',
                        'quantity' => 1,
                        'points_per_unit' => 1
                    ]
                ]),
                'total_points' => 1,
                'total_quantity' => 1,
                'status' => 'pending',
                'expires_at' => now()->addDays(30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Some expired ones for testing
            [
                'receipt_code' => 'OLD001',
                'seller_id' => $sellers->get('Bamboo Cafe'),
                'items' => json_encode([
                    [
                        'name' => 'Expired Item',
                        'quantity' => 1,
                        'points_per_unit' => 2
                    ]
                ]),
                'total_points' => 2,
                'total_quantity' => 1,
                'status' => 'expired',
                'expires_at' => now()->subDays(1), // Already expired
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(1),
            ]
        ];

        foreach ($pendingTransactions as $transaction) {
            // Only insert if receipt code doesn't exist
            if (!DB::table('pending_transactions')->where('receipt_code', $transaction['receipt_code'])->exists()) {
                DB::table('pending_transactions')->insert($transaction);
            }
        }
        
        $this->command->info('Sample pending transactions created!');
        $this->command->info('Test with these codes: TEST123, QUICK99, GBC001, ECO123, BAM456');
    }
    
    /**
     * Seed basic items if table is empty
     */
    private function seedItems(): void
    {
        if (DB::table('items')->count() > 0) {
            return; // Items already exist
        }
        
        $items = [
            ['name' => 'Coffee', 'points_per_unit' => 2],
            ['name' => 'Reusable Cup', 'points_per_unit' => 5],
            ['name' => 'Organic Vegetables', 'points_per_unit' => 4],
            ['name' => 'Bamboo Utensils', 'points_per_unit' => 6],
            ['name' => 'Glass Container', 'points_per_unit' => 3],
            ['name' => 'Eco Bag', 'points_per_unit' => 3],
            ['name' => 'Metal Straw', 'points_per_unit' => 2],
            ['name' => 'Smoothie', 'points_per_unit' => 3],
        ];
        
        foreach ($items as $item) {
            $item['created_at'] = now();
            $item['updated_at'] = now();
            DB::table('items')->insert($item);
        }
    }
}