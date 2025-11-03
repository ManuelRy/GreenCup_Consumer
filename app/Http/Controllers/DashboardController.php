<?php

namespace App\Http\Controllers;

use App\Repository\ConsumerPointRepository;
use App\Repository\PointTransactionRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private PointTransactionRepository $pTRepo;
    private ConsumerPointRepository $cPRepo;

    public function __construct(PointTransactionRepository $pTRepo, ConsumerPointRepository $cPRepo)
    {
        $this->pTRepo = $pTRepo;
        $this->cPRepo = $cPRepo;
    }
    public function index(Request $request)
    {
        $consumer = Auth::user();

        $selectedMonth = $request->get('month', Carbon::now()->format('F'));
        $year = Carbon::now()->year;

        $monthNumber = Carbon::parse("1 $selectedMonth $year")->month;

        try {
            $currentTotal = $this->cPRepo->getTotalByConsumerId($consumer->id) ?? 0;
            $availablePoints = $this->pTRepo->current($consumer->id);
            // $monthlyData = $this->getMonthlyData($consumer->id, $monthNumber, $year);
            // Get recent activity data (NEW)
            $recentActivity = $this->getRecentActivityForDashboard($consumer->id);

            // Get environmental impact data for motivational messages
            $environmentalData = $this->getEnvironmentalImpactData($consumer->id);
            $motivationalMessage = $this->getMotivationalMessage($environmentalData, $consumer);
        } catch (\Exception $e) {
            $currentTotal = 0;
            $availablePoints = 0;
            $recentActivity = collect([]);
            $environmentalData = [];
            $motivationalMessage = $this->getDefaultMotivationalMessage($consumer);
        }

        return view('dashboard', compact('consumer', 'currentTotal', 'availablePoints', 'selectedMonth', 'recentActivity', 'environmentalData', 'motivationalMessage'));
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

    /**
     * Get environmental impact data for the consumer
     * Using same calculations as environmental-impact page
     */
    private function getEnvironmentalImpactData($consumerId)
    {
        // Get total cups saved (sum of all quantities from pending_transactions)
        // This correctly accounts for multiple cups per transaction
        $totalUnits = DB::table('pending_transactions')
            ->where('claimed_by_consumer_id', $consumerId)
            ->sum('total_quantity') ?? 0;

        // Calculate environmental impact using environmental-impact page formulas
        $co2_grams = $totalUnits * 20; // grams of CO2 saved
        $co2_kg = $co2_grams / 1000; // Convert to kg
        $waste_prevented_grams = $totalUnits * 8; // grams of waste prevented
        $waste_prevented_kg = $waste_prevented_grams / 1000; // Convert to kg

        return [
            'total_units' => $totalUnits, // Total cups saved
            'co2_saved_grams' => round($co2_grams, 2), // CO2 in grams
            'co2_saved' => round($co2_kg, 2), // CO2 in kg
            'waste_prevented_grams' => round($waste_prevented_grams, 2), // Waste in grams
            'waste_prevented' => round($waste_prevented_kg, 2), // Waste in kg
        ];
    }

    /**
     * Get motivational message based on environmental impact
     */
    private function getMotivationalMessage($environmentalData, $consumer)
    {
        $totalUnits = $environmentalData['total_units'] ?? 0;

        if ($totalUnits >= 100) {
            return "Amazing work, {$consumer->name}! You've made a huge environmental impact! 🌍";
        } elseif ($totalUnits >= 50) {
            return "Great job, {$consumer->name}! You're making a real difference! 🌱";
        } elseif ($totalUnits >= 20) {
            return "Keep it up, {$consumer->name}! Every eco-action counts! ♻️";
        } elseif ($totalUnits >= 5) {
            return "Nice start, {$consumer->name}! You're on the right track! 🌿";
        } else {
            return "Welcome, {$consumer->name}! Start your eco-journey today! 💚";
        }
    }

    /**
     * Get default motivational message
     */
    private function getDefaultMotivationalMessage($consumer)
    {
        return "Welcome back, {$consumer->name}! Keep making a difference! 🌱";
    }

    /**
     * Guest mode dashboard (no authentication required)
     */
    public function guestIndex(Request $request)
    {
        // Create a mock consumer object for guest mode
        $consumer = (object) [
            'name' => 'Guest',
            'id' => null,
        ];

        // Default values for guest mode
        $currentTotal = [
            'coins' => 0,
            'earned' => 0,
            'spent' => 0
        ];
        $availablePoints = 0;
        $selectedMonth = $request->get('month', Carbon::now()->format('F'));
        $recentActivity = collect([]);

        // Guest environmental data with example/demo values
        $environmentalData = [
            'total_units' => 0,
            'co2_saved_grams' => 0,
            'co2_saved' => 0,
            'waste_prevented_grams' => 0,
            'waste_prevented' => 0,
        ];

        $motivationalMessage = "Welcome to GreenCup! Join us to start your eco-journey! 🌱";

        // For guests, always show onboarding on first visit (will be stored in localStorage)
        return view('dashboard', compact(
            'consumer',
            'currentTotal',
            'availablePoints',
            'selectedMonth',
            'recentActivity',
            'environmentalData',
            'motivationalMessage'
        ))->with('show_onboarding', false); // Let JavaScript handle guest tour based on localStorage
    }
}
