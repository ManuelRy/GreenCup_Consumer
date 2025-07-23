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
    | Dashboard Methods (from DashboardController)
    |--------------------------------------------------------------------------
    */

    /**
     * Show dashboard
     */
    public function dashboard(Request $request)
    {
        $consumer = Auth::guard('consumer')->user();
        $selectedMonth = $request->get('month', Carbon::now()->format('F'));
        $year = Carbon::now()->year;
        
        $monthNumber = Carbon::parse("1 $selectedMonth $year")->month;
        
        $totalEarned = $consumer->pointTransactions()->where('type', 'earn')->sum('points');
        $totalSpent = $consumer->pointTransactions()->where('type', 'spend')->sum('points');
        $availablePoints = $totalEarned - $totalSpent;
        
        $monthlyData = $this->getMonthlyData($consumer->id, $monthNumber, $year);
        
        return view('dashboard', compact('consumer', 'availablePoints', 'monthlyData', 'selectedMonth'));
    }

    /*
    |--------------------------------------------------------------------------
    | Account Management Methods (from AccountController)
    |--------------------------------------------------------------------------
    */

    /**
     * Show account overview
     */
    public function account()
    {
        $consumer = Auth::guard('consumer')->user();
        
        try {
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
                )
                ->orderBy('point_transactions.scanned_at', 'desc')
                ->limit(50)
                ->get();
                
        } catch (\Exception $e) {
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
     * Transaction history with filtering
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
                'point_transactions.id', 'point_transactions.points', 'point_transactions.type',
                'point_transactions.description', 'point_transactions.units_scanned',
                'point_transactions.seller_id', 'point_transactions.scanned_at as transaction_date',
                'point_transactions.created_at', 'sellers.business_name as store_name',
                'sellers.address as store_location', 'items.name as item_name',
                'items.id as item_id', 'items.points_per_unit', 'qr_codes.code as qr_code'
            );
        
        if ($request->filled('type')) $query->where('point_transactions.type', $request->type);
        if ($request->filled('store')) $query->where('sellers.id', $request->store);
        if ($request->filled('date_from')) $query->whereDate('point_transactions.scanned_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('point_transactions.scanned_at', '<=', $request->date_to);
        
        $transactions = $query->orderBy('point_transactions.scanned_at', 'desc')->paginate(20);
        
        $stores = DB::table('point_transactions')
            ->join('sellers', 'point_transactions.seller_id', '=', 'sellers.id')
            ->where('point_transactions.consumer_id', $consumer->id)
            ->select('sellers.id', 'sellers.business_name')
            ->distinct()
            ->orderBy('sellers.business_name')
            ->get();
        
        return view('account.transactions', compact('consumer', 'transactions', 'stores'));
    }

    /*
    |--------------------------------------------------------------------------
    | QR Code Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Show consumer's QR code
     */
    public function showQrCode()
    {
        $consumer = Auth::guard('consumer')->user();
        if (!$consumer) {
            return redirect()->route('login')->with('error', 'Please log in to view your QR code.');
        }

        $consumer->load(['qrCode', 'pointTransactions']);

        if (!$consumer->qrCode) {
            $consumer->generateQrCode();
            $consumer->refresh();
        }

        return view('consumers.qr-code', compact('consumer'));
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
    | API Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get consumer points (API)
     */
    public function getPoints()
    {
        $consumer = Auth::guard('consumer')->user();
        
        $totalEarned = $consumer->pointTransactions()->where('type', 'earn')->sum('points');
        $totalSpent = $consumer->pointTransactions()->where('type', 'spend')->sum('points');
        
        return response()->json([
            'success' => true,
            'data' => [
                'total_earned' => $totalEarned,
                'total_spent' => $totalSpent,
                'available' => $totalEarned - $totalSpent
            ]
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
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