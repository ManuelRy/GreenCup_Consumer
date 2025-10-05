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
        'earned' => 0,
        'spent' => 0,
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
  public function claim($consumer_id, $seller_id, $points, $description = null, $receipt_code = null)
  {
    $cp = $this->getByConsumerAndSeller($consumer_id, $seller_id);
    $cp->earned += $points;
    $cp->coins += $points;
    $cp->save();

    // Create point transaction record for earning
    \App\Models\PointTransaction::create([
      'consumer_id' => $consumer_id,
      'seller_id' => $seller_id,
      'points' => $points,
      'units_scanned' => 1,
      'type' => 'earn',
      'description' => $description ?: 'Points earned from receipt',
      'receipt_code' => $receipt_code,
      'scanned_at' => now(),
    ]);

    return $cp;
  }

  public function redeem($consumer_id, $seller_id, $points, $reward = null)
  {
    $cp = $this->getByConsumerAndSeller($consumer_id, $seller_id);
    $cp->spent += $points;
    $cp->coins -= $points;
    $cp->save();

    // Create point transaction record for spending
    $description = $reward
      ? "Redeemed: {$reward->name}"
      : 'Reward redemption';

    \App\Models\PointTransaction::create([
      'consumer_id' => $consumer_id,
      'seller_id' => $seller_id,
      'points' => $points,
      'units_scanned' => 1,
      'type' => 'spend',
      'description' => $description,
      'scanned_at' => now(),
    ]);

    return $cp;
  }

  public function refund($consumer_id, $seller_id, $points, $type = 'spend')
  {
    $cp = $this->getByConsumerAndSeller($consumer_id, $seller_id);

    if ($type === 'spend') {
      // Refunding spent points (reward redemption refund)
      $cp->spent -= $points;
      $cp->coins += $points;
      $description = 'Refund for reward redemption';
      $transaction_type = 'refund';
    } else {
      // Refunding earned points (receipt rejection)
      $cp->earned -= $points;
      $cp->coins -= $points;
      $description = 'Receipt rejected by seller';
      $transaction_type = 'rejected';
    }

    $cp->save();

    // Create point transaction record
    \App\Models\PointTransaction::create([
      'consumer_id' => $consumer_id,
      'seller_id' => $seller_id,
      'points' => $points,
      'units_scanned' => 1,
      'type' => $transaction_type,
      'description' => $description,
      'scanned_at' => now(),
    ]);

    return $cp;
  }

  public function approve($consumer_id, $seller_id, $points, $receipt_code)
  {
    // Just create a transaction record for approval (points already claimed)
    \App\Models\PointTransaction::create([
      'consumer_id' => $consumer_id,
      'seller_id' => $seller_id,
      'points' => $points,
      'units_scanned' => 1,
      'type' => 'approved',
      'description' => 'Receipt approved by seller',
      'receipt_code' => $receipt_code,
      'scanned_at' => now(),
    ]);

    return $this->getByConsumerAndSeller($consumer_id, $seller_id);
  }
  public function countByConsumerId($id): int
  {
    return ConsumerPoint::where('consumer_id', $id)->count();
  }
}
