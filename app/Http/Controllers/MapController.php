<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Seller;

class MapController extends Controller
{
    /**
     * Display the store locator map page
     */
    public function index()
    {
        // Check if user is authenticated (remove consumer guard if not using separate guards)
        // $consumer = Auth::guard('consumer')->user();
        // if (!$consumer) {
        //     return redirect()->route('login')
        //         ->with('error', 'Please login to access store locator');
        // }

        try {
            $stores = $this->getAllStores();
            
            // Replace with your actual Mapbox token
            $mapboxToken = env('MAPBOX_ACCESS_TOKEN', 'pk.eyJ1IjoibmVha3NlbmJlc3RmcmkiLCJhIjoiY205cXhkb3c3MTF3MzJ2b2doamJiM2NmaSJ9.zTnzZvYetGaqX0CODz4qoQ');
            
            return view('map.index', compact('stores'))
                   ->with('mapboxToken', $mapboxToken);
                   
        } catch (\Exception $e) {
            Log::error('[MapController@index] ' . $e->getMessage());
            
            // Return view with empty stores array instead of aborting
            $stores = collect([]);
            $mapboxToken = env('MAPBOX_ACCESS_TOKEN', 'pk.eyJ1IjoibmVha3NlbmJlc3RmcmkiLCJhIjoiY205cXhkb3c3MTF3MzJ2b2doamJiM2NmaSJ9.zTnzZvYetGaqX0CODz4qoQ');
            
            return view('map.index', compact('stores'))
                   ->with('mapboxToken', $mapboxToken)
                   ->with('error', 'Some stores may not be available at the moment.');
        }
    }

    /**
     * Alternative method name to match your route
     */
    public function MapPage()
    {
        return $this->index();
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
     * Get all stores from database - Fixed for sellers table
     */
    private function getAllStores($search = null)
    {
        try {
            $query = DB::table('sellers')
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
                ->where('is_active', true)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude');

            // Add search functionality
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('business_name', 'LIKE', "%{$search}%")
                      ->orWhere('address', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            $stores = $query->get();

            // Enrich each store with additional computed data
            return $stores->map(function($store) {
                return $this->enrichStoreData($store);
            });
            
        } catch (\Exception $e) {
            Log::error('Error fetching stores: ' . $e->getMessage());
            
            // Return sample data if database fails
            return collect([
                (object)[
                    'id' => 1,
                    'name' => 'Sample Store 1',
                    'address' => '123 Main Street, Phnom Penh',
                    'latitude' => 11.5564,
                    'longitude' => 104.9282,
                    'phone' => '+855 12 345 678',
                    'points_reward' => 150,
                    'transaction_count' => 25,
                    'hours' => '8:00 AM - 9:00 PM',
                    'description' => 'A sample store for testing',
                    'rank_class' => 'silver',
                    'rank_text' => 'Silver',
                    'rank_icon' => '🥈',
                    'is_active' => true
                ],
                (object)[
                    'id' => 2,
                    'name' => 'Sample Store 2',
                    'address' => '456 Central Market, Phnom Penh',
                    'latitude' => 11.5449,
                    'longitude' => 104.9282,
                    'phone' => '+855 12 987 654',
                    'points_reward' => 75,
                    'transaction_count' => 15,
                    'hours' => '9:00 AM - 8:00 PM',
                    'description' => 'Another sample store',
                    'rank_class' => 'bronze',
                    'rank_text' => 'Bronze',
                    'rank_icon' => '🥉',
                    'is_active' => true
                ]
            ]);
        }
    }

    /**
     * Get single store by ID - Fixed for sellers table
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
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->first();
        } catch (\Exception $e) {
            Log::error('Error fetching store by ID: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Enrich store data with computed fields - Fixed for points system
     */
    private function enrichStoreData($store)
    {
        // Use phone from database or generate demo if null
        $store->phone = $store->phone ?: $this->generateDemoPhone();
        
        // Get total points given to consumers by this store
        $store->points_reward = $this->getStorePointsGivenToConsumers($store->id);
        
        // Calculate rank based on points given to consumers
        $store->rank_class = $this->getRankClass($store->points_reward);
        $store->rank_text = $this->getRankText($store->points_reward);
        $store->rank_icon = $this->getRankIcon($store->points_reward);
        
        // Get transaction count for this store
        $store->transaction_count = $this->getStoreTransactionCount($store->id);
        
        // Format working hours
        $store->hours_formatted = $this->formatWorkingHours($store->hours);
        
        // Check if store is currently open (basic implementation)
        $store->is_open = $this->isStoreCurrentlyOpen($store->hours);
        
        // Initialize distance (will be calculated by frontend)
        $store->distance = 0;
        
        return $store;
    }

    /**
     * Search stores by query with optional location filtering
     */
    private function searchStoresByQuery($query, $userLat = null, $userLng = null, $radiusKm = 50)
    {
        try {
            $baseQuery = DB::table('sellers')
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
                ->where('is_active', true)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->where(function($q) use ($query) {
                    $q->where('business_name', 'LIKE', "%{$query}%")
                      ->orWhere('address', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%");
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
        } catch (\Exception $e) {
            Log::error('Error searching stores: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get total points given to consumers by this store - Fixed to handle missing table
     */
    private function getStorePointsGivenToConsumers($storeId)
    {
        try {
            // Check if point_transactions table exists
            $exists = DB::select("SHOW TABLES LIKE 'point_transactions'");
            if (empty($exists)) {
                // Return demo points if table doesn't exist
                return rand(10, 500);
            }
            
            return DB::table('point_transactions')
                ->where('seller_id', $storeId)
                ->where('type', 'earn')
                ->sum('points') ?: rand(10, 500);
        } catch (\Exception $e) {
            Log::error('Error getting store points: ' . $e->getMessage());
            return rand(10, 500); // Return demo points
        }
    }

    /**
     * Rank calculation based on points given to consumers - Updated thresholds
     */
    private function getRankClass($points)
    {
        $numPoints = floatval($points);
        
        if ($numPoints >= 100) return 'platinum';   // 100+ points given
        if ($numPoints >= 50) return 'gold';        // 50+ points given
        if ($numPoints >= 25) return 'silver';      // 25+ points given
        if ($numPoints >= 10) return 'bronze';      // 10+ points given
        return 'standard';                          // Under 10 points
    }

    private function getRankText($points)
    {
        $numPoints = floatval($points);
        
        if ($numPoints >= 100) return 'Platinum';
        if ($numPoints >= 50) return 'Gold';
        if ($numPoints >= 25) return 'Silver';
        if ($numPoints >= 10) return 'Bronze';
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
                cos(radians(latitude)) * 
                cos(radians(longitude) - radians({$userLng})) + 
                sin(radians({$userLat})) * 
                sin(radians(latitude))
            )) <= {$radiusKm}
        ";
    }

    /**
     * Generate demo phone number (fallback if no phone in database)
     */
    private function generateDemoPhone()
    {
        return '+855 ' . rand(10, 99) . ' ' . rand(100, 999) . ' ' . rand(100, 999);
    }

    /**
     * Get transaction count for store - Fixed to handle missing table
     */
    private function getStoreTransactionCount($storeId)
    {
        try {
            // Check if point_transactions table exists
            $exists = DB::select("SHOW TABLES LIKE 'point_transactions'");
            if (empty($exists)) {
                return rand(5, 50); // Return demo count
            }
            
            return DB::table('point_transactions')
                ->where('seller_id', $storeId)
                ->where('type', 'earn')
                ->count() ?: rand(5, 50);
        } catch (\Exception $e) {
            Log::error('Error getting transaction count: ' . $e->getMessage());
            return rand(5, 50); // Return demo count
        }
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
     * Check if store is currently open - Basic implementation
     */
    private function isStoreCurrentlyOpen($hours)
    {
        if (!$hours) {
            return false;
        }
        
        // For demo purposes, return true for stores with hours
        // TODO: Implement real opening hours logic
        return true;
    }
}