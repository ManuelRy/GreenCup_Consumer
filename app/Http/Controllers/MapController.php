<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MapController extends Controller
{
    /**
     * Display the store locator map page
     */
    public function index()
    {
        $consumer = Auth::guard('consumer')->user();
        
        if (!$consumer) {
            return redirect()->route('login')
                ->with('error', 'Please login to access store locator');
        }

        try {
            // Get stores with locations for initial map load
            $stores = $this->getAllStores();
            
            return view('map.index', [
                'stores' => $stores,
                'consumer' => $consumer,
                'mapboxToken' => 'pk.eyJ1IjoibmVha3NlbmJlc3RmcmkiLCJhIjoiY205cXhkb3c3MTF3MzJ2b2doamJiM2NmaSJ9.zTnzZvYetGaqX0CODz4qoQ'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Map page error: ' . $e->getMessage());
            
            return redirect()->route('dashboard')
                ->with('error', 'Unable to load store locator. Please try again.');
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
     * Get specific store details
     */
    public function getStoreDetails($id)
    {
        try {
            $store = $this->getStoreById($id);
            
            if (!$store) {
                return response()->json([
                    'success' => false,
                    'message' => 'Store not found'
                ], 404);
            }

            // Get additional store data
            $storeDetails = $this->enrichStoreData($store);
            
            return response()->json([
                'success' => true,
                'data' => $storeDetails
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get store details error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch store details'
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
                return response()->json([
                    'success' => false,
                    'message' => 'Store location not available'
                ], 404);
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
            
            return response()->json([
                'success' => false,
                'message' => 'Unable to calculate distance'
            ], 500);
        }
    }

    /**
     * Search stores by location or name
     */
    public function searchStores(Request $request)
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
            
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'data' => []
            ], 500);
        }
    }

    /**
     * Get all stores from database
     */
    private function getAllStores($search = null)
    {
        $query = DB::table('sellers')
            ->join('seller_locations', function($join) {
                $join->on('sellers.id', '=', 'seller_locations.seller_id')
                     ->where('seller_locations.is_primary', true);
            })
            ->leftJoin('seller_photos', function($join) {
                $join->on('sellers.id', '=', 'seller_photos.seller_id')
                     ->where('seller_photos.is_featured', true);
            })
            ->select([
                'sellers.id',
                'sellers.business_name as name',
                'sellers.description',
                'sellers.working_hours as hours',
                'sellers.email',
                'seller_locations.address',
                'seller_locations.latitude',
                'seller_locations.longitude',
                'seller_photos.url as image',
                DB::raw('COALESCE(seller_photos.url, NULL) as has_image')
            ])
            ->whereNotNull('seller_locations.latitude')
            ->whereNotNull('seller_locations.longitude');

        // Add search functionality
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('sellers.business_name', 'LIKE', "%{$search}%")
                  ->orWhere('seller_locations.address', 'LIKE', "%{$search}%")
                  ->orWhere('sellers.description', 'LIKE', "%{$search}%");
            });
        }

        $stores = $query->get();

        // Enrich each store with additional computed data
        return $stores->map(function($store) {
            return $this->enrichStoreData($store);
        });
    }

    /**
     * Get single store by ID
     */
    private function getStoreById($id)
    {
        return DB::table('sellers')
            ->join('seller_locations', function($join) {
                $join->on('sellers.id', '=', 'seller_locations.seller_id')
                     ->where('seller_locations.is_primary', true);
            })
            ->leftJoin('seller_photos', function($join) {
                $join->on('sellers.id', '=', 'seller_photos.seller_id')
                     ->where('seller_photos.is_featured', true);
            })
            ->select([
                'sellers.id',
                'sellers.business_name as name',
                'sellers.description',
                'sellers.working_hours as hours',
                'sellers.email',
                'seller_locations.address',
                'seller_locations.latitude',
                'seller_locations.longitude',
                'seller_photos.url as image'
            ])
            ->where('sellers.id', $id)
            ->whereNotNull('seller_locations.latitude')
            ->whereNotNull('seller_locations.longitude')
            ->first();
    }

    /**
     * Enrich store data with computed fields
     */
    private function enrichStoreData($store)
    {
        // Generate demo phone (replace with real phone field from database)
        $store->phone = $this->generateDemoPhone();
        
        // Generate demo rating (replace with real rating calculation)
        $store->rating = $this->calculateStoreRating($store->id);
        
        // Get transaction count for this store
        $store->transaction_count = $this->getStoreTransactionCount($store->id);
        
        // Format working hours
        $store->hours_formatted = $this->formatWorkingHours($store->hours);
        
        // Check if store is currently open
        $store->is_open = $this->isStoreCurrentlyOpen($store->hours);
        
        // Get all photos for this store
        $store->photos = $this->getStorePhotos($store->id);
        
        // Initialize distance (will be calculated by frontend)
        $store->distance = 0;
        
        return $store;
    }

    /**
     * Search stores by query with optional location filtering
     */
    private function searchStoresByQuery($query, $userLat = null, $userLng = null, $radiusKm = 50)
    {
        $baseQuery = DB::table('sellers')
            ->join('seller_locations', function($join) {
                $join->on('sellers.id', '=', 'seller_locations.seller_id')
                     ->where('seller_locations.is_primary', true);
            })
            ->leftJoin('seller_photos', function($join) {
                $join->on('sellers.id', '=', 'seller_photos.seller_id')
                     ->where('seller_photos.is_featured', true);
            })
            ->select([
                'sellers.id',
                'sellers.business_name as name',
                'sellers.description',
                'sellers.working_hours as hours',
                'sellers.email',
                'seller_locations.address',
                'seller_locations.latitude',
                'seller_locations.longitude',
                'seller_photos.url as image'
            ])
            ->whereNotNull('seller_locations.latitude')
            ->whereNotNull('seller_locations.longitude')
            ->where(function($q) use ($query) {
                $q->where('sellers.business_name', 'LIKE', "%{$query}%")
                  ->orWhere('seller_locations.address', 'LIKE', "%{$query}%")
                  ->orWhere('sellers.description', 'LIKE', "%{$query}%");
            });

        // Add location-based filtering if user location provided
        if ($userLat && $userLng) {
            $baseQuery->whereRaw(
                $this->getDistanceWhereClause($userLat, $userLng, $radiusKm)
            );
        }

        $stores = $baseQuery->get();

        return $stores->map(function($store) {
            return $this->enrichStoreData($store);
        });
    }

    /**
     * Calculate Haversine distance between two points
     */
    private function calculateHaversineDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat/2) * sin($dLat/2) + 
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
             sin($dLng/2) * sin($dLng/2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }

    /**
     * Generate WHERE clause for distance-based filtering
     */
    private function getDistanceWhereClause($userLat, $userLng, $radiusKm)
    {
        return "
            (6371 * acos(
                cos(radians({$userLat})) * 
                cos(radians(seller_locations.latitude)) * 
                cos(radians(seller_locations.longitude) - radians({$userLng})) + 
                sin(radians({$userLat})) * 
                sin(radians(seller_locations.latitude))
            )) <= {$radiusKm}
        ";
    }

    /**
     * Generate demo phone number (replace with real database field)
     */
    private function generateDemoPhone()
    {
        return '+855 ' . rand(10, 99) . ' ' . rand(100, 999) . ' ' . rand(100, 999);
    }

    /**
     * Calculate store rating based on transactions
     */
    private function calculateStoreRating($storeId)
    {
        // For now, return demo rating
        // TODO: Implement real rating based on customer feedback
        return round(rand(35, 50) / 10, 1); // 3.5 to 5.0
    }

    /**
     * Get transaction count for store
     */
    private function getStoreTransactionCount($storeId)
    {
        return DB::table('point_transactions')
            ->where('seller_id', $storeId)
            ->where('type', 'earn')
            ->count();
    }

    /**
     * Format working hours for display
     */
    private function formatWorkingHours($hours)
    {
        if (!$hours) {
            return 'Hours not specified';
        }
        
        return $hours;
    }

    /**
     * Check if store is currently open
     */
    private function isStoreCurrentlyOpen($hours)
    {
        // For demo purposes, randomly return true/false
        // TODO: Implement real opening hours logic
        return rand(0, 1) === 1;
    }

    /**
     * Get all photos for a store
     */
    private function getStorePhotos($storeId)
    {
        return DB::table('seller_photos')
            ->where('seller_id', $storeId)
            ->select('url', 'caption', 'is_featured')
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}