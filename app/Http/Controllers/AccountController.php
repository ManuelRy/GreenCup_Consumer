<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function index()
    {
        $consumer = Auth::guard('consumer')->user();
        
        // Get account statistics from point_transactions table
        try {
            // Check if tables exist first
            if (!DB::getSchemaBuilder()->hasTable('point_transactions')) {
                throw new \Exception('Tables not migrated yet');
            }
            
            $totalPointsEarned = DB::table('point_transactions')
                ->where('consumer_id', $consumer->id)
                ->where('type', 'earn')
                ->sum('points') ?? 0;
                
            $totalPointsSpent = DB::table('point_transactions')
                ->where('consumer_id', $consumer->id)
                ->where('type', 'spend')
                ->sum('points') ?? 0;
                
            $availablePoints = $totalPointsEarned - $totalPointsSpent;
            
            // Get transactions with store and item information
            $transactions = DB::table('point_transactions')
                ->leftJoin('sellers', 'point_transactions.seller_id', '=', 'sellers.id')
                ->leftJoin('qr_codes', 'point_transactions.qr_code_id', '=', 'qr_codes.id')
                ->leftJoin('items', 'qr_codes.item_id', '=', 'items.id')
                ->leftJoin('seller_locations', function($join) {
                    $join->on('sellers.id', '=', 'seller_locations.seller_id')
                         ->where('seller_locations.is_primary', true);
                })
                ->where('point_transactions.consumer_id', $consumer->id)
                ->select(
                    'point_transactions.id',
                    'point_transactions.points',
                    'point_transactions.type',
                    'point_transactions.description',
                    'point_transactions.scanned_at as transaction_date',
                    'point_transactions.created_at',
                    'sellers.business_name as store_name',
                    'items.name as item_name',
                    'seller_locations.address as store_location'
                )
                ->orderBy('point_transactions.scanned_at', 'desc')
                ->limit(50)
                ->get();
                
        } catch (\Exception $e) {
            // Fallback data if tables don't exist yet or there's an error
            $totalPointsEarned = 0;
            $totalPointsSpent = 0;
            $availablePoints =0;
            $transactions = collect([]); // Empty collection
        }
        
        return view('account.index', compact(
            'consumer',
            'totalPointsEarned',
            'totalPointsSpent',
            'availablePoints',
            'transactions'  // ← This was missing!
        ));
    }
    
}