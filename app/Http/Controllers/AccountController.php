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
            // FIXED: Removed seller_locations join since it doesn't exist in your schema
            $transactions = DB::table('point_transactions')
                ->leftJoin('sellers', 'point_transactions.seller_id', '=', 'sellers.id')
                ->leftJoin('qr_codes', 'point_transactions.qr_code_id', '=', 'qr_codes.id')
                ->leftJoin('items', 'qr_codes.item_id', '=', 'items.id')
                ->where('point_transactions.consumer_id', $consumer->id)
                ->select(
                    'point_transactions.id',
                    'point_transactions.points',
                    'point_transactions.type',
                    'point_transactions.description',
                    'point_transactions.units_scanned',        // Added for modal
                    'point_transactions.seller_id',            // Added for modal
                    'point_transactions.scanned_at as transaction_date',
                    'point_transactions.created_at',
                    'sellers.business_name as store_name',
                    'sellers.address as store_location', // FIXED: Use sellers.address directly
                    'items.name as item_name',
                    'items.id as item_id',                     // Added for modal
                    'items.points_per_unit',                   // Added for modal
                    'qr_codes.code as qr_code'                 // Added for modal
                )
                ->orderBy('point_transactions.scanned_at', 'desc')
                ->limit(50)
                ->get();
                
        } catch (\Exception $e) {
            // Fallback data if tables don't exist yet or there's an error
            $totalPointsEarned = 0;
            $totalPointsSpent = 0;
            $availablePoints = 0;
            $transactions = collect([]); // Empty collection
        }
        
        return view('account.index', compact(
            'consumer',
            'totalPointsEarned',
            'totalPointsSpent',
            'availablePoints',
            'transactions'
        ));
    }
    
    /**
     * Show the form for updating account settings
     */
    public function edit()
    {
        $consumer = Auth::guard('consumer')->user();
        
        if (!$consumer) {
            return redirect()->route('login')
                ->with('error', 'Please login to access account settings');
        }
        
        return view('account.edit', compact('consumer'));
    }
    
    /**
     * Update consumer profile information
     */
    public function updateProfile(Request $request)
    {
        $consumer = Auth::guard('consumer')->user();
        
        if (!$consumer) {
            return redirect()->route('login')
                ->with('error', 'Please login to update your profile');
        }
        
        $data = $request->validate([
            'full_name'     => 'required|string|max:255',
            'phone_number'  => 'nullable|string|max:20',
            'gender'        => 'required|in:male,female,other',
            'date_of_birth' => 'nullable|date|before:today',
        ]);
        
        $consumer->update($data);
        
        return redirect()
            ->route('account.index')
            ->with('success', 'Profile updated successfully!');
    }
    
    /**
     * Update consumer password
     */
    public function updatePassword(Request $request)
    {
        $consumer = Auth::guard('consumer')->user();
        
        if (!$consumer) {
            return redirect()->route('login')
                ->with('error', 'Please login to change your password');
        }
        
        $request->validate([
            'current_password' => 'required|current_password:consumer',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        
        $consumer->update([
            'password' => Hash::make($request->password)
        ]);
        
        return redirect()
            ->route('account.index')
            ->with('success', 'Password updated successfully!');
    }
    
    /**
     * Get consumer's transaction history with filtering
     */
    public function transactionHistory(Request $request)
    {
        $consumer = Auth::guard('consumer')->user();
        
        if (!$consumer) {
            return redirect()->route('login');
        }
        
        $query = DB::table('point_transactions')
            ->leftJoin('sellers', 'point_transactions.seller_id', '=', 'sellers.id')
            ->leftJoin('qr_codes', 'point_transactions.qr_code_id', '=', 'qr_codes.id')
            ->leftJoin('items', 'qr_codes.item_id', '=', 'items.id')
            ->where('point_transactions.consumer_id', $consumer->id)
            ->select(
                'point_transactions.id',
                'point_transactions.points',
                'point_transactions.type',
                'point_transactions.description',
                'point_transactions.units_scanned',
                'point_transactions.seller_id',
                'point_transactions.scanned_at as transaction_date',
                'point_transactions.created_at',
                'sellers.business_name as store_name',
                'sellers.address as store_location',
                'items.name as item_name',
                'items.id as item_id',
                'items.points_per_unit',
                'qr_codes.code as qr_code'
            );
        
        // Apply filters if provided
        if ($request->filled('type')) {
            $query->where('point_transactions.type', $request->type);
        }
        
        if ($request->filled('store')) {
            $query->where('sellers.id', $request->store);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('point_transactions.scanned_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('point_transactions.scanned_at', '<=', $request->date_to);
        }
        
        $transactions = $query
            ->orderBy('point_transactions.scanned_at', 'desc')
            ->paginate(20);
        
        // Get unique stores for filter dropdown
        $stores = DB::table('point_transactions')
            ->join('sellers', 'point_transactions.seller_id', '=', 'sellers.id')
            ->where('point_transactions.consumer_id', $consumer->id)
            ->select('sellers.id', 'sellers.business_name')
            ->distinct()
            ->orderBy('sellers.business_name')
            ->get();
        
        return view('account.transactions', compact('consumer', 'transactions', 'stores'));
    }
}