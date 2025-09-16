<?php

namespace App\Repository;

use App\Models\ConsumerPoint;
use Illuminate\Database\Eloquent\Collection;

class ConsumerPointRepository
{
  public function listByConsumerId($id): Collection
  {
    return ConsumerPoint::where('consumer_id', $id)->with(['seller'])->get();
  }

  public function getTotalByConsumerId($id)
  {
    $totals = ConsumerPoint::where('consumer_id', $id)
      ->selectRaw('SUM(earned) as earned, SUM(coins) as coins, SUM(spent) as spent')
      ->first();

    return [
      'earned' => (int) $totals->earned,
      'coins'  => (int) $totals->coins,
      'spent'  => (int) $totals->spent,
    ];
  }
}
