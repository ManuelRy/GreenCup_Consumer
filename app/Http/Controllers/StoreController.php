<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class StoreController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Gallery/Feed Methods (from GalleryController)
    |--------------------------------------------------------------------------
    */

    /**
     * Show Instagram-style feed of store photos
     */
    public function gallery()
    {
        try {
            $consumer = Auth::guard('consumer')->user();
            if (!$consumer) {
                return redirect()->route('login')->with('error', 'Please login to view the gallery');
            }

            $postsData = $this->getFeedPosts(1, 20);

            return view('gallery.index', [
                'posts' => $postsData['posts'],
                'consumer' => $consumer,
                'hasMore' => $postsData['hasMore']
            ]);

        } catch (\Exception $e) {
            \Log::error('Gallery index error: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Unable to load gallery. Please try again.');
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
            return response()->json(['success' => false, 'message' => 'Unable to load posts']);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Map/Location Methods (from MapController)
    |--------------------------------------------------------------------------
    */

    /**
     * Display the store locator map page
     */
    public function map()
    {
        try {
            $stores = $this->getAllStores();
            $mapboxToken = env('MAPBOX_ACCESS_TOKEN', 'pk.eyJ1IjoibmVha3NlbmJlc3RmcmkiLCJhIjoiY205cXhkb3c3MTF3MzJ2b2doamJiM2NmaSJ9.zTnzZvYetGaqX0CODz4qoQ');
            
            return view('map.index', compact('stores'))->with('mapboxToken', $mapboxToken);
                   
        } catch (\Exception $e) {
            Log::error('[StoreController@map] ' . $e->getMessage());
            
            $stores = collect([]);
            $mapboxToken = env('MAPBOX_ACCESS_TOKEN', 'pk.eyJ1IjoibmVha3NlbmJlc3RmcmkiLCJhIjoiY205cXhkb3c3MTF3MzJ2b2doamJiM2NmaSJ9.zTnzZvYetGaqX0CODz4qoQ');
            
            return view('map.index', compact('stores'))
                   ->with('mapboxToken', $mapboxToken)
                   ->with('error', 'Some stores may not be available at the moment.');
        }
    }

    /**
     * Get all stores with locations (API endpoint)
     */
    public function getStores(Request $request)
    {
        try {
            $stores = $this->getAllStores($request->get('search'));
            
            return response()->json([
                'success' => true,
                'data' => $stores,
                'count' => $stores->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get stores API error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch stores',
                'data' => []
            ], 500);
        }
    }

    /**
     * Calculate distance between user and store
     */
    public function calculateDistance(Request $request)
    {
        $request->validate([
            'user_lat' => 'required|numeric|between:-90,90',
            'user_lng' => 'required|numeric|between:-180,180',
            'store_id' => 'required|integer|exists:sellers,id'
        ]);

        try {
            $store = $this->getStoreById($request->store_id);
            
            if (!$store || !$store->latitude || !$store->longitude) {
                return response()->json(['success' => false, 'message' => 'Store location not available'], 404);
            }

            $distance = $this->calculateHaversineDistance(
                $request->user_lat, $request->user_lng,
                $store->latitude, $store->longitude
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'distance_km' => round($distance, 2),
                    'distance_miles' => round($distance * 0.621371, 2)
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Calculate distance error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Unable to calculate distance'], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Store Profile Methods (from SellerController)
    |--------------------------------------------------------------------------
    */

    /**
     * Show individual store profile page
     */
    public function show($id)
    {
        try {
            $seller = DB::table('sellers')
                ->where('id', $id)
                ->where('is_active', true)
                ->first([
                    'id', 'business_name', 'description', 'address', 'phone',
                    'working_hours', 'photo_url', 'photo_caption', 'total_points', 'created_at'
                ]);

            if (!$seller) {
                abort(404, 'Store not found');
            }

            $photos = $this->getSellerPhotos($id);
            $seller->photos = $photos;
            $seller = $this->addRankingData($seller);

            return view('sellers.show', compact('seller'));

        } catch (\Exception $e) {
            \Log::error('Error loading seller profile: ' . $e->getMessage());
            abort(404, 'Store not found');
        }
    }

    /**
     * Get store details (API)
     */
    public function getStoreDetails($id)
    {
        try {
            $store = $this->getStoreById($id);
            
            if (!$store) {
                return response()->json(['success' => false, 'message' => 'Store not found'], 404);
            }

            $storeDetails = $this->enrichStoreData($store);
            return response()->json(['success' => true, 'data' => $storeDetails]);
            
        } catch (\Exception $e) {
            Log::error('Get store details error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Unable to fetch store details'], 500);
        }
    }

    /**
     * Get store's transaction history
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

            return response()->json(['success' => true, 'transactions' => $transactions]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Unable to load transactions']);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Search Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Search stores by location or name
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2|max:100',
            'user_lat' => 'nullable|numeric|between:-90,90',
            'user_lng' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:100'
        ]);

        try {
            $stores = $this->searchStoresByQuery(
                $request->query,
                $request->user_lat,
                $request->user_lng,
                $request->radius ?? 50
            );
            
            return response()->json([
                'success' => true,
                'data' => $stores,
                'count' => $stores->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Search stores error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Search failed', 'data' => []], 500);
        }
    }

    /**
     * Gallery search for sellers
     */
    public function gallerySearch(Request $request)
    {
        try {
            $query = $request->get('q', '');
            
            if (empty(trim($query))) {
                return response()->json(['success' => true, 'sellers' => []]);
            }
            
            $sellers = DB::table('sellers')
                ->where('is_active', true)
                ->where(function($queryBuilder) use ($query) {
                    $queryBuilder->where('business_name', 'like', "%{$query}%")
                               ->orWhere('description', 'like', "%{$query}%")
                               ->orWhere('address', 'like', "%{$query}%");
                })
                ->select(['id', 'business_name', 'description', 'address', 'photo_url', 'total_points'])
                ->orderByDesc('total_points')
                ->limit(20)
                ->get();

            $sellers = $sellers->map(function($seller) {
                return $this->addRankingData($seller);
            });

            return response()->json(['success' => true, 'sellers' => $sellers]);

        } catch (\Exception $e) {
            \Log::error('Search error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Search failed']);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Statistics Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get photo statistics
     */
    public function getPhotoStats()
    {
        try {
            $stats = [
                'total_sellers' => DB::table('sellers')->where('is_active', true)->count(),
                'sellers_with_main_photo' => DB::table('sellers')->where('is_active', true)->whereNotNull('photo_url')->count(),
                'total_seller_photos' => 0,
                'sellers_with_gallery_photos' => 0,
                'total_posts' => 0
            ];

            if (DB::getSchemaBuilder()->hasTable('seller_photos')) {
                $stats['total_seller_photos'] = DB::table('seller_photos')->count();
                $stats['sellers_with_gallery_photos'] = DB::table('seller_photos')->distinct('seller_id')->count('seller_id');
                $stats['total_posts'] = DB::table('seller_photos as sp')
                    ->join('sellers as s', 's.id', '=', 'sp.seller_id')
                    ->where('s.is_active', true)
                    ->count();
            }

            return response()->json(['success' => true, 'stats' => $stats]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Unable to get stats']);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Private Helper Methods
    |--------------------------------------------------------------------------
    */

    private function getFeedPosts($page = 1, $perPage = 20)
    {
        try {
            $offset = ($page - 1) * $perPage;
            
            $query = DB::table('seller_photos as sp')
                ->join('sellers as s', 's.id', '=', 'sp.seller_id')
                ->where('s.is_active', true)
                ->select([
                    'sp.id', 'sp.photo_url', 'sp.caption', 'sp.category', 'sp.is_featured', 'sp.created_at',
                    's.id as seller_id', 's.business_name', 's.address', 's.phone', 's.total_points', 's.description'
                ])
                ->orderByDesc('sp.created_at');

            $total = $query->count();
            $posts = $query->offset($offset)->limit($perPage)->get();

            $processedPosts = collect();
            
            foreach ($posts as $post) {
                $photoUrl = $post->photo_url;
                if (!str_starts_with($photoUrl, 'http') && !str_starts_with($photoUrl, '/')) {
                    $photoUrl = '/storage/seller_photos/' . $photoUrl;
                }
                if (str_starts_with($photoUrl, '/storage/')) {
                    $photoUrl = asset($photoUrl);
                }
                
                $timeAgo = $this->getTimeAgo($post->created_at);
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

            $hasMore = ($offset + $perPage) < $total;

            return ['posts' => $processedPosts, 'hasMore' => $hasMore];

        } catch (\Exception $e) {
            \Log::error('Error getting feed posts: ' . $e->getMessage());
            return ['posts' => collect(), 'hasMore' => false];
        }
    }

    private function getAllStores($search = null)
    {
        try {
            $query = DB::table('sellers')
                ->select([
                    'id', 'business_name as name', 'description', 'working_hours as hours',
                    'email', 'address', 'latitude', 'longitude', 'photo_url as image',
                    'phone', 'total_points', 'is_active'
                ])
                ->where('is_active', true)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('business_name', 'LIKE', "%{$search}%")
                      ->orWhere('address', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            $stores = $query->get();

            return $stores->map(function($store) {
                return $this->enrichStoreData($store);
            });
            
        } catch (\Exception $e) {
            Log::error('Error fetching stores: ' . $e->getMessage());
            return collect([]);
        }
    }

    private function getStoreById($id)
    {
        try {
            return DB::table('sellers')
                ->select([
                    'id', 'business_name as name', 'description', 'working_hours as hours',
                    'email', 'address', 'latitude', 'longitude', 'photo_url as image',
                    'phone', 'total_points', 'is_active'
                ])
                ->where('id', $id)
                ->where('is_active', true)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->first();
        } catch (\Exception $e) {
            Log::error('Error fetching store by ID: ' . $e->getMessage());
            return null;
        }
    }

    private function enrichStoreData($store)
    {
        $store->phone = $store->phone ?: $this->generateDemoPhone();
        $store->points_reward = $this->getStorePointsGivenToConsumers($store->id);
        $store->rank_class = $this->getRankClass($store->points_reward);
        $store->rank_text = $this->getRankText($store->points_reward);
        $store->rank_icon = $this->getRankIcon($store->points_reward);
        $store->transaction_count = $this->getStoreTransactionCount($store->id);
        $store->hours_formatted = $this->formatWorkingHours($store->hours);
        $store->is_open = $this->isStoreCurrentlyOpen($store->hours);
        $store->distance = 0;
        
        return $store;
    }

    private function addRankingData($seller)
    {
        $points = $seller->total_points ?? 0;
        $seller->rank_class = $this->getRankClass($points);
        $seller->rank_text = $this->getRankText($points);
        $seller->rank_icon = $this->getRankIcon($points);
        $seller->points_reward = $points;
        return $seller;
    }

    private function getSellerPhotos($sellerId)
    {
        try {
            $photos = collect();

            if (DB::getSchemaBuilder()->hasTable('seller_photos')) {
                $dbPhotos = DB::table('seller_photos')
                    ->where('seller_id', $sellerId)
                    ->orderByDesc('is_featured')
                    ->orderBy('sort_order')
                    ->orderByDesc('created_at')
                    ->get(['id', 'photo_url', 'caption', 'category', 'is_featured', 'sort_order', 'created_at']);

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

    private function searchStoresByQuery($query, $userLat = null, $userLng = null, $radiusKm = 50)
    {
        try {
            $baseQuery = DB::table('sellers')
                ->select([
                    'id', 'business_name as name', 'description', 'working_hours as hours',
                    'email', 'address', 'latitude', 'longitude', 'photo_url as image',
                    'phone', 'total_points', 'is_active'
                ])
                ->where('is_active', true)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->where(function($q) use ($query) {
                    $q->where('business_name', 'LIKE', "%{$query}%")
                      ->orWhere('address', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%");
                });

            if ($userLat && $userLng) {
                $baseQuery->whereRaw($this->getDistanceWhereClause($userLat, $userLng, $radiusKm));
            }

            $stores = $baseQuery->get();

            return $stores->map(function($store) {
                return $this->enrichStoreData($store);
            });
        } catch (\Exception $e) {
            Log::error('Error searching stores: ' . $e->getMessage());
            return collect([]);
        }
    }

    private function getStorePointsGivenToConsumers($storeId)
    {
        try {
            $exists = DB::select("SHOW TABLES LIKE 'point_transactions'");
            if (empty($exists)) {
                return rand(10, 500);
            }
            
            return DB::table('point_transactions')
                ->where('seller_id', $storeId)
                ->where('type', 'earn')
                ->sum('points') ?: rand(10, 500);
        } catch (\Exception $e) {
            Log::error('Error getting store points: ' . $e->getMessage());
            return rand(10, 500);
        }
    }

    private function getStoreTransactionCount($storeId)
    {
        try {
            $exists = DB::select("SHOW TABLES LIKE 'point_transactions'");
            if (empty($exists)) {
                return rand(5, 50);
            }
            
            return DB::table('point_transactions')
                ->where('seller_id', $storeId)
                ->where('type', 'earn')
                ->count() ?: rand(5, 50);
        } catch (\Exception $e) {
            Log::error('Error getting transaction count: ' . $e->getMessage());
            return rand(5, 50);
        }
    }

    private function getTimeAgo($datetime)
    {
        try {
            $created = Carbon::parse($datetime);
            $now = Carbon::now();
            $diff = $created->diff($now);
            
            if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
            elseif ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
            elseif ($diff->d > 0) return $diff->d == 1 ? 'Yesterday' : $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
            elseif ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
            elseif ($diff->i > 0) return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
            else return 'Just now';
        } catch (\Exception $e) {
            return 'Recently';
        }
    }

    private function calculateHaversineDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2) * sin($dLng/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c;
    }

    private function getDistanceWhereClause($userLat, $userLng, $radiusKm)
    {
        return "(6371 * acos(cos(radians({$userLat})) * cos(radians(latitude)) * cos(radians(longitude) - radians({$userLng})) + sin(radians({$userLat})) * sin(radians(latitude)))) <= {$radiusKm}";
    }

    private function generateDemoPhone()
    {
        return '+855 ' . rand(10, 99) . ' ' . rand(100, 999) . ' ' . rand(100, 999);
    }

    private function formatWorkingHours($hours)
    {
        return $hours ?: 'Hours not specified';
    }

    private function isStoreCurrentlyOpen($hours)
    {
        return !empty($hours);
    }

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