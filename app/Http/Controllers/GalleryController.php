<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GalleryController extends Controller
{
    /**
     * Show what sellers are posting - Instagram/Facebook style feed
     */
    public function index()
    {
        $consumer = Auth::guard('consumer')->user();
        
        if (!$consumer) {
            return redirect()->route('login')
                ->with('error', 'Please login to view posts');
        }

        // Get all stores with photos
        $stores = DB::table('sellers')
            ->select([
                'id',
                'business_name',
                'description', 
                'address',
                'total_points'
            ])
            ->where('is_active', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        // Enrich each store with photos and ranking data
        $stores = $stores->map(function($store) {
            return $this->enrichStoreData($store);
        })->filter(function($store) {
            // Only show stores that have photos
            return count($store->photos) > 0;
        });

        return view('gallery.index', [
            'stores' => $stores,
            'consumer' => $consumer
        ]);
    }

    /**
     * Enrich store data with photos and ranking information
     */
    private function enrichStoreData($store)
    {
        // Get photos for this store
        $store->photos = $this->getStorePhotos($store->id);
        
        // Get total points given to consumers by this store
        $store->points_reward = $this->getStorePointsGivenToConsumers($store->id);
        
        // Calculate rank based on points given to consumers
        $store->rank_class = $this->getRankClass($store->points_reward);
        $store->rank_text = $this->getRankText($store->points_reward);
        $store->rank_icon = $this->getRankIcon($store->points_reward);
        
        return $store;
    }

    /**
     * Get photos for a store - supports multiple sources
     */
    private function getStorePhotos($storeId)
    {
        $photos = collect([]);
        
        try {
            // Method 1: Get from seller_photos table (if you create one)
            if (DB::getSchemaBuilder()->hasTable('seller_photos')) {
                $dbPhotos = DB::table('seller_photos')
                    ->where('seller_id', $storeId)
                    ->where('is_active', true)
                    ->orderBy('is_featured', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get(['url', 'caption']);
                
                foreach ($dbPhotos as $photo) {
                    $photos->push((object)[
                        'url' => $photo->url,
                        'caption' => $photo->caption ?? ''
                    ]);
                }
            }
            
            // Method 2: Get single photo from sellers table
            $seller = DB::table('sellers')
                ->where('id', $storeId)
                ->whereNotNull('photo_url')
                ->first(['photo_url', 'photo_caption']);
            
            if ($seller && $seller->photo_url) {
                $photos->push((object)[
                    'url' => $seller->photo_url,
                    'caption' => $seller->photo_caption ?? ''
                ]);
            }
            
            // Method 3: Generate demo photos if no real photos exist
            if ($photos->isEmpty()) {
                $photos = $this->generateDemoPhotos($storeId);
            }
            
        } catch (\Exception $e) {
            // Fallback to demo photos
            $photos = $this->generateDemoPhotos($storeId);
        }
        
        return $photos->toArray();
    }

    /**
     * Generate demo photos for stores (for demonstration)
     */
    private function generateDemoPhotos($storeId)
    {
        // Demo photos based on store ID
        $demoSets = [
            [
                'https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=800',
                'https://images.unsplash.com/photo-1559925393-8be0ec4767c8?w=800',
                'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=800',
                'https://images.unsplash.com/photo-1571091718767-18b5b1457add?w=800'
            ],
            [
                'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800',
                'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=800',
                'https://images.unsplash.com/photo-1556909075-f3e64d6761b4?w=800'
            ],
            [
                'https://images.unsplash.com/photo-1515003197210-e0cd71810b5f?w=800',
                'https://images.unsplash.com/photo-1571091655789-405eb7a3a3a8?w=800',
                'https://images.unsplash.com/photo-1548940740-204726a19be3?w=800',
                'https://images.unsplash.com/photo-1567027394268-a3cbb207fa8f?w=800',
                'https://images.unsplash.com/photo-1571091656363-dea7acb24f42?w=800'
            ],
            [
                'https://images.unsplash.com/photo-1593560704563-f176a2eb61db?w=800',
                'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=800'
            ]
        ];
        
        $photoSet = $demoSets[$storeId % count($demoSets)];
        
        return collect($photoSet)->map(function($url, $index) {
            return (object)[
                'url' => $url,
                'caption' => 'Beautiful store interior ' . ($index + 1)
            ];
        });
    }

    /**
     * Get total points given to consumers by this store
     */
    private function getStorePointsGivenToConsumers($storeId)
    {
        try {
            // Check if point_transactions table exists
            $exists = DB::select("SHOW TABLES LIKE 'point_transactions'");
            if (empty($exists)) {
                return rand(50, 2500); // Demo points for ranking demonstration
            }
            
            return DB::table('point_transactions')
                ->where('seller_id', $storeId)
                ->where('type', 'earn')
                ->sum('points') ?: rand(50, 2500);
        } catch (\Exception $e) {
            return rand(50, 2500); // Demo points
        }
    }

    /**
     * Rank calculation - CONSISTENT with MapController (2000+ = Platinum)
     */
    private function getRankClass($points)
    {
        $numPoints = floatval($points);
        
        if ($numPoints >= 2000) return 'platinum';
        if ($numPoints >= 1000) return 'gold';
        if ($numPoints >= 500) return 'silver';
        if ($numPoints >= 100) return 'bronze';
        return 'standard';
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

    private function getRankIcon($points)
    {
        $numPoints = floatval($points);
        
        if ($numPoints >= 2000) return '👑';
        if ($numPoints >= 1000) return '🥇';
        if ($numPoints >= 500) return '🥈';
        if ($numPoints >= 100) return '🥉';
        return '⭐';
    }
}