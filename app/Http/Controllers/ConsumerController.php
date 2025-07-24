<?php

namespace App\Http\Controllers;

use App\Models\Consumer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

class ConsumerController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Authentication Methods (from LoginController)
    |--------------------------------------------------------------------------
    */
    
    /**
     * Show the login form
     */
    public function showLogin()
    {
        if (Auth::guard('consumer')->check()) {
            return redirect()->route('dashboard');
        }
        return view('consumers.login');
    }

    /**
     * Handle login submission
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->filled('remember_me');

        try {
            if (Auth::guard('consumer')->attempt($credentials, $remember)) {
                $request->session()->regenerate();
                return redirect()->intended(route('dashboard'))->with('success', 'Welcome back!');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'remember_token')) {
                if (Auth::guard('consumer')->attempt($credentials, false)) {
                    $request->session()->regenerate();
                    return redirect()->intended(route('dashboard'))->with('success', 'Welcome back!');
                }
            }
            throw $e;
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.'])->onlyInput('email');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::guard('consumer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Registration Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Show the registration form
     */
    public function showRegister()
    {
        return view('consumers.create');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'full_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:consumers,email',
            'phone_number'  => 'nullable|string|max:20',
            'gender'        => 'required|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'password'      => 'required|string|min:8|confirmed',
        ]);

        $consumer = Consumer::create($data);

        return redirect()->route('login')->with([
            'registration_success' => 'Welcome to GreenCup! Please sign in with your new account.',
            'registration_email' => $consumer->email
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard Methods - UPDATED for Receipt System
    |--------------------------------------------------------------------------
    */

    /**
     * Show dashboard (UPDATED for receipt system)
     */
 public function dashboard(Request $request)
    {
        $consumer = Auth::guard('consumer')->user();
        
        if (!$consumer) {
            return redirect()->route('login');
        }
        
        $selectedMonth = $request->get('month', Carbon::now()->format('F'));
        $year = Carbon::now()->year;
        
        $monthNumber = Carbon::parse("1 $selectedMonth $year")->month;
        
        try {
            // Use DB queries instead of Eloquent relationships
            $totalEarned = DB::table('point_transactions')
                ->where('consumer_id', $consumer->id)
                ->where('type', 'earn')
                ->sum('points') ?? 0;
                
            $totalSpent = DB::table('point_transactions')
                ->where('consumer_id', $consumer->id)
                ->where('type', 'spend')
                ->sum('points') ?? 0;
                
            $availablePoints = $totalEarned - $totalSpent;
            
            $monthlyData = $this->getMonthlyData($consumer->id, $monthNumber, $year);
            
            // Get recent activity data (NEW)
            $recentActivity = $this->getRecentActivityForDashboard($consumer->id);
            
        } catch (\Exception $e) {
            \Log::error('Dashboard error: ' . $e->getMessage());
            $availablePoints = 0;
            $monthlyData = [
                'points_in' => 0,
                'points_out' => 0,
                'prev_points_in' => 0,
                'prev_points_out' => 0,
                'all_activities' => 0,
                'net_flow' => 0,
                'prev_month_name' => Carbon::now()->subMonth()->format('M')
            ];
            $recentActivity = collect([]);
        }
        
        return view('dashboard', compact('consumer', 'availablePoints', 'monthlyData', 'selectedMonth', 'recentActivity'));
    }
     private function getRecentActivityForDashboard($consumerId, $limit = 5)
    {
        $transactions = DB::table('point_transactions as pt')
            ->leftJoin('sellers as s', 's.id', '=', 'pt.seller_id')
            ->leftJoin('pending_transactions as pend', 'pend.receipt_code', '=', 'pt.receipt_code')
            ->where('pt.consumer_id', $consumerId)
            ->select([
                'pt.id',
                'pt.points',
                'pt.type',
                'pt.description',
                'pt.units_scanned',
                'pt.scanned_at',
                'pt.receipt_code',
                's.business_name as store_name',
                'pend.items as receipt_items'
            ])
            ->orderBy('pt.scanned_at', 'desc')
            ->limit($limit)
            ->get();
        
        return $transactions->map(function ($transaction) {
            // Parse receipt items to get activity name
            $activityName = 'Unknown Activity';
            $icon = '🔄';
            
            if ($transaction->receipt_items) {
                $items = json_decode($transaction->receipt_items, true) ?: [];
                if (!empty($items)) {
                    if (count($items) === 1) {
                        $activityName = $items[0]['name'] . ' Purchase';
                        $icon = $this->getActivityIcon($items[0]['name']);
                    } else {
                        $activityName = 'Multi-item Purchase';
                        $icon = '🛒';
                    }
                }
            } elseif ($transaction->description) {
                if (str_contains(strtolower($transaction->description), 'coffee')) {
                    $activityName = 'Coffee Purchase';
                    $icon = '☕';
                } elseif (str_contains(strtolower($transaction->description), 'reward') || str_contains(strtolower($transaction->description), 'redeem')) {
                    $activityName = 'Reward Redeemed';
                    $icon = '🎁';
                } elseif (str_contains(strtolower($transaction->description), 'eco') || str_contains(strtolower($transaction->description), 'green')) {
                    $activityName = 'Eco Action Bonus';
                    $icon = '🌱';
                } else {
                    $activityName = $transaction->type === 'earn' ? 'Points Earned' : 'Points Spent';
                    $icon = $transaction->type === 'earn' ? '💚' : '💸';
                }
            }
            
            // Format time ago
            $timeAgo = Carbon::parse($transaction->scanned_at)->diffForHumans();
            
            return (object) [
                'id' => $transaction->id,
                'name' => $activityName,
                'icon' => $icon,
                'points' => $transaction->points,
                'type' => $transaction->type,
                'time_ago' => $timeAgo,
                'store_name' => $transaction->store_name,
                'receipt_code' => $transaction->receipt_code,
                'description' => $transaction->description
            ];
        });
    }

    /**
     * Get activity icon based on item name (NEW HELPER)
     */
    private function getActivityIcon($itemName)
    {
        $itemName = strtolower($itemName);
        
        if (str_contains($itemName, 'coffee')) return '☕';
        if (str_contains($itemName, 'cup') || str_contains($itemName, 'bottle')) return '🥤';
        if (str_contains($itemName, 'bag')) return '🛍️';
        if (str_contains($itemName, 'straw')) return '🥤';
        if (str_contains($itemName, 'vegetable') || str_contains($itemName, 'fruit')) return '🥬';
        if (str_contains($itemName, 'container')) return '📦';
        if (str_contains($itemName, 'utensil') || str_contains($itemName, 'bamboo')) return '🥢';
        if (str_contains($itemName, 'smoothie') || str_contains($itemName, 'juice')) return '🥤';
        
        return '🛒'; // Default shopping icon
    }

    /*
    |--------------------------------------------------------------------------
    | Account Management Methods - UPDATED for Receipt System
    |--------------------------------------------------------------------------
    */

    /**
     * Show account overview (UPDATED for receipt system)
     */
    public function account()
    {
        $consumer = Auth::guard('consumer')->user();
        
        if (!$consumer) {
            return redirect()->route('login');
        }
        
        try {
            // Check if required tables exist
            if (!DB::getSchemaBuilder()->hasTable('point_transactions')) {
                throw new \Exception('Tables not migrated yet');
            }
            
            // Calculate points summary
            $totalPointsEarned = DB::table('point_transactions')
                ->where('consumer_id', $consumer->id)
                ->where('type', 'earn')
                ->sum('points') ?? 0;
                
            $totalPointsSpent = DB::table('point_transactions')
                ->where('consumer_id', $consumer->id)
                ->where('type', 'spend')
                ->sum('points') ?? 0;
                
            $availablePoints = $totalPointsEarned - $totalPointsSpent;
            
            // Get transaction history with receipt system data
            $transactions = $this->getTransactionHistory($consumer->id);
                
        } catch (\Exception $e) {
            \Log::error('Account page error: ' . $e->getMessage());
            $totalPointsEarned = 0;
            $totalPointsSpent = 0;
            $availablePoints = 0;
            $transactions = collect([]);
        }
        
        return view('account.index', compact(
            'consumer', 'totalPointsEarned', 'totalPointsSpent', 'availablePoints', 'transactions'
        ));
    }

    /**
     * Show account edit form
     */
    public function showEditAccount()
    {
        $consumer = Auth::guard('consumer')->user();
        if (!$consumer) {
            return redirect()->route('login')->with('error', 'Please login to access account settings');
        }
        return view('account.edit', compact('consumer'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $consumer = Auth::guard('consumer')->user();
        if (!$consumer) {
            return redirect()->route('login')->with('error', 'Please login to update your profile');
        }
        
        $data = $request->validate([
            'full_name'     => 'required|string|max:255',
            'phone_number'  => 'nullable|string|max:20',
            'gender'        => 'required|in:male,female,other',
            'date_of_birth' => 'nullable|date|before:today',
        ]);
        
        $consumer->update($data);
        return redirect()->route('account')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $consumer = Auth::guard('consumer')->user();
        if (!$consumer) {
            return redirect()->route('login')->with('error', 'Please login to change your password');
        }
        
        $request->validate([
            'current_password' => 'required|current_password:consumer',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        
        $consumer->update(['password' => Hash::make($request->password)]);
        return redirect()->route('account')->with('success', 'Password updated successfully!');
    }

    /**
     * Transaction history with filtering (UPDATED for receipt system)
     */
    public function transactionHistory(Request $request)
    {
        $consumer = Auth::guard('consumer')->user();
        if (!$consumer) {
            return redirect()->route('login');
        }
        
        // Base query for receipt system
        $query = DB::table('point_transactions as pt')
            ->leftJoin('sellers as s', 's.id', '=', 'pt.seller_id')
            ->leftJoin('pending_transactions as pend', 'pend.receipt_code', '=', 'pt.receipt_code')
            ->where('pt.consumer_id', $consumer->id)
            ->select([
                'pt.id', 'pt.points', 'pt.type', 'pt.description', 'pt.units_scanned',
                'pt.seller_id', 'pt.scanned_at as transaction_date', 'pt.receipt_code',
                'pt.created_at', 's.business_name as store_name', 's.address as store_location',
                'pend.items as receipt_items', 'pend.total_points as receipt_total_points'
            ]);
        
        // Apply filters
        if ($request->filled('type')) {
            $query->where('pt.type', $request->type);
        }
        if ($request->filled('store')) {
            $query->where('s.id', $request->store);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('pt.scanned_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('pt.scanned_at', '<=', $request->date_to);
        }
        
        $transactions = $query->orderBy('pt.scanned_at', 'desc')->paginate(20);
        
        // Process transactions for display
        $transactions->getCollection()->transform(function ($transaction) {
            // Parse receipt items
            $items = [];
            if ($transaction->receipt_items) {
                $items = json_decode($transaction->receipt_items, true) ?: [];
            }
            
            // Determine item name
            $itemName = 'Unknown Item';
            if (!empty($items)) {
                if (count($items) === 1) {
                    $itemName = $items[0]['name'];
                } else {
                    $itemNames = array_column($items, 'name');
                    $itemName = implode(', ', array_slice($itemNames, 0, 2));
                    if (count($itemNames) > 2) {
                        $itemName .= ' +' . (count($itemNames) - 2) . ' more';
                    }
                }
            }
            
            $transaction->item_name = $itemName;
            $transaction->receipt_items_parsed = $items;
            return $transaction;
        });
        
        // Get unique stores for filter dropdown
        $stores = DB::table('point_transactions as pt')
            ->join('sellers as s', 'pt.seller_id', '=', 's.id')
            ->where('pt.consumer_id', $consumer->id)
            ->select('s.id', 's.business_name')
            ->distinct()
            ->orderBy('s.business_name')
            ->get();
        
        return view('account.transactions', compact('consumer', 'transactions', 'stores'));
    }

    /*
    |--------------------------------------------------------------------------
    | QR Code Methods - UPDATED for Receipt System
    |--------------------------------------------------------------------------
    */

    /**
     * Show consumer's QR code (UPDATED for receipt system)
     */
    public function showQrCode()
    {
        $consumer = Auth::guard('consumer')->user();
        if (!$consumer) {
            return redirect()->route('login')->with('error', 'Please log in to view your QR code.');
        }

        try {
            // Get consumer's QR code from qr_codes table if it exists
            $qrCode = DB::table('qr_codes')
                ->where('consumer_id', $consumer->id)
                ->where('type', 'consumer_profile')
                ->first();
                
            // Get recent transactions using DB query
            $recentTransactions = DB::table('point_transactions as pt')
                ->leftJoin('sellers as s', 's.id', '=', 'pt.seller_id')
                ->where('pt.consumer_id', $consumer->id)
                ->select([
                    'pt.points',
                    'pt.type', 
                    'pt.scanned_at',
                    's.business_name as store_name'
                ])
                ->orderBy('pt.scanned_at', 'desc')
                ->limit(5)
                ->get();
                
            // If no QR code exists, create one
            if (!$qrCode) {
                $qrCodeData = [
                    'consumer_id' => $consumer->id,
                    'seller_id' => null,
                    'item_id' => null,
                    'type' => 'consumer_profile',
                    'code' => 'CONSUMER-' . strtoupper(substr(md5($consumer->id . time()), 0, 8)),
                    'active' => true,
                    'expires_at' => null,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                $qrCodeId = DB::table('qr_codes')->insertGetId($qrCodeData);
                $qrCode = (object) array_merge($qrCodeData, ['id' => $qrCodeId]);
            }
            
        } catch (\Exception $e) {
            \Log::error('QR Code page error: ' . $e->getMessage());
            $qrCode = null;
            $recentTransactions = collect([]);
        }

        return view('consumers.qr-code', compact('consumer', 'qrCode', 'recentTransactions'));
    }

    /**
     * Show receipt scanning page
     */
    public function showScanReceipt()
    {
        $consumer = Auth::guard('consumer')->user();
        return view('consumers.scan-receipt', compact('consumer'));
    }

    /*
    |--------------------------------------------------------------------------
    | API Methods - UPDATED for Receipt System
    |--------------------------------------------------------------------------
    */

    /**
     * Get consumer points (API) - UPDATED
     */
    public function getPoints()
    {
        $consumer = Auth::guard('consumer')->user();
        
        if (!$consumer) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        try {
            $totalEarned = DB::table('point_transactions')
                ->where('consumer_id', $consumer->id)
                ->where('type', 'earn')
                ->sum('points') ?? 0;
                
            $totalSpent = DB::table('point_transactions')
                ->where('consumer_id', $consumer->id)
                ->where('type', 'spend')
                ->sum('points') ?? 0;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_earned' => $totalEarned,
                    'total_spent' => $totalSpent,
                    'available' => $totalEarned - $totalSpent
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch points'
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods - NEW for Receipt System
    |--------------------------------------------------------------------------
    */

    /**
     * Get detailed transaction history for account page (NEW METHOD)
     */
    private function getTransactionHistory($consumerId, $limit = 20)
    {
        $transactions = DB::table('point_transactions as pt')
            ->leftJoin('sellers as s', 's.id', '=', 'pt.seller_id')
            ->leftJoin('pending_transactions as pend', 'pend.receipt_code', '=', 'pt.receipt_code')
            ->where('pt.consumer_id', $consumerId)
            ->select([
                'pt.id',
                'pt.points',
                'pt.type',
                'pt.description',
                'pt.units_scanned',
                'pt.scanned_at as transaction_date',
                'pt.receipt_code',
                'pt.created_at',
                's.business_name as store_name',
                's.address as store_location',
                's.phone as store_phone',
                'pend.items as receipt_items',
                'pend.total_points as receipt_total_points',
                'pend.total_quantity as receipt_total_quantity'
            ])
            ->orderBy('pt.scanned_at', 'desc')
            ->limit($limit)
            ->get();
        
        // Process and enhance transaction data
        return $transactions->map(function ($transaction) {
            // Parse receipt items if available
            $items = [];
            if ($transaction->receipt_items) {
                $items = json_decode($transaction->receipt_items, true) ?: [];
            }
            
            // Determine item name from receipt items or description
            $itemName = 'Unknown Item';
            if (!empty($items)) {
                if (count($items) === 1) {
                    $itemName = $items[0]['name'];
                } else {
                    $itemNames = array_column($items, 'name');
                    $itemName = implode(', ', array_slice($itemNames, 0, 2));
                    if (count($itemNames) > 2) {
                        $itemName .= ' +' . (count($itemNames) - 2) . ' more';
                    }
                }
            } elseif ($transaction->description) {
                // Extract item name from description like "Purchased: Coffee, Muffin from Store"
                if (preg_match('/Purchased: (.+?) from/', $transaction->description, $matches)) {
                    $itemName = $matches[1];
                } else {
                    $itemName = 'Receipt Purchase';
                }
            }
            
            // Calculate points per unit
            $pointsPerUnit = $transaction->units_scanned > 0 
                ? round($transaction->points / $transaction->units_scanned, 1)
                : $transaction->points;
            
            // Generate QR code reference for modal display
            $qrCode = $transaction->receipt_code ?: 'TXN-' . str_pad($transaction->id, 6, '0', STR_PAD_LEFT);
            
            return (object) [
                'id' => $transaction->id,
                'item_name' => $itemName,
                'store_name' => $transaction->store_name ?: 'Unknown Store',
                'store_location' => $transaction->store_location ?: 'Location not specified',
                'store_phone' => $transaction->store_phone,
                'transaction_date' => $transaction->transaction_date ?: $transaction->created_at,
                'points' => $transaction->points,
                'type' => $transaction->type,
                'description' => $transaction->description,
                'units_scanned' => $transaction->units_scanned ?: 1,
                'points_per_unit' => $pointsPerUnit,
                'receipt_code' => $transaction->receipt_code,
                'qr_code' => $qrCode, // For modal compatibility
                'code' => $qrCode, // Alternative field name
                'receipt_items' => $items,
                'receipt_total_points' => $transaction->receipt_total_points,
                'receipt_total_quantity' => $transaction->receipt_total_quantity,
                'created_at' => $transaction->created_at
            ];
        });
    }

    /**
     * Get monthly data for dashboard
     */
    private function getMonthlyData($consumerId, $month, $year)
    {
        $pointsIn = DB::table('point_transactions')
            ->where('consumer_id', $consumerId)
            ->where('type', 'earn')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('points');
            
        $pointsOut = DB::table('point_transactions')
            ->where('consumer_id', $consumerId)
            ->where('type', 'spend')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('points');
            
        $prevMonth = $month == 1 ? 12 : $month - 1;
        $prevYear = $month == 1 ? $year - 1 : $year;
        
        $prevPointsIn = DB::table('point_transactions')
            ->where('consumer_id', $consumerId)
            ->where('type', 'earn')
            ->whereMonth('created_at', $prevMonth)
            ->whereYear('created_at', $prevYear)
            ->sum('points');
            
        $prevPointsOut = DB::table('point_transactions')
            ->where('consumer_id', $consumerId)
            ->where('type', 'spend')
            ->whereMonth('created_at', $prevMonth)
            ->whereYear('created_at', $prevYear)
            ->sum('points');
        
        return [
            'points_in' => $pointsIn,
            'points_out' => $pointsOut,
            'prev_points_in' => $prevPointsIn,
            'prev_points_out' => $prevPointsOut,
            'all_activities' => $pointsIn + $pointsOut,
            'net_flow' => $pointsIn - $pointsOut,
            'prev_month_name' => Carbon::create()->month($prevMonth)->format('M')
        ];
    }
}