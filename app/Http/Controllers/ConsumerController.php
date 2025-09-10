<?php

namespace App\Http\Controllers;

use App\Models\Consumer;
use App\Repository\ConsumerRepository;
use App\Repository\PointTransactionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

class ConsumerController extends Controller
{
    private ConsumerRepository $cRepo;
    private PointTransactionRepository $pTRepo;

    public function __construct(ConsumerRepository $cRepo, PointTransactionRepository $pTRepo)
    {
        $this->cRepo = $cRepo;
        $this->pTRepo = $pTRepo;
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

        // Base query for receipt system - using the same logic as getTransactionHistory
        $query = DB::table('point_transactions as pt')
            ->leftJoin('sellers as s', 's.id', '=', 'pt.seller_id')
            ->leftJoin('pending_transactions as pend', 'pend.receipt_code', '=', 'pt.receipt_code')
            ->where('pt.consumer_id', $consumer->id)
            ->select([
                'pt.id',
                'pt.points',
                'pt.type',
                'pt.description',
                'pt.units_scanned',
                'pt.seller_id',
                'pt.scanned_at as transaction_date',
                'pt.receipt_code',
                'pt.created_at',
                's.business_name as store_name',
                's.address as store_location',
                's.phone as store_phone',
                'pend.items as receipt_items',
                'pend.total_points as receipt_total_points',
                'pend.total_quantity as receipt_total_quantity'
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

        $transactionData = $query->orderBy('pt.scanned_at', 'desc')->get();

        // Process transactions using the same logic as getTransactionHistory
        $processedTransactions = $transactionData->map(function ($transaction) {
            // Parse receipt items if available
            $items = [];
            if ($transaction->receipt_items) {
                $items = json_decode($transaction->receipt_items, true) ?: [];
            }

            // Determine item name from receipt items or description - SAME LOGIC AS WORKING METHOD
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
                'qr_code' => $qrCode,
                'code' => $qrCode,
                'receipt_items_parsed' => $items, // Keep this for the template
                'receipt_items' => $items, // Also keep this
                'receipt_total_points' => $transaction->receipt_total_points,
                'receipt_total_quantity' => $transaction->receipt_total_quantity,
                'created_at' => $transaction->created_at
            ];
        });

        // Paginate the processed data manually
        $page = $request->get('page', 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        $paginatedItems = $processedTransactions->slice($offset, $perPage)->values();

        $transactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $processedTransactions->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

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
        try {
            $id = Auth::id();
            $totalEarned = $this->pTRepo->earn($id);
            $totalSpent = $this->pTRepo->spent($id);
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
}
