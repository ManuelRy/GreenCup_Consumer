<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReceiptController extends Controller
{
    /**
     * Check if a receipt code is valid and return its details
     */
    public function check(Request $request)
    {
        $request->validate([
            'receipt_code' => 'required|string'
        ]);
        
        $consumer = Auth::guard('consumer')->user();
        
        if (!$consumer) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }
        
        try {
            $receiptCode = $request->input('receipt_code');
            
            // Find the pending transaction
            $transaction = DB::table('pending_transactions')
                ->where('receipt_code', $receiptCode)
                ->first();
                
            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid receipt code'
                ]);
            }
            
            // Check if expired
            if ($transaction->expires_at && Carbon::parse($transaction->expires_at)->isPast()) {
                // Update status to expired
                DB::table('pending_transactions')
                    ->where('id', $transaction->id)
                    ->update(['status' => 'expired']);
                    
                return response()->json([
                    'success' => false,
                    'message' => 'This receipt has expired'
                ]);
            }
            
            // Get seller information
            $seller = DB::table('sellers')
                ->where('id', $transaction->seller_id)
                ->first();
                
            if (!$seller) {
                return response()->json([
                    'success' => false,
                    'message' => 'Store not found'
                ]);
            }
            
            // Parse items data
            $items = json_decode($transaction->items, true);
            
            // Build response
            $receiptData = [
                'receipt_code' => $transaction->receipt_code,
                'store_name' => $seller->business_name,
                'store_address' => $seller->address,
                'status' => $transaction->status,
                'total_points' => $transaction->total_points,
                'total_quantity' => $transaction->total_quantity,
                'items' => array_map(function($item) {
                    return [
                        'name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'points_per_unit' => $item['points_per_unit'],
                        'total_points' => $item['quantity'] * $item['points_per_unit']
                    ];
                }, $items),
                'created_at' => Carbon::parse($transaction->created_at)->format('M d, Y g:i A'),
                'expires_at' => $transaction->expires_at ? Carbon::parse($transaction->expires_at)->format('M d, Y g:i A') : null
            ];
            
            return response()->json([
                'success' => true,
                'receipt' => $receiptData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Receipt check error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error checking receipt'
            ], 500);
        }
    }
    
    /**
     * Claim points from a receipt
     */
    public function claim(Request $request)
    {
        $request->validate([
            'receipt_code' => 'required|string'
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
            
            $receiptCode = $request->input('receipt_code');
            
            // Find and lock the transaction
            $transaction = DB::table('pending_transactions')
                ->where('receipt_code', $receiptCode)
                ->lockForUpdate()
                ->first();
                
            if (!$transaction) {
                throw new \Exception('Invalid receipt code');
            }
            
            // Check if already claimed
            if ($transaction->status === 'claimed') {
                throw new \Exception('This receipt has already been claimed');
            }
            
            // Check if expired
            if ($transaction->expires_at && Carbon::parse($transaction->expires_at)->isPast()) {
                DB::table('pending_transactions')
                    ->where('id', $transaction->id)
                    ->update(['status' => 'expired']);
                    
                throw new \Exception('This receipt has expired');
            }
            
            // Get seller info
            $seller = DB::table('sellers')
                ->where('id', $transaction->seller_id)
                ->first();
                
            if (!$seller) {
                throw new \Exception('Store not found');
            }
            
            // Parse items for transaction description
            $items = json_decode($transaction->items, true);
            $itemNames = array_column($items, 'name');
            $description = 'Purchased: ' . implode(', ', array_slice($itemNames, 0, 3));
            if (count($itemNames) > 3) {
                $description .= ' and ' . (count($itemNames) - 3) . ' more items';
            }
            
            // Create point transaction for consumer
            $pointTransactionId = DB::table('point_transactions')->insertGetId([
                'consumer_id' => $consumer->id,
                'seller_id' => $transaction->seller_id,
                'qr_code_id' => null, // No QR code record for new receipt system
                'units_scanned' => $transaction->total_quantity,
                'points' => $transaction->total_points,
                'type' => 'earn',
                'description' => $description . ' from ' . $seller->business_name,
                'scanned_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Update pending transaction status
            DB::table('pending_transactions')
                ->where('id', $transaction->id)
                ->update([
                    'status' => 'claimed',
                    'claimed_at' => now(),
                    'claimed_by_consumer_id' => $consumer->id,
                    'updated_at' => now()
                ]);
            
            // Update seller's total points (optional - represents points given out)
            DB::table('sellers')
                ->where('id', $transaction->seller_id)
                ->increment('total_points', $transaction->total_points);
            
            DB::commit();
            
            // Get consumer's new total points
            $totalEarned = DB::table('point_transactions')
                ->where('consumer_id', $consumer->id)
                ->where('type', 'earn')
                ->sum('points');
                
            $totalSpent = DB::table('point_transactions')
                ->where('consumer_id', $consumer->id)
                ->where('type', 'spend')
                ->sum('points');
                
            $newBalance = $totalEarned - $totalSpent;
            
            return response()->json([
                'success' => true,
                'message' => 'Points claimed successfully!',
                'points_earned' => $transaction->total_points,
                'new_balance' => $newBalance,
                'transaction_id' => $pointTransactionId,
                'seller' => [
                    'name' => $seller->business_name,
                    'address' => $seller->address
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Receipt claim error: ' . $e->getMessage(), [
                'consumer_id' => $consumer->id,
                'receipt_code' => $request->input('receipt_code')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get consumer's receipt history
     */
    public function history(Request $request)
    {
        $consumer = Auth::guard('consumer')->user();
        
        if (!$consumer) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }
        
        try {
            $receipts = DB::table('pending_transactions as pt')
                ->join('sellers as s', 's.id', '=', 'pt.seller_id')
                ->where('pt.claimed_by_consumer_id', $consumer->id)
                ->where('pt.status', 'claimed')
                ->select(
                    'pt.receipt_code',
                    'pt.total_points',
                    'pt.total_quantity',
                    'pt.claimed_at',
                    's.business_name',
                    's.address',
                    'pt.items'
                )
                ->orderBy('pt.claimed_at', 'desc')
                ->limit(20)
                ->get();
                
            // Parse items for each receipt
            $receipts = $receipts->map(function($receipt) {
                $receipt->items = json_decode($receipt->items, true);
                $receipt->claimed_at_formatted = Carbon::parse($receipt->claimed_at)->format('M d, Y g:i A');
                $receipt->claimed_at_relative = Carbon::parse($receipt->claimed_at)->diffForHumans();
                return $receipt;
            });
            
            return response()->json([
                'success' => true,
                'receipts' => $receipts
            ]);
            
        } catch (\Exception $e) {
            Log::error('Receipt history error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching receipt history'
            ], 500);
        }
    }
}