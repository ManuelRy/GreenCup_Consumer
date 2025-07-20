<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $consumer = Auth::guard('consumer')->user();
        $selectedMonth = $request->get('month', Carbon::now()->format('F'));
        $year = Carbon::now()->year;
        
        // Get month number from name
        $monthNumber = Carbon::parse("1 $selectedMonth $year")->month;
        
        // Calculate available points (total earned - total spent)
        $totalEarned = $consumer->pointTransactions()
            ->where('type', 'earn')
            ->sum('points');
            
        $totalSpent = $consumer->pointTransactions()
            ->where('type', 'spend')
            ->sum('points');
            
        $availablePoints = $totalEarned - $totalSpent;
        
        // Get monthly data
        $monthlyData = $this->getMonthlyData($consumer->id, $monthNumber, $year);
        
        return view('dashboard', compact(
            'consumer',
            'availablePoints',
            'monthlyData',
            'selectedMonth'
        ));
    }
    
    private function getMonthlyData($consumerId, $month, $year)
    {
        // Points earned this month
        $pointsIn = DB::table('point_transactions')
            ->where('consumer_id', $consumerId)
            ->where('type', 'earn')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('points');
            
        // Points spent this month
        $pointsOut = DB::table('point_transactions')
            ->where('consumer_id', $consumerId)
            ->where('type', 'spend')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('points');
            
        // Previous month data for comparison
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
        
        // All activities (total transactions this month)
        $allActivities = $pointsIn + $pointsOut;
        
        // Net flow
        $netFlow = $pointsIn - $pointsOut;
        
        return [
            'points_in' => $pointsIn,
            'points_out' => $pointsOut,
            'prev_points_in' => $prevPointsIn,
            'prev_points_out' => $prevPointsOut,
            'all_activities' => $allActivities,
            'net_flow' => $netFlow,
            'prev_month_name' => Carbon::create()->month($prevMonth)->format('M')
        ];
    }
}