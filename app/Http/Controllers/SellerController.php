<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SellerController extends Controller
{
    /**
     * Show a specific seller's profile/store page
     */
    public function show($id)
    {
        try {
            // Get seller information
            $seller = DB::table('sellers')
                ->where('id', $id)
                ->where('is_active', true)
                ->first([
                    'id',
                    'business_name',
                    'description', 
                    'address',
                    'phone',
                    'working_hours',
                    'photo_url',
                    'photo_caption',
                    'total_points'
                ]);

            if (!$seller) {
                abort(404, 'Store not found');
            }

            // Get seller's photos
            $photos = $this->getSellerPhotos($id);

            // Get seller rank information
            $seller->rank_class = $this->getRankClass($seller->total_points ?? 0);
            $seller->rank_text = $this->getRankText($seller->total_points ?? 0);
            $seller->rank_icon = $this->getRankIcon($seller->total_points ?? 0);

            // Add photos to seller object
            $seller->photos = $photos;

            return view('sellers.show', compact('seller'));

        } catch (\Exception $e) {
            \Log::error('Error loading seller profile: ' . $e->getMessage());
            abort(404, 'Store not found');
        }
    }

    /**
     * Get photos for a seller
     */
    private function getSellerPhotos($sellerId)
    {
        try {
            $photos = collect([]);

            // Get from seller_photos table
            if (DB::getSchemaBuilder()->hasTable('seller_photos')) {
                $dbPhotos = DB::table('seller_photos')
                    ->where('seller_id', $sellerId)
                    ->orderBy('is_featured', 'desc')
                    ->orderBy('sort_order')
                    ->orderBy('created_at', 'desc')
                    ->get(['photo_url', 'caption', 'category', 'is_featured', 'created_at']);

                foreach ($dbPhotos as $photo) {
                    $photos->push((object)[
                        'url' => $photo->photo_url,
                        'caption' => $photo->caption ?? '',
                        'category' => $photo->category ?? 'store',
                        'is_featured' => $photo->is_featured ?? false,
                        'created_at' => $photo->created_at
                    ]);
                }
            }

            // Fallback to seller's main photo if no photos in seller_photos
            if ($photos->isEmpty()) {
                $seller = DB::table('sellers')
                    ->where('id', $sellerId)
                    ->whereNotNull('photo_url')
                    ->first(['photo_url', 'photo_caption']);

                if ($seller && $seller->photo_url) {
                    $photos->push((object)[
                        'url' => $seller->photo_url,
                        'caption' => $seller->photo_caption ?? '',
                        'category' => 'store',
                        'is_featured' => true,
                        'created_at' => now()
                    ]);
                }
            }

            return $photos->toArray();

        } catch (\Exception $e) {
            \Log::error('Error loading seller photos: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get seller's point transactions for consumers to see
     */
    public function getTransactions($id)
    {
        try {
            $transactions = DB::table('point_transactions')
                ->where('seller_id', $id)
                ->where('type', 'earn')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(['points', 'description', 'created_at']);

            return response()->json([
                'success' => true,
                'transactions' => $transactions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to load transactions'
            ]);
        }
    }

    /**
     * Search sellers for consumers
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q', '');
            
            $sellers = DB::table('sellers')
                ->where('is_active', true)
                ->where(function($queryBuilder) use ($query) {
                    $queryBuilder->where('business_name', 'like', "%{$query}%")
                               ->orWhere('description', 'like', "%{$query}%")
                               ->orWhere('address', 'like', "%{$query}%");
                })
                ->select([
                    'id',
                    'business_name',
                    'description',
                    'address',
                    'photo_url',
                    'total_points'
                ])
                ->limit(10)
                ->get();

            // Add rank information to each seller
            $sellers = $sellers->map(function($seller) {
                $seller->rank_class = $this->getRankClass($seller->total_points ?? 0);
                $seller->rank_text = $this->getRankText($seller->total_points ?? 0);
                $seller->rank_icon = $this->getRankIcon($seller->total_points ?? 0);
                return $seller;
            });

            return response()->json([
                'success' => true,
                'sellers' => $sellers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed'
            ]);
        }
    }

    /**
     * Rank calculation helpers
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