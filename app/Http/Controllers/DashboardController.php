<?php

namespace App\Http\Controllers;

use App\Repository\PointTransactionRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private PointTransactionRepository $pTRepo;

    public function __construct(PointTransactionRepository $pTRepo)
    {
        $this->pTRepo = $pTRepo;
    }
    public function index(Request $request)
    {
        $consumer = Auth::user();

        $selectedMonth = $request->get('month', Carbon::now()->format('F'));
        $year = Carbon::now()->year;

        $monthNumber = Carbon::parse("1 $selectedMonth $year")->month;

        try {
            $availablePoints = $this->pTRepo->current($consumer->id);

            $monthlyData = $this->pTRepo->monthly($consumer->id, $monthNumber, $year);
            // $monthlyData = $this->getMonthlyData($consumer->id, $monthNumber, $year);
            // Get recent activity data (NEW)
            $recentActivity = $this->getRecentActivityForDashboard($consumer->id);
        } catch (\Exception $e) {
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

    // private function getMonthlyData($consumerId, $month, $year)
    // {
    //     $pointsIn = DB::table('point_transactions')
    //         ->where('consumer_id', $consumerId)
    //         ->where('type', 'earn')
    //         ->whereMonth('created_at', $month)
    //         ->whereYear('created_at', $year)
    //         ->sum('points');

    //     $pointsOut = DB::table('point_transactions')
    //         ->where('consumer_id', $consumerId)
    //         ->where('type', 'spend')
    //         ->whereMonth('created_at', $month)
    //         ->whereYear('created_at', $year)
    //         ->sum('points');

    //     $prevMonth = $month == 1 ? 12 : $month - 1;
    //     $prevYear = $month == 1 ? $year - 1 : $year;

    //     $prevPointsIn = DB::table('point_transactions')
    //         ->where('consumer_id', $consumerId)
    //         ->where('type', 'earn')
    //         ->whereMonth('created_at', $prevMonth)
    //         ->whereYear('created_at', $prevYear)
    //         ->sum('points');

    //     $prevPointsOut = DB::table('point_transactions')
    //         ->where('consumer_id', $consumerId)
    //         ->where('type', 'spend')
    //         ->whereMonth('created_at', $prevMonth)
    //         ->whereYear('created_at', $prevYear)
    //         ->sum('points');

    //     return [
    //         'points_in' => $pointsIn,
    //         'points_out' => $pointsOut,
    //         'prev_points_in' => $prevPointsIn,
    //         'prev_points_out' => $prevPointsOut,
    //         'all_activities' => $pointsIn + $pointsOut,
    //         'net_flow' => $pointsIn - $pointsOut,
    //         'prev_month_name' => Carbon::create()->month($prevMonth)->format('M')
    //     ];
    // }

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
}
