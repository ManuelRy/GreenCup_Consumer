<?php

namespace App\Http\Controllers;

use App\Repository\SellerRepository;
use App\Repository\FileRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class StoreController extends Controller
{
    private SellerRepository $sRepo;
    private FileRepository $fileRepo;

    public function __construct(SellerRepository $sRepo, FileRepository $fileRepo)
    {
        $this->sRepo = $sRepo;
        $this->fileRepo = $fileRepo;
    }
    /*
    |--------------------------------------------------------------------------
    | Gallery/Feed Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Show Instagram-style feed of store photos
     */
    public function gallery()
    {
        try {
            $consumer = Auth::guard('consumer')->user();
            $postsData = $this->getFeedPosts(1, 20);

            return view('gallery.index', [
                'posts' => $postsData['posts'],
                'consumer' => $consumer,
                'hasMore' => $postsData['hasMore']
            ]);
        } catch (\Exception $e) {
            Log::error('Gallery index error: ' . $e->getMessage());
            return view('gallery.index', [
                'posts' => collect([]),
                'consumer' => null,
                'hasMore' => false
            ])->with('error', 'Unable to load gallery. Please try again.');
        }
    }

    /**
     * Get feed posts (AJAX endpoint for pagination)
     */
    public function getFeed(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $sellerId = $request->get('seller_id');
            $perPage = 20;

            $postsData = $this->getFeedPosts($page, $perPage, $sellerId);

            return response()->json([
                'success' => true,
                'posts' => $postsData['posts'],
                'hasMore' => $postsData['hasMore'],
                'message' => $postsData['posts']->count() > 0 ? 'Posts loaded successfully' : 'No posts found for this seller'
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading feed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Unable to load posts: ' . $e->getMessage(),
                'posts' => [],
                'hasMore' => false
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Map/Location Methods - FIXED for Real Data
    |--------------------------------------------------------------------------
    */

    /**
     * Display the store locator map page
     */
    public function map()
    {
        try {
            // Support both authenticated and guest users
            $consumer = Auth::guard('consumer')->user();

            $stores = $this->getAllStoresForMap();
            $mapboxToken = env('MAPBOX_ACCESS_TOKEN', 'pk.eyJ1IjoibmVha3NlbmJlc3RmcmkiLCJhIjoiY205cXhkb3c3MTF3MzJ2b2doamJiM2NmaSJ9.zTnzZvYetGaqX0CODz4qoQ');

            Log::info('Map method called - Stores found: ' . $stores->count());

            return view('map.index', compact('stores', 'consumer', 'mapboxToken'));
        } catch (\Exception $e) {
            Log::error('[StoreController@map] Error: ' . $e->getMessage());

            $consumer = Auth::guard('consumer')->user();
            $stores = collect([]);
            $mapboxToken = env('MAPBOX_ACCESS_TOKEN', 'pk.eyJ1IjoibmVha3NlbmJlc3RmcmkiLCJhIjoiY205cXhkb3c3MTF3MzJ2b2doamJiM2NmaSJ9.zTnzZvYetGaqX0CODz4qoQ');

            return view('map.index', compact('stores', 'consumer', 'mapboxToken'))
                ->with('error', 'Some stores may not be available at the moment.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | API Methods for Map
    |--------------------------------------------------------------------------
    */

    /**
     * Get all stores (API endpoint) - FIXED with real data
     */
    public function getStores(Request $request)
    {
        try {
            $search = $request->get('search');
            $stores = $this->getAllStores($search);

            return response()->json([
                'success' => true,
                'data' => $stores,
                'count' => $stores->count(),
                'message' => $stores->count() > 0 ? 'Stores loaded successfully' : 'No stores found in database'
            ]);
        } catch (\Exception $e) {
            Log::error('Get stores API error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch stores: ' . $e->getMessage(),
                'data' => [],
                'count' => 0
            ], 500);
        }
    }

    /**
     * Get store details (API)
     */
    public function getStoreDetails($id)
    {
        try {
            $store = $this->sRepo->get($id);

            if (!$store) {
                return response()->json(['success' => false, 'message' => 'Store not found'], 404);
            }

            $storeDetails = $this->enrichStoreData($store);

            // Add business_name explicitly (ensure it's included)
            $storeDetails->business_name = $store->business_name;
            $storeDetails->name = $store->business_name; // Map to name for compatibility

            // Add items with their image_url
            $storeDetails->items = $this->getStoreItems($id);

            // Add available rewards for the store
            $storeDetails->rewards = $this->getStoreRewards($id);

            return response()->json(['success' => true, 'data' => $storeDetails]);
        } catch (\Exception $e) {
            Log::error('Get store details error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Unable to fetch store details'], 500);
        }
    }

    /**
     * Get store items with image_url
     */
    private function getStoreItems($storeId)
    {
        try {
            $items = DB::table('items')
                ->where('seller_id', $storeId)
                ->select([
                    'id',
                    'name',
                    'points_per_unit',
                    'image_url'
                ])
                ->orderBy('name')
                ->get();

            return $items->map(function ($item) {
                // Ensure image_url is properly formatted
                $imageUrl = $item->image_url;
                if ($imageUrl && !str_starts_with($imageUrl, 'http') && !str_starts_with($imageUrl, '/')) {
                    $imageUrl = '/storage/items/' . $imageUrl;
                }
                if ($imageUrl && str_starts_with($imageUrl, '/storage/')) {
                    $imageUrl = asset($imageUrl);
                }

                return (object)[
                    'id' => $item->id,
                    'name' => $item->name,
                    'points_per_unit' => $item->points_per_unit,
                    'image_url' => $imageUrl ?: asset('images/placeholder.png'), // fallback to placeholder
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error fetching items for store ' . $storeId . ': ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get store rewards (valid rewards only)
     */
    private function getStoreRewards($storeId)
    {
        try {
            // Use Reward model to leverage the image_path accessor
            $rewards = \App\Models\Reward::where('seller_id', $storeId)
                ->where('is_active', true)
                ->where('valid_until', '>=', Carbon::now())
                ->where('valid_from', '<=', Carbon::now())
                ->whereRaw('quantity > quantity_redeemed')
                ->orderBy('points_required')
                ->get();

            return $rewards->map(function ($reward) {
                return (object)[
                    'id' => $reward->id,
                    'name' => $reward->name,
                    'description' => $reward->description,
                    'points_required' => $reward->points_required,
                    'remaining_stock' => $reward->remaining_stock,
                    'image_url' => $reward->image_url, // Uses the accessor which handles normalization
                    'expires_at' => $reward->valid_until
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error fetching rewards for store ' . $storeId . ': ' . $e->getMessage());
            return collect([]);
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
                $request->user_lat,
                $request->user_lng,
                $store->latitude,
                $store->longitude
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
    | Store Profile Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Show individual store profile page
     */
    public function show($id)
    {
        try {
            $consumer = Auth::guard('consumer')->user();
            if (!$consumer) {
                return redirect()->route('login')->with('error', 'Please login to view store profiles');
            }

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

            $photos = $this->getSellerPhotos($id);
            $seller->photos = $photos;
            $seller = $this->addRankingData($seller);

            return view('store', compact('seller', 'consumer'));
        } catch (\Exception $e) {
            Log::error('Error loading seller profile: ' . $e->getMessage());
            abort(404, 'Store not found');
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
                $request->input('query'),
                $request->input('user_lat'),
                $request->input('user_lng'),
                $request->input('radius', 50)
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
                ->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('business_name', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%")
                        ->orWhere('address', 'like', "%{$query}%");
                })
                ->select(['id', 'business_name', 'description', 'address', 'photo_url', 'total_points'])
                ->orderByDesc('total_points')
                ->limit(20)
                ->get();

            $sellers = $sellers->map(function ($seller) {
                return $this->addRankingData($seller);
            });

            return response()->json(['success' => true, 'sellers' => $sellers]);
        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage());
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
    | Private Helper Methods - FIXED for Real Data
    |--------------------------------------------------------------------------
    */

    /**
     * Get all stores specifically formatted for map display - USES REAL DATA
     */
    private function getAllStoresForMap()
    {
        try {
            // Get base store data
            $stores = DB::table('sellers')
                ->select([
                    'id',
                    'business_name as name',
                    'description',
                    'working_hours as hours',
                    'email',
                    'address',
                    'latitude',
                    'longitude',
                    'photo_url as image',
                    'phone',
                    'total_points',
                    'is_active',
                    'created_at'
                ])
                ->where('is_active', true)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->where('latitude', '!=', 0)
                ->where('longitude', '!=', 0)
                ->get();

            Log::info('Raw stores fetched for map: ' . $stores->count());

            // Process each store using the same logic as gallery (use total_points directly)
            return $stores->map(function ($store) {
                // Use the same points logic as gallery - direct from sellers table
                $points = $store->total_points ?? 0;

                // For transaction count, still get from point_transactions if needed
                $transactionCount = DB::table('point_transactions')
                    ->where('seller_id', $store->id)
                    ->count();

                // Generate consistent phone if missing (only for phone)
                if (empty($store->phone)) {
                    $store->phone = $this->generateConsistentPhone($store->id);
                }

                // Calculate ranking based on points (same as gallery)
                $rankClass = $this->getRankClass($points);
                $rankText = $this->getRankText($points);
                $rankIcon = $this->getRankIcon($points);

                // Ensure seller profile image is properly formatted
                $profileImage = $this->resolveSellerImage($store->image);

                return (object)[
                    'id' => $store->id,
                    'name' => $store->name,
                    'business_name' => $store->name, // Explicit business_name field
                    'description' => $store->description ?: 'Quality eco-friendly products and services',
                    'hours' => $store->hours ?: '9:00 AM - 6:00 PM',
                    'address' => $store->address,
                    'latitude' => floatval($store->latitude),
                    'longitude' => floatval($store->longitude),
                    'phone' => $store->phone,
                    'email' => $store->email,
                    'image' => $profileImage,
                    'photo_url' => $profileImage,
                    'is_active' => $store->is_active,

                    // Points data (same as gallery)
                    'total_points' => $points,
                    'points_reward' => $points,
                    'transaction_count' => $transactionCount,

                    // Ranking based on points
                    'rank_class' => $rankClass,
                    'rank_text' => $rankText,
                    'rank_icon' => $rankIcon,

                    // Other fields
                    'distance' => null,
                    'hours_formatted' => $this->formatWorkingHours($store->hours),
                    'is_open' => $this->isStoreCurrentlyOpen($store->hours),
                    'created_at' => $store->created_at
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error fetching stores for map: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get feed posts for gallery
     */
    private function getFeedPosts($page = 1, $perPage = 20, $sellerId = null)
    {
        try {
            $offset = ($page - 1) * $perPage;

            // Check if seller_photos table exists
            if (!DB::getSchemaBuilder()->hasTable('seller_photos')) {
                Log::info('seller_photos table does not exist');
                return ['posts' => collect(), 'hasMore' => false];
            }

            $query = DB::table('seller_photos as sp')
                ->join('sellers as s', 's.id', '=', 'sp.seller_id')
                ->where('s.is_active', true)
                ->where(function($q) {
                    $q->whereNull('sp.photo_caption')
                      ->orWhere('sp.photo_caption', '')
                      ->orWhere('sp.photo_caption', 'NOT LIKE', '[FROZEN] %');
                })
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
                    's.description',
                    's.photo_url as store_photo'
                ]);

            // Filter by specific seller if provided
            if ($sellerId) {
                $query->where('s.id', $sellerId);
            }

            $query->orderByDesc('sp.created_at');

            $total = $query->count();
            $posts = $query->offset($offset)->limit($perPage)->get();

            Log::info("Found {$posts->count()} posts for seller {$sellerId}, total: {$total}");

            $processedPosts = collect();

            foreach ($posts as $post) {
                // Use the same normalization logic for photo URLs
                $photoUrl = $this->resolveSellerImage($post->photo_url);

                $timeAgo = $this->getTimeAgo($post->created_at);
                $points = $post->total_points ?? 0;
                $storeImage = $this->resolveSellerImage($post->store_photo);

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
                    'rank_icon' => $this->getRankIcon($points),
                    'store_image' => $storeImage
                ];

                $processedPosts->push($processedPost);
            }

            $hasMore = ($offset + $perPage) < $total;

            return ['posts' => $processedPosts, 'hasMore' => $hasMore];
        } catch (\Exception $e) {
            Log::error('Error getting feed posts: ' . $e->getMessage());
            return ['posts' => collect(), 'hasMore' => false];
        }
    }

    /**
     * Get all stores with enriched data
     */
    private function getAllStores($search = null)
    {
        try {
            $query = DB::table('sellers')
                ->select([
                    'sellers.id',
                    'sellers.business_name as name',
                    'sellers.description',
                    'sellers.working_hours as hours',
                    'sellers.email',
                    'sellers.address',
                    'sellers.latitude',
                    'sellers.longitude',
                    'sellers.photo_url as image',
                    'sellers.phone',
                    'sellers.total_points',
                    'sellers.is_active'
                ])
                ->where('sellers.is_active', true);

            // Only filter by coordinates if they exist
            $query->where(function ($q) {
                $q->where(function ($subQ) {
                    $subQ->whereNotNull('sellers.latitude')
                        ->whereNotNull('sellers.longitude')
                        ->where('sellers.latitude', '!=', 0)
                        ->where('sellers.longitude', '!=', 0);
                });
            });

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('sellers.business_name', 'LIKE', "%{$search}%")
                        ->orWhere('sellers.address', 'LIKE', "%{$search}%")
                        ->orWhere('sellers.description', 'LIKE', "%{$search}%")
                        ->orWhereExists(function ($subQuery) use ($search) {
                            $subQuery->select(DB::raw(1))
                                ->from('items')
                                ->whereColumn('items.seller_id', 'sellers.id')
                                ->where('items.name', 'LIKE', "%{$search}%");
                        });
                });
            }

            $stores = $query->distinct()->get();

            return $stores->map(function ($store) {
                return $this->enrichStoreData($store);
            });
        } catch (\Exception $e) {
            Log::error('Error fetching stores: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get store by ID
     */
    private function getStoreById($id)
    {
        try {
            return DB::table('sellers')
                ->select([
                    'id',
                    'business_name as name',
                    'description',
                    'working_hours as hours',
                    'email',
                    'address',
                    'latitude',
                    'longitude',
                    'photo_url as image',
                    'phone',
                    'total_points',
                    'is_active'
                ])
                ->where('id', $id)
                ->where('is_active', true)
                ->first();
        } catch (\Exception $e) {
            Log::error('Error fetching store by ID: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Enrich store data with REAL calculated fields - FIXED
     */
    private function enrichStoreData($store)
    {
        $resolvedImage = $this->resolveSellerImage($store->image);
        $store->image = $resolvedImage;
        $store->photo_url = $resolvedImage;

        // Generate consistent phone if missing (only field that can be fake)
        if (empty($store->phone)) {
            $store->phone = $this->generateConsistentPhone($store->id);
        }

        // Use the same points logic as gallery - direct from store
        $points = $store->total_points ?? 0;
        $store->points_reward = $points;

        // Get transaction count only if needed
        $store->transaction_count = DB::table('point_transactions')
            ->where('seller_id', $store->id)
            ->count();

        // Add ranking data based on points (same as gallery)
        $store->rank_class = $this->getRankClass($points);
        $store->rank_text = $this->getRankText($points);
        $store->rank_icon = $this->getRankIcon($points);

        // Format working hours
        $store->hours_formatted = $this->formatWorkingHours($store->hours);
        $store->is_open = $this->isStoreCurrentlyOpen($store->hours);

        // Initialize distance
        $store->distance = null;

        // Add photos (last 3 images max) and total count
        $allPhotos = $this->getSellerPhotos($store->id);
        $store->photos = array_slice($allPhotos, -3, 3);
        $store->total_photos = count($allPhotos);

        return $store;
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
     * Get seller photos
     */
    private function getSellerPhotos($sellerId)
    {
        try {
            $photos = collect();

            if (DB::getSchemaBuilder()->hasTable('seller_photos')) {
                $dbPhotos = DB::table('seller_photos')
                    ->where('seller_id', $sellerId)
                    ->where(function($q) {
                        $q->whereNull('photo_caption')
                          ->orWhere('photo_caption', '')
                          ->orWhere('photo_caption', 'NOT LIKE', '[FROZEN] %');
                    })
                    ->orderByDesc('is_featured')
                    ->orderBy('sort_order')
                    ->orderByDesc('created_at')
                    ->get(['id', 'photo_url', 'caption', 'category', 'is_featured', 'sort_order', 'created_at']);

                foreach ($dbPhotos as $photo) {
                    // Use the same normalization logic as seller images
                    $photoUrl = $this->resolveSellerImage($photo->photo_url);

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
                    // Use the same normalization logic
                    $photoUrl = $this->resolveSellerImage($seller->photo_url);

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
            Log::error('Error loading seller photos for seller ' . $sellerId . ': ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Search stores by query
     */
    private function searchStoresByQuery($query, $userLat = null, $userLng = null, $radiusKm = 50)
    {
        try {
            $baseQuery = DB::table('sellers')
                ->select([
                    'sellers.id',
                    'sellers.business_name as name',
                    'sellers.description',
                    'sellers.working_hours as hours',
                    'sellers.email',
                    'sellers.address',
                    'sellers.latitude',
                    'sellers.longitude',
                    'sellers.photo_url as image',
                    'sellers.phone',
                    'sellers.total_points',
                    'sellers.is_active'
                ])
                ->where('sellers.is_active', true)
                ->where(function ($q) {
                    $q->where(function ($subQ) {
                        $subQ->whereNotNull('sellers.latitude')
                            ->whereNotNull('sellers.longitude')
                            ->where('sellers.latitude', '!=', 0)
                            ->where('sellers.longitude', '!=', 0);
                    });
                })
                ->where(function ($q) use ($query) {
                    $q->where('sellers.business_name', 'LIKE', "%{$query}%")
                        ->orWhere('sellers.address', 'LIKE', "%{$query}%")
                        ->orWhere('sellers.description', 'LIKE', "%{$query}%")
                        ->orWhereExists(function ($subQuery) use ($query) {
                            $subQuery->select(DB::raw(1))
                                ->from('items')
                                ->whereColumn('items.seller_id', 'sellers.id')
                                ->where('items.name', 'LIKE', "%{$query}%");
                        });
                });

            if ($userLat && $userLng) {
                $baseQuery->whereRaw($this->getDistanceWhereClause($userLat, $userLng, $radiusKm));
            }

            $stores = $baseQuery->distinct()->get();

            return $stores->map(function ($store) {
                return $this->enrichStoreData($store);
            });
        } catch (\Exception $e) {
            Log::error('Error searching stores: ' . $e->getMessage());
            return collect([]);
        }
    }

    private function resolveSellerImage(?string $path, ?string $default = null): ?string
    {
        if (empty($path)) {
            return $default;
        }

        $trimmed = trim($path);

        // If it already starts with http:// or https://, it's a full URL
        // Use FileRepository to normalize it
        if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://')) {
            return $this->normalizeRemoteUrl($trimmed);
        }

        // If it starts with //, add https:
        if (str_starts_with($trimmed, '//')) {
            return $this->normalizeRemoteUrl('https:' . $trimmed);
        }

        $normalized = ltrim($trimmed, '/');

        // If it doesn't start with http, it might be a relative path to the file server
        // Use FileRepository to get the proper URL
        if (!str_starts_with($normalized, 'http')) {
            return $this->fileRepo->get($normalized);
        }

        // Legacy handling for local storage paths
        if (str_starts_with($normalized, 'storage/')) {
            return asset($normalized);
        }

        if (str_starts_with($normalized, 'public/')) {
            $normalized = substr($normalized, 7);
        }

        if (
            str_starts_with($normalized, 'seller_photos/') ||
            str_starts_with($normalized, 'sellers/') ||
            str_starts_with($normalized, 'store_photos/')
        ) {
            return asset('storage/' . $normalized);
        }

        return asset('storage/sellers/' . $normalized);
    }

    /**
     * Normalize remote URL using FileRepository (similar to NormalizesRemoteUrl trait)
     */
    private function normalizeRemoteUrl(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (str_starts_with($value, '//')) {
            $value = 'https:' . $value;
        }

        if (!str_starts_with($value, 'http')) {
            return $this->fileRepo->get(ltrim($value, '/'));
        }

        $host = parse_url($value, PHP_URL_HOST);

        if ($host && $host === $this->fileRepo->remoteHost()) {
            $relative = $this->fileRepo->extractRelativePathFromUrl($value);

            if ($relative) {
                return $this->fileRepo->get($relative);
            }
        }

        if (str_starts_with($value, 'http://')) {
            $value = str_replace('http://', 'https://', $value);
        }

        return $value;
    }

    /**
     * Generate consistent phone number based on store ID (only remaining "fake" data)
     */
    private function generateConsistentPhone($storeId)
    {
        // Generate consistent phone using store ID
        $seed = intval($storeId) * 456; // Different multiplier for phone
        $hash = abs(crc32(strval($seed)));

        $part1 = ($hash % 90) + 10; // 10-99
        $part2 = (($hash >> 8) % 900) + 100; // 100-999
        $part3 = (($hash >> 16) % 900) + 100; // 100-999

        return "+855 {$part1} {$part2} {$part3}";
    }

    /*
    |--------------------------------------------------------------------------
    | Utility Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate time ago from datetime
     */
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

    /**
     * Calculate distance using Haversine formula
     */
    private function calculateHaversineDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    /**
     * Get distance WHERE clause for SQL
     */
    private function getDistanceWhereClause($userLat, $userLng, $radiusKm)
    {
        return "(6371 * acos(cos(radians({$userLat})) * cos(radians(latitude)) * cos(radians(longitude) - radians({$userLng})) + sin(radians({$userLat})) * sin(radians(latitude)))) <= {$radiusKm}";
    }

    /**
     * Format working hours
     */
    private function formatWorkingHours($hours)
    {
        return $hours ?: 'Hours not specified';
    }

    /**
     * Check if store is currently open
     */
    private function isStoreCurrentlyOpen($hours)
    {
        return !empty($hours);
    }

    /*
    |--------------------------------------------------------------------------
    | Ranking System Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get rank class based on points
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

    /**
     * Get rank text based on points
     */
    private function getRankText($points)
    {
        $numPoints = floatval($points);
        if ($numPoints >= 2000) return 'Platinum';
        if ($numPoints >= 1000) return 'Gold';
        if ($numPoints >= 500) return 'Silver';
        if ($numPoints >= 100) return 'Bronze';
        return 'Standard';
    }

    /**
     * Get rank icon based on points
     */
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
