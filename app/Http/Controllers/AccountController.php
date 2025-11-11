<?php

namespace App\Http\Controllers;

use App\Repository\ConsumerPointRepository;
use App\Repository\ConsumerRepository;
use App\Repository\PointTransactionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    private ConsumerRepository $cRepo;
    private ConsumerPointRepository $cPRepo;

    public function __construct(ConsumerRepository $cRepo, PointTransactionRepository $pTRepo, ConsumerPointRepository $cPRepo)
    {
        $this->cRepo = $cRepo;
        $this->cPRepo = $cPRepo;
    }
    public function index()
    {
        try {
            $consumer = Auth::user();
            $wallets  = $this->cPRepo->listByConsumerId($consumer->id);
            $total = $this->cPRepo->getTotalByConsumerId($consumer->id);
            // Get transaction history with receipt system data
            $transactions = $this->getTransactionHistory($consumer->id);
        } catch (\Exception $e) {
            $consumer = Auth::user();
            $wallets = collect([]);
            $total = ['earned' => 0, 'coins' => 0, 'spent' => 0];
            $transactions = collect([]);
        }
        return view('account.index', compact(
            'consumer',
            'wallets',
            'total',
            'transactions'
        ));
    }

    public function edit()
    {
        $consumer = Auth::user();
        return view('account.edit', compact('consumer'));
    }
    public function transactionHistory(Request $request)
    {
        $consumer = Auth::user();

        // Only load stores when needed for filter dropdown
        // Use cache for stores list (cache for 1 hour)
        $stores = Cache::remember('stores_list', 3600, function () {
            return DB::table('sellers')
                ->select('id', 'business_name')
                ->orderBy('business_name')
                ->get();
        });

        // Build point transactions query
        $pointTransactionsQuery = DB::table('point_transactions as pt')
            ->leftJoin('sellers as s', 's.id', '=', 'pt.seller_id')
            ->where('pt.consumer_id', $consumer->id);

        // Apply type filter for point transactions
        if ($request->filled('type')) {
            if ($request->type === 'earn') {
                $pointTransactionsQuery->where('pt.type', 'earn');
            } elseif ($request->type === 'spend') {
                $pointTransactionsQuery->where('pt.type', 'spend');
            }
        }

        // Apply store filter
        if ($request->filled('store')) {
            $pointTransactionsQuery->where('pt.seller_id', $request->store);
        }

        // Apply date filters
        if ($request->filled('date_from')) {
            $pointTransactionsQuery->whereDate('pt.scanned_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $pointTransactionsQuery->whereDate('pt.scanned_at', '<=', $request->date_to);
        }

        $pointTransactionsQuery->select([
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
            DB::raw("CASE WHEN pt.description LIKE 'Redeemed:%' THEN 'reward_redemption' ELSE 'point_transaction' END as transaction_type"),
            DB::raw('NULL as reward_name'),
            DB::raw('NULL as reward_status'),
            DB::raw('NULL as status_date')
        ]);

        // Build reward redemptions query
        $rewardRedemptionsQuery = DB::table('redeem_histories as rh')
            ->join('rewards as r', 'r.id', '=', 'rh.reward_id')
            ->leftJoin('sellers as s', 's.id', '=', 'r.seller_id')
            ->where('rh.consumer_id', $consumer->id);

        // Apply type filter for reward redemptions (only show when type is 'spend' or not filtered)
        if ($request->filled('type')) {
            if ($request->type === 'earn') {
                // Don't include reward redemptions for 'earn' filter
                $rewardRedemptionsQuery->whereRaw('1 = 0'); // This will exclude all reward redemptions
            }
            // If type is 'spend', include all reward redemptions (no additional filter needed)
        }

        // Apply store filter
        if ($request->filled('store')) {
            $rewardRedemptionsQuery->where('r.seller_id', $request->store);
        }

        // Apply date filters based on status dates or created_at
        if ($request->filled('date_from')) {
            $rewardRedemptionsQuery->whereRaw(
                'DATE(COALESCE(rh.approved_at, rh.rejected_at, rh.created_at)) >= ?',
                [$request->date_from]
            );
        }
        if ($request->filled('date_to')) {
            $rewardRedemptionsQuery->whereRaw(
                'DATE(COALESCE(rh.approved_at, rh.rejected_at, rh.created_at)) <= ?',
                [$request->date_to]
            );
        }

        $rewardRedemptionsQuery->select([
            'rh.id',
            'r.points_required as points',
            DB::raw("'spend' as type"),
            DB::raw("CONCAT('Reward: ', r.name) as description"),
            DB::raw('NULL as units_scanned'),
            DB::raw('COALESCE(rh.approved_at, rh.rejected_at, rh.created_at) as transaction_date'),
            DB::raw('NULL as receipt_code'),
            'rh.created_at',
            's.business_name as store_name',
            's.address as store_location',
            's.phone as store_phone',
            DB::raw("'reward_redemption' as transaction_type"),
            'r.name as reward_name',
            'rh.status as reward_status',
            DB::raw('COALESCE(rh.approved_at, rh.rejected_at) as status_date')
        ]);

        // Combine both queries using UNION
        $transactions = $pointTransactionsQuery
            ->unionAll($rewardRedemptionsQuery)
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);

        return view('account.transactions', compact('consumer', 'transactions', 'stores'));
    }
    public function updateProfile(Request $request)
    {
        try {
            $id = Auth::id();
            $data = $request->validate([
                'full_name'     => 'required|string|max:255',
                'phone_number'  => 'nullable|string|min:8|max:20',
                'gender'        => 'required|in:male,female,other',
                'date_of_birth' => 'nullable|date|before:today',
            ]);
            $this->cRepo->update($id, $data);

            return redirect()->route('account')->with('success', 'Profile updated successfully!');
        } catch (\Throwable $e) {
            abort(500, 'Something went wrong');
        }
    }
    public function updatePassword(Request $request)
    {
        try {
            $id = Auth::id();
            $request->validate([
                'current_password' => 'required|current_password:consumer',
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                    'regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*(),.?":{}|<>]).*$/'
                ],
            ], [
                'password.regex' => 'Password is not strong enough. It must contain at least 1 uppercase letter and 1 special character (!@#$%^&*(),.?":{}|<>)',
                'password.min' => 'Password is not strong enough. It must be at least 8 characters',
                'password.confirmed' => 'Password confirmation does not match',
            ]);

            $this->cRepo->update($id, ['password' => Hash::make($request->password)]);
            return redirect()->route('account')->with('success', 'Password updated successfully!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to update password. Please try again.');
        }
    }

    // TODO: Implement need to refactor here
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
                // Extract item name from description
                if (preg_match('/Redeemed: (.+)/', $transaction->description, $matches)) {
                    // Reward redemption
                    $itemName = 'Reward: ' . $matches[1];
                } elseif (preg_match('/Purchased: (.+?) from/', $transaction->description, $matches)) {
                    // Receipt purchase
                    $itemName = $matches[1];
                } else {
                    // Use description as-is
                    $itemName = $transaction->description;
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
}
