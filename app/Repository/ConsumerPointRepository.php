<?php

namespace App\Repository;

use App\Models\ConsumerPoint;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ConsumerPointRepository
{
  public function listByConsumerId($id): Collection
  {
    return ConsumerPoint::where('consumer_id', $id)
      ->where('coins', '>=', 1)
      ->with(['seller'])
      ->get();
  }

  public function getTotalByConsumerId($id)
  {
    // Calculate earned from point_transactions
    // Includes: 'earn' transactions
    $earned = \App\Models\PointTransaction::where('consumer_id', $id)
      ->where('type', 'earn')
      ->sum('points') ?? 0;

    // Calculate spent from point_transactions
    // Includes: 'spend' transactions (redemptions)
    $spent = \App\Models\PointTransaction::where('consumer_id', $id)
      ->where('type', 'spend')
      ->sum('points') ?? 0;

    // Calculate rejected (subtract from earned)
    // When a receipt is rejected, we lose those earned points
    $rejected = \App\Models\PointTransaction::where('consumer_id', $id)
      ->where('type', 'rejected')
      ->sum('points') ?? 0;

    // Calculate refunds (add back to available)
    // When a redemption is refunded, we get those spent points back
    $refunded = \App\Models\PointTransaction::where('consumer_id', $id)
      ->where('type', 'refund')
      ->sum('points') ?? 0;

    // Calculate current coins correctly:
    // Start with earned, subtract rejected (net earned)
    // Subtract spent, add back refunds (net spent)
    $netEarned = $earned - $rejected;
    $netSpent = $spent - $refunded;
    $coins = $netEarned - $netSpent;
    
    // CRITICAL FIX: Ensure coins never display as negative
    // If somehow negative, cap at 0 and log the issue
    if ($coins < 0) {
      \Log::warning("Consumer ID {$id} has negative points: Earned={$earned}, Rejected={$rejected}, Spent={$spent}, Refunded={$refunded}, Calculated={$coins}");
      $coins = 0;
    }

    return [
      'earned' => (int) $netEarned,  // Total earned minus rejections
      'coins'  => max(0, (int) $coins), // Ensure never negative
      'spent'  => (int) $netSpent,   // Total spent minus refunds
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
    
    // CRITICAL FIX: Prevent negative points
    if ($cp->coins < $points) {
      throw new \Exception('Insufficient points for redemption. Available: ' . $cp->coins . ', Required: ' . $points);
    }
    
    $cp->spent += $points;
    $cp->coins -= $points;
    
    // Double-check after calculation
    if ($cp->coins < 0) {
      throw new \Exception('Point calculation resulted in negative balance. Transaction cancelled.');
    }
    
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
      // This should always make coins go UP, so it's safe
      $cp->spent -= $points;
      $cp->coins += $points;
      $description = 'Refund for reward redemption';
      $transaction_type = 'refund';
    } else {
      // Refunding earned points (receipt rejection)
      // CRITICAL FIX: Prevent negative points when rejecting receipts
      if ($cp->earned < $points) {
        throw new \Exception('Cannot refund more points than earned. Earned: ' . $cp->earned . ', Refund requested: ' . $points);
      }
      if ($cp->coins < $points) {
        throw new \Exception('Insufficient points for refund. Available: ' . $cp->coins . ', Refund: ' . $points);
      }
      
      $cp->earned -= $points;
      $cp->coins -= $points;
      $description = 'Receipt rejected by seller';
      $transaction_type = 'rejected';
    }

    // Double-check no negative values
    if ($cp->coins < 0 || $cp->earned < 0 || $cp->spent < 0) {
      throw new \Exception('Refund calculation resulted in negative balance. Transaction cancelled.');
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
