<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GalleryController extends Controller
{
    /**
     * Show Facebook-style feed of seller photos
     */
    public function index()
    {
        try {
            $consumer = Auth::guard('consumer')->user();
            
            if (!$consumer) {
                return redirect()->route('login')
                    ->with('error', 'Please login to view the gallery');
            }

            // Get posts for the feed (each photo as individual post)
            $postsData = $this->getFeedPosts(1, 20);

            return view('gallery.index', [
                'posts' => $postsData['posts'],
                'consumer' => $consumer,
                'hasMore' => $postsData['hasMore']
            ]);

        } catch (\Exception $e) {
            \Log::error('Gallery index error: ' . $e->getMessage());
            return redirect()->route('dashboard')
                ->with('error', 'Unable to load gallery. Please try again.');
        }
    }

    /**
     * Get feed posts (AJAX endpoint for pagination)
     */
    public function getFeed(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = 20;
            
            $postsData = $this->getFeedPosts($page, $perPage);
            
            return response()->json([
                'success' => true,
                'posts' => $postsData['posts'],
                'hasMore' => $postsData['hasMore']
            ]);

        } catch (\Exception $e) {
            \Log::error('Error loading feed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Unable to load posts'
            ]);
        }
    }

    /**
     * Get stores with photos - For backward compatibility
     */
    private function getStoresWithPhotos()
    {
        try {
            $sellers = DB::table('sellers')
                ->where('is_active', true)
                ->select([
                    'id',
                    'business_name',
                    'description',
                    'address',
                    'phone',
                    'working_hours',
                    'photo_url as main_photo_url',
                    'photo_caption as main_photo_caption',
                    'total_points',
                    'created_at',
                    'updated_at'
                ])
                ->orderByDesc('total_points')
                ->get();

            $storesWithPhotos = collect();

            foreach ($sellers as $seller) {
                $photos = $this->getSellerPhotos($seller->id);
                
                if (count($photos) > 0) {
                    $seller->photos = $photos;
                    $seller = $this->addRankingData($seller);
                    $storesWithPhotos->push($seller);
                }
            }

            return $storesWithPhotos;

        } catch (\Exception $e) {
            \Log::error('Error getting stores with photos: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get posts for the feed in chronological order
     */
    private function getFeedPosts($page = 1, $perPage = 20)
    {
        try {
            $offset = ($page - 1) * $perPage;
            
            // Build the query for seller photos
            $query = DB::table('seller_photos as sp')
                ->join('sellers as s', 's.id', '=', 'sp.seller_id')
                ->where('s.is_active', true)
                ->select([
                    'sp.id',
                    'sp.photo_url',
                    'sp.caption',
                    'sp.category',
                    'sp.is_featured',
                    'sp.created_at',
                    's.id as seller_id',
                    's.business_name',
                    's.address',
                    's.phone',
                    's.total_points',
                    's.description'
                ])
                ->orderByDesc('sp.created_at');

            // Get total count for pagination
            $total = $query->count();
            
            // Get posts for current page
            $posts = $query->offset($offset)
                          ->limit($perPage)
                          ->get();

            \Log::info('Feed posts query result', [
                'total' => $total,
                'posts_count' => $posts->count(),
                'page' => $page,
                'offset' => $offset
            ]);

            // Process posts
            $processedPosts = collect();
            
            foreach ($posts as $post) {
                // Format photo URL
                $photoUrl = $post->photo_url;
                if (!str_starts_with($photoUrl, 'http') && !str_starts_with($photoUrl, '/')) {
                    $photoUrl = '/storage/seller_photos/' . $photoUrl;
                }
                if (str_starts_with($photoUrl, '/storage/')) {
                    $photoUrl = asset($photoUrl);
                }
                
                // Calculate time ago
                $timeAgo = $this->getTimeAgo($post->created_at);
                
                // Add ranking data
                $points = $post->total_points ?? 0;
                
                $processedPost = (object)[
                    'id' => $post->id,
                    'seller_id' => $post->seller_id,
                    'business_name' => $post->business_name,
                    'address' => $post->address,
                    'phone' => $post->phone,
                    'photo_url' => $photoUrl,
                    'caption' => $post->caption,
                    'category' => $post->category,
                    'is_featured' => $post->is_featured,
                    'created_at' => $post->created_at,
                    'time_ago' => $timeAgo,
                    'total_points' => $points,
                    'rank_class' => $this->getRankClass($points),
                    'rank_text' => $this->getRankText($points),
                    'rank_icon' => $this->getRankIcon($points)
                ];
                
                $processedPosts->push($processedPost);
            }

            // Check if there are more posts
            $hasMore = ($offset + $perPage) < $total;

            return [
                'posts' => $processedPosts,
                'hasMore' => $hasMore
            ];

        } catch (\Exception $e) {
            \Log::error('Error getting feed posts: ' . $e->getMessage());
            return [
                'posts' => collect(),
                'hasMore' => false
            ];
        }
    }

    /**
     * Calculate time ago string
     */
    private function getTimeAgo($datetime)
    {
        try {
            $created = Carbon::parse($datetime);
            $now = Carbon::now();
            
            $diff = $created->diff($now);
            
            if ($diff->y > 0) {
                return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
            } elseif ($diff->m > 0) {
                return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
            } elseif ($diff->d > 0) {
                if ($diff->d == 1) {
                    return 'Yesterday';
                }
                return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
            } elseif ($diff->h > 0) {
                return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
            } elseif ($diff->i > 0) {
                return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
            } else {
                return 'Just now';
            }
        } catch (\Exception $e) {
            return 'Recently';
        }
    }

    /**
     * Show individual seller/store page
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
                    'total_points',
                    'created_at'
                ]);

            if (!$seller) {
                abort(404, 'Store not found');
            }

            // Get all seller's photos
            $photos = $this->getSellerPhotos($id);
            $seller->photos = $photos;
            
            // Add ranking data
            $seller = $this->addRankingData($seller);

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
            $photos = collect();

            // Get from seller_photos table
            if (DB::getSchemaBuilder()->hasTable('seller_photos')) {
                $dbPhotos = DB::table('seller_photos')
                    ->where('seller_id', $sellerId)
                    ->orderByDesc('is_featured')
                    ->orderBy('sort_order')
                    ->orderByDesc('created_at')
                    ->get([
                        'id',
                        'photo_url',
                        'caption',
                        'category',
                        'is_featured',
                        'sort_order',
                        'created_at'
                    ]);

                foreach ($dbPhotos as $photo) {
                    $photoUrl = $photo->photo_url;
                    
                    if (!str_starts_with($photoUrl, 'http') && !str_starts_with($photoUrl, '/')) {
                        $photoUrl = '/storage/seller_photos/' . $photoUrl;
                    }
                    
                    if (str_starts_with($photoUrl, '/storage/')) {
                        $photoUrl = asset($photoUrl);
                    }

                    $photos->push((object)[
                        'id' => $photo->id,
                        'url' => $photoUrl,
                        'caption' => $photo->caption ?? '',
                        'category' => $photo->category ?? 'store',
                        'is_featured' => (bool)$photo->is_featured,
                        'sort_order' => $photo->sort_order ?? 0,
                        'created_at' => $photo->created_at
                    ]);
                }
            }

            // Fallback to seller's main photo if no gallery photos
            if ($photos->isEmpty()) {
                $seller = DB::table('sellers')
                    ->where('id', $sellerId)
                    ->whereNotNull('photo_url')
                    ->first(['photo_url', 'photo_caption']);

                if ($seller && $seller->photo_url) {
                    $photoUrl = $seller->photo_url;
                    if (str_starts_with($photoUrl, '/storage/')) {
                        $photoUrl = asset($photoUrl);
                    }
                    
                    $photos->push((object)[
                        'id' => 0,
                        'url' => $photoUrl,
                        'caption' => $seller->photo_caption ?? '',
                        'category' => 'store',
                        'is_featured' => true,
                        'sort_order' => 0,
                        'created_at' => now()
                    ]);
                }
            }

            return $photos->toArray();

        } catch (\Exception $e) {
            \Log::error('Error loading seller photos for seller ' . $sellerId . ': ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Add ranking data to seller object
     */
    private function addRankingData($seller)
    {
        $points = $seller->total_points ?? 0;
        
        $seller->rank_class = $this->getRankClass($points);
        $seller->rank_text = $this->getRankText($points);
        $seller->rank_icon = $this->getRankIcon($points);
        $seller->points_reward = $points;
        
        return $seller;
    }

    /**
     * Search sellers
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q', '');
            
            if (empty(trim($query))) {
                return response()->json([
                    'success' => true,
                    'sellers' => []
                ]);
            }
            
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
                ->orderByDesc('total_points')
                ->limit(20)
                ->get();

            // Add rank information to each seller
            $sellers = $sellers->map(function($seller) {
                $seller = $this->addRankingData($seller);
                return $seller;
            });

            return response()->json([
                'success' => true,
                'sellers' => $sellers
            ]);

        } catch (\Exception $e) {
            \Log::error('Search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Search failed'
            ]);
        }
    }

    /**
     * Get photo statistics
     */
    public function getPhotoStats()
    {
        try {
            $stats = [
                'total_sellers' => DB::table('sellers')->where('is_active', true)->count(),
                'sellers_with_main_photo' => DB::table('sellers')
                    ->where('is_active', true)
                    ->whereNotNull('photo_url')
                    ->count(),
                'total_seller_photos' => 0,
                'sellers_with_gallery_photos' => 0,
                'total_posts' => 0
            ];

            if (DB::getSchemaBuilder()->hasTable('seller_photos')) {
                $stats['total_seller_photos'] = DB::table('seller_photos')->count();
                $stats['sellers_with_gallery_photos'] = DB::table('seller_photos')
                    ->distinct('seller_id')
                    ->count('seller_id');
                $stats['total_posts'] = DB::table('seller_photos as sp')
                    ->join('sellers as s', 's.id', '=', 'sp.seller_id')
                    ->where('s.is_active', true)
                    ->count();
            }

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to get stats'
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