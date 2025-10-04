<?php

namespace App\Repository;

use App\Models\Item;
use App\Models\RedeemHistory;
use App\Models\Reward;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class RewardRepository
{
  public function get($id): ?Model
  {
    return Reward::find($id);
  }

  public function history($consumer_id): Collection
  {
    return RedeemHistory::with(['reward.seller'])->where('consumer_id', $consumer_id)->get();
  }

  public function redeem( $reward_id, $qantity): bool
  {
    $reward = $this->get($reward_id);
    return $reward ? $reward->update([
      'quantity_redeemed' => $reward->quantity_redeemed + $qantity
    ]) : false;
  }

  public function createHistory($consumer_id, $reward_id, $quantity = 1)
  {
    // For now, create one history record per item redeemed
    // TODO: Add quantity column to redeem_histories table in backend migration
    $histories = [];
    for ($i = 0; $i < $quantity; $i++) {
      $histories[] = RedeemHistory::create([
        'consumer_id' => $consumer_id,
        'reward_id' => $reward_id,
        'is_redeemed' => true,
      ]);
    }
    return collect($histories);
  }
}
