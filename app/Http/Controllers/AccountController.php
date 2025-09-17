<?php

namespace App\Http\Controllers;

use App\Repository\ConsumerPointRepository;
use App\Repository\ConsumerRepository;
use App\Repository\PointTransactionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        // Filtering logic (optional, basic example)
        $query = DB::table('point_transactions as pt')
            ->leftJoin('sellers as s', 's.id', '=', 'pt.seller_id')
            ->where('pt.consumer_id', $consumer->id);

        if ($request->filled('type')) {
            $query->where('pt.type', $request->type);
        }
        if ($request->filled('store')) {
            $query->where('pt.seller_id', $request->store);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('pt.scanned_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('pt.scanned_at', '<=', $request->date_to);
        }

        $transactions = $query
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
            ])
            ->orderBy('pt.scanned_at', 'desc')
            ->paginate(10);

        // Get all stores for filter dropdown
        $stores = DB::table('sellers')->select('id', 'business_name')->get();

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
            // $consumer->update($data);
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
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);

            $this->cRepo->update($id, ['password' => Hash::make($request->password)]);
            return redirect()->route('account')->with('success', 'Password updated successfully!');
        } catch (\Throwable $e) {
            abort(500, 'Something went wrong');
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
}
