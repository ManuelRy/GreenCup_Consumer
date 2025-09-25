<?php

namespace App\Repository;

use App\Models\ConsumerPoint;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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
  public function getByConsumerAndSeller($consumer_id, $seller_id): ?Model
  {
    return ConsumerPoint::firstOrCreate(
      [
        'consumer_id' => $consumer_id,
        'seller_id' => $seller_id,
      ],
      [
        'coins' => 0,
      ]
    );
  }

  public function get($id)
  {
    return ConsumerPoint::find($id);
  }
  public function update(int $id, array $data): bool
  {
    $cp = $this->get($id);
    return $cp ? $cp->update($data) : false;
  }
  public function claim($consumer_id, $seller_id, $points)
  {
    $cp = $this->getByConsumerAndSeller($consumer_id, $seller_id);
    $cp->earned += $points;
    $cp->coins += $points;
    $cp->save();
    return $cp;
  }

  public function redeem($consumer_id, $seller_id, $points)
  {
    $cp = $this->getByConsumerAndSeller($consumer_id, $seller_id);
    $cp->spent += $points;
    $cp->coins -= $points;
    $cp->save();
    return $cp;
  }

  public function refund($consumer_id, $seller_id, $points)
  {
    $cp = $this->getByConsumerAndSeller($consumer_id, $seller_id);
    $cp->spent -= $points;
    $cp->coins += $points;
    $cp->save();
    return $cp;
  }
}
