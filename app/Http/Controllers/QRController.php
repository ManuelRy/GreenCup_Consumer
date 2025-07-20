<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QRController extends Controller
{
    public function processAndConfirm(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string'
        ]);
        
        $consumer = Auth::guard('consumer')->user();
        
        if (!$consumer) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }
        
        try {
            DB::beginTransaction();
            
            $qrData = $request->input('qr_data');
            
            // Try to decode JSON QR data first
            $decodedData = json_decode($qrData, true);
            
            if ($decodedData && isset($decodedData['seller_id'], $decodedData['item_id'])) {
                // Structured QR code with seller and item info
                $sellerId = $decodedData['seller_id'];
                $itemId = $decodedData['item_id'];
                $qrCodeRecord = null;
            } else {
                // Simple QR code - look up in qr_codes table
                $qrCodeRecord = DB::table('qr_codes')
                    ->where('code', $qrData)
                    ->where('active', true)
                    ->where(function($query) {
                        $query->whereNull('expires_at')
                              ->orWhere('expires_at', '>', now());
                    })
                    ->first();
                
                if (!$qrCodeRecord) {
                    throw new \Exception('Invalid or expired QR code');
                }
                
                $sellerId = $qrCodeRecord->seller_id;
                $itemId = $qrCodeRecord->item_id;
            }
            
            // Get seller details
            $seller = DB::table('sellers')
                ->leftJoin('seller_locations', function($join) {
                    $join->on('sellers.id', '=', 'seller_locations.seller_id')
                         ->where('seller_locations.is_primary', true);
                })
                ->where('sellers.id', $sellerId)
                ->select(
                    'sellers.*',
                    'seller_locations.address as location'
                )
                ->first();
                
            // Get item details
            $item = DB::table('items')
                ->where('id', $itemId)
                ->first();
            
            if (!$seller || !$item) {
                throw new \Exception('Seller or item not found');
            }
            
            // Get consumer's current points
            $currentPoints = DB::table('point_transactions')
                ->where('consumer_id', $consumer->id)
                ->sum(DB::raw('CASE WHEN type = "earn" THEN points ELSE -points END'));
            
            // Check for duplicate recent scans (prevent double scanning)
            $qrCodeId = $qrCodeRecord ? $qrCodeRecord->id : 1; // Default fallback
            
            $recentTransaction = DB::table('point_transactions')
                ->where('consumer_id', $consumer->id)
                ->where('seller_id', $sellerId)
                ->where('qr_code_id', $qrCodeId)
                ->where('created_at', '>=', now()->subMinutes(5))
                ->first();
                
            if ($recentTransaction) {
                throw new \Exception('This QR code was already scanned recently. Please wait a few minutes before scanning again.');
            }
            
            // Create transaction record
            $transactionId = DB::table('point_transactions')->insertGetId([
                'consumer_id' => $consumer->id,
                'seller_id' => $sellerId,
                'qr_code_id' => $qrCodeId,
                'units_scanned' => 1,
                'points' => $item->points_per_unit,
                'type' => 'earn',
                'description' => "Purchase of {$item->name} from {$seller->business_name}",
                'scanned_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            DB::commit();
            
            // Calculate new total points
            $newTotalPoints = $currentPoints + $item->points_per_unit;
            
            return response()->json([
                'success' => true,
                'message' => 'Transaction completed successfully!',
                'transaction_id' => $transactionId,
                'current_points' => $currentPoints,
                'points_earned' => $item->points_per_unit,
                'new_total_points' => $newTotalPoints,
                'seller' => [
                    'id' => $seller->id,
                    'business_name' => $seller->business_name,
                    'location' => $seller->location,
                    'description' => $seller->description
                ],
                'item' => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'points_per_unit' => $item->points_per_unit
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    // Keep your existing methods for backward compatibility
    public function processScan(Request $request)
    {
        // Your existing processScan method
        return $this->processAndConfirm($request);
    }
    
    public function confirmTransaction(Request $request)
    {
        // Your existing confirmTransaction method
        // This can be used for two-step process if needed
    }
    
    public function showSellerDetails($sellerId, Request $request)
    {
        // Your existing showSellerDetails method
        $consumer = Auth::guard('consumer')->user();
        
        if (!$consumer) {
            return redirect()->route('login')->with('error', 'Please login to continue');
        }
        
        $seller = DB::table('sellers')
            ->leftJoin('seller_locations', function($join) {
                $join->on('sellers.id', '=', 'seller_locations.seller_id')
                     ->where('seller_locations.is_primary', true);
            })
            ->where('sellers.id', $sellerId)
            ->select(
                'sellers.*',
                'seller_locations.address as location'
            )
            ->first();
            
        if (!$seller) {
            abort(404, 'Seller not found');
        }
        
        $item = null;
        if ($request->has('item_id')) {
            $item = DB::table('items')
                ->where('id', $request->input('item_id'))
                ->first();
        }
        
        $photos = DB::table('seller_photos')
            ->where('seller_id', $sellerId)
            ->orderBy('is_featured', 'desc')
            ->get();
            
        $previousTransactions = DB::table('point_transactions')
            ->where('consumer_id', $consumer->id)
            ->where('seller_id', $sellerId)
            ->where('type', 'earn')
            ->count();
            
        return view('seller.details', compact('seller', 'photos', 'previousTransactions', 'consumer', 'item'));
    }
}