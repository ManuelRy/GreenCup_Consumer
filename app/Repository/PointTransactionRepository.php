<?php

namespace App\Repository;

use App\Models\PointTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PointTransactionRepository
{
  public function current($consumer_id): float
  {
    return $this->earn($consumer_id) - $this->spent($consumer_id);
  }

  public function earn($consumer_id)
  {
    return   $earn = PointTransaction::where('consumer_id', $consumer_id)
      ->where('type', 'earn')
      ->sum('points') ?? 0;
  }
  public function spent($consumer_id)
  {
    return $spent = PointTransaction::where('consumer_id', $consumer_id)
      ->where('type', 'spend')
      ->sum('points') ?? 0;
  }

  public function monthly(int $consumer_id, $month, $year)
  {
    $pointsIn = PointTransaction::where('consumer_id', $consumer_id)
      ->where('type', 'earn')
      ->whereMonth('created_at', $month)
      ->whereYear('created_at', $year)
      ->sum('points') ?? 0;

    $pointsOut = PointTransaction::where('consumer_id', $consumer_id)
      ->where('type', 'spend')
      ->whereMonth('created_at', $month)
      ->whereYear('created_at', $year)
      ->sum('points') ?? 0;

    $prevMonth = $month == 1 ? 12 : $month - 1;
    $prevYear = $month == 1 ? $year - 1 : $year;

    $prevPointsIn = PointTransaction::where('consumer_id', $consumer_id)
      ->where('type', 'earn')
      ->whereMonth('created_at', $prevMonth)
      ->whereYear('created_at', $prevYear)
      ->sum('points') ?? 0;

    $prevPointsOut = PointTransaction::where('consumer_id', $consumer_id)
      ->where('type', 'spend')
      ->whereMonth('created_at', $prevMonth)
      ->whereYear('created_at', $prevYear)
      ->sum('points') ?? 0;

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
