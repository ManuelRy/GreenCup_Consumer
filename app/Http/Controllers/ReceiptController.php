<?php

namespace App\Http\Controllers;

use App\Repository\ConsumerPointRepository;
use App\Repository\EarnHistoryRepository;
use App\Repository\PendingTransactionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReceiptController extends Controller
{
    private PendingTransactionRepository $pTRepo;
    private ConsumerPointRepository $cPRepo;
    private EarnHistoryRepository $eHRepo;

    public function __construct(
        PendingTransactionRepository $pTRepo,
        ConsumerPointRepository $cPRepo,
        EarnHistoryRepository $eHRepo
    ) {
        $this->pTRepo = $pTRepo;
        $this->cPRepo = $cPRepo;
        $this->eHRepo = $eHRepo;
    }
    /**
     * Check if a receipt code is valid and return its details
     */
    public function scan()
    {
        return view('consumers.scan-receipt');
    }
    public function check(Request $request)
    {
        $request->validate([
            'receipt_code' => 'required|string'
        ]);

        try {

            $pending = $this->pTRepo->getByCode($request->receipt_code);
            if (!$pending) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid receipt code'
                ]);
            }

            // Check if expired
            if ($this->pTRepo->isExpire($pending)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This receipt has expired'
                ]);
            }

            $receiptData = [
                'receipt_code' => $pending->receipt_code,
                'store_name' => $pending->seller->business_name,
                'store_address' => $pending->seller->address,
                'status' => $pending->status,
                'total_points' => $pending->total_points,
                'total_quantity' => $pending->total_quantity,
                'items' => array_map(function ($item) {
                    return [
                        'name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'points_per_unit' => $item['points_per_unit'],
                        'total_points' => $item['total_points']
                    ];
                },  $pending->items),
                'created_at' => $pending->created_at,
                'expires_at' => $pending->expires_at
            ];

            return response()->json([
                'success' => true,
                'receipt' => $receiptData
            ]);
        } catch (\Exception $e) {
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
        try {
            $request->validate([
                'receipt_code' => 'required|string'
            ]);

            $consumer_id = Auth::id();
            DB::beginTransaction();
            $pending = $this->pTRepo->getByCode($request->receipt_code);
            if (!$pending) {
                throw new \Exception('Invalid receipt code');
            }

            if ($pending->status === 'claimed') {
                throw new \Exception('This receipt has already been claimed');
            }

            // Check if expired
            if ($this->pTRepo->isExpire($pending)) {
                throw new \Exception('This receipt has expired');
            }

            $itemNames = array_column($pending->items, 'name');
            $description = 'Purchased: ' . implode(', ', array_slice($itemNames, 0, 3));
            if (count($itemNames) > 3) {
                $description .= ' and ' . (count($itemNames) - 3) . ' more items';
            }

            // update pending transaction
            $this->pTRepo->update($pending->id, [
                'claimed_at' => now(),
                'status' => 'claimed',
                'claimed_by_consumer_id' => $consumer_id,
            ]);
            // update consumer points
            $consumer_point = $this->cPRepo->claim($consumer_id, $pending->seller_id, $pending->total_points, $description, $pending->receipt_code);
            // create new earn history
            $this->eHRepo->create([
                'consumer_id' => $consumer_id,
                'earned' =>  $pending->total_points,
                'seller_id' => $pending->seller_id
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Points claimed successfully!',
                'points_earned' => $pending->total_points,
                'new_balance' => $consumer_point->earned,
                // 'transaction_id' => $pointTransactionId,
                'seller' => [
                    'name' => $pending->seller->business_name,
                    'address' => $pending->seller->address
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
            $receipts = $receipts->map(function ($receipt) {
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

    /**
     * Handle point rejection by seller
     * This would typically be called by a webhook or API from the seller system
     */
    public function handleRejection(Request $request)
    {
        $request->validate([
            'receipt_code' => 'required|string',
            'reason' => 'string|nullable'
        ]);

        try {
            DB::beginTransaction();

            $pending = $this->pTRepo->getByCode($request->receipt_code);
            if (!$pending) {
                throw new \Exception('Invalid receipt code');
            }

            if ($pending->status !== 'claimed') {
                throw new \Exception('Receipt is not in claimed status');
            }

            if (!$pending->claimed_by_consumer_id) {
                throw new \Exception('Receipt was not claimed by any consumer');
            }

            // Update pending transaction status to rejected
            $this->pTRepo->update($pending->id, [
                'status' => 'rejected',
            ]);

            // Return points to consumer (deduct earned points and coins)
            $this->cPRepo->refund($pending->claimed_by_consumer_id, $pending->seller_id, $pending->total_points, 'earn');

            // Log the rejection for admin purposes
            Log::info('Point transaction rejected', [
                'receipt_code' => $pending->receipt_code,
                'consumer_id' => $pending->claimed_by_consumer_id,
                'seller_id' => $pending->seller_id,
                'points' => $pending->total_points,
                'reason' => $request->reason
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction rejected and points returned to consumer',
                'points_returned' => $pending->total_points,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error handling rejection: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Handle point approval by seller
     * This confirms the points are valid and should remain with the consumer
     */
    public function handleApproval(Request $request)
    {
        $request->validate([
            'receipt_code' => 'required|string',
            'message' => 'string|nullable'
        ]);

        try {
            DB::beginTransaction();

            $pending = $this->pTRepo->getByCode($request->receipt_code);
            if (!$pending) {
                throw new \Exception('Invalid receipt code');
            }

            if ($pending->status !== 'claimed') {
                throw new \Exception('Receipt is not in claimed status');
            }

            if (!$pending->claimed_by_consumer_id) {
                throw new \Exception('Receipt was not claimed by any consumer');
            }

            // Update pending transaction status to approved
            $this->pTRepo->update($pending->id, [
                'status' => 'approved',
            ]);

            // Create approval transaction record
            $this->cPRepo->approve($pending->claimed_by_consumer_id, $pending->seller_id, $pending->total_points, $pending->receipt_code);

            // Log the approval for admin purposes
            Log::info('Point transaction approved', [
                'receipt_code' => $pending->receipt_code,
                'consumer_id' => $pending->claimed_by_consumer_id,
                'seller_id' => $pending->seller_id,
                'points' => $pending->total_points,
                'message' => $request->message
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction approved successfully',
                'points_confirmed' => $pending->total_points,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error handling approval: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
