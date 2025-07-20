<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SellerController extends Controller
{
    /**
     * Show seller product/posts page
     */
    public function show($id)
    {
        $consumer = Auth::guard('consumer')->user();
        
        if (!$consumer) {
            return redirect()->route('login')
                ->with('error', 'Please login to view seller pages');
        }

        try {
            // Get seller information
            $seller = $this->getSellerById($id);
            
            if (!$seller) {
                return redirect()->route('map')
                    ->with('error', 'Seller not found');
            }

            // Get seller posts
            $posts = $this->getSellerPosts($id);
            
            // Get seller photos
            $photos = $this->getSellerPhotos($id);
            
            // Get menu items (if available)
            $menu_items = $this->getMenuItems($id);
            
            return view('seller.show', [
                'seller' => $seller,
                'posts' => $posts,
                'photos' => $photos,
                'menu_items' => $menu_items,
                'consumer' => $consumer
            ]);
            
        } catch (\Exception $e) {
            Log::error('Seller page error: ' . $e->getMessage());
            
            return redirect()->route('map')
                ->with('error', 'Unable to load seller page');
        }
    }

    /**
     * Get seller by ID with ranking data
     */
    private function getSellerById($id)
    {
        $seller = DB::table('sellers')
            ->select([
                'id',
                'business_name',
                'description', 
                'working_hours',
                'email',
                'address',
                'latitude',
                'longitude',
                'photo_url',
                'phone',
                'total_points',
                'is_active'
            ])
            ->where('id', $id)
            ->where('is_active', true)
            ->first();

        if (!$seller) {
            return null;
        }

        // Add ranking data
        $seller = $this->enrichSellerWithRanking($seller);
        
        return $seller;
    }

    /**
     * Get seller posts
     */
    private function getSellerPosts($sellerId)
    {
        // Check if you have a posts table, if not return sample data
        $posts = collect();
        
        // If you have a posts table:
        /*
        $posts = DB::table('seller_posts')
            ->where('seller_id', $sellerId)
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($post) {
                // Get post images
                $post->images = DB::table('post_images')
                    ->where('post_id', $post->id)
                    ->orderBy('order', 'asc')
                    ->get();
                
                return $post;
            });
        */
        
        // For now, return empty collection
        return $posts;
    }

    /**
     * Get seller photos
     */
    private function getSellerPhotos($sellerId)
    {
        // Check if using separate photos table
        if (DB::getSchemaBuilder()->hasTable('seller_photos')) {
            return DB::table('seller_photos')
                ->where('seller_id', $sellerId)
                ->orderBy('is_featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        return collect();
    }

    /**
     * Get menu items for seller
     */
    private function getMenuItems($sellerId)
    {
        // Check if you have menu/items table
        if (DB::getSchemaBuilder()->hasTable('items')) {
            return DB::table('items')
                ->where('seller_id', $sellerId)
                ->where('status', 'active')
                ->orderBy('category', 'asc')
                ->orderBy('name', 'asc')
                ->get();
        }
        
        return collect();
    }

    /**
     * Add ranking information to seller
     */
    private function enrichSellerWithRanking($seller)
    {
        // Get total points given to consumers
        $pointsGiven = DB::table('point_transactions')
            ->where('seller_id', $seller->id)
            ->where('type', 'earn')
            ->sum('points');

        // Get transaction count
        $transactionCount = DB::table('point_transactions')
            ->where('seller_id', $seller->id)
            ->where('type', 'earn')
            ->count();

        // Calculate rank
        $seller->points_reward = $pointsGiven;
        $seller->transaction_count = $transactionCount;
        $seller->rank_class = $this->getRankClass($pointsGiven);
        $seller->rank_text = $this->getRankText($pointsGiven);
        $seller->rank_icon = $this->getRankIcon($pointsGiven);
        
        // Add posts count (placeholder)
        $seller->posts_count = 0; // Update when you have posts table
        
        return $seller;
    }

    /**
     * Ranking helper functions
     */
    private function getRankClass($points)
    {
        if ($points >= 500) return 'platinum';
        if ($points >= 250) return 'gold';
        if ($points >= 100) return 'silver';
        if ($points >= 50) return 'bronze';
        return 'standard';
    }

    private function getRankText($points)
    {
        if ($points >= 500) return 'Platinum';
        if ($points >= 250) return 'Gold';
        if ($points >= 100) return 'Silver';
        if ($points >= 50) return 'Bronze';
        return 'Standard';
    }

    private function getRankIcon($points)
    {
        if ($points >= 500) return '👑';
        if ($points >= 250) return '🥇';
        if ($points >= 100) return '🥈';
        if ($points >= 50) return '🥉';
        return '⭐';
    }
}