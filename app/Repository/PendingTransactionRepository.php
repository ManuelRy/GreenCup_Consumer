<?php

namespace App\Repository;

use App\Models\PendingTransaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PendingTransactionRepository
{
  public function listByConsumer($consumer_id): Collection
  {
    return PendingTransaction::with(['seller'])
      ->where('claimed_by_consumer_id', $consumer_id)
      ->get();
  }
  public function get($id): ?Model
  {
    return  PendingTransaction::with(['seller'])->find($id);
  }
  public function update($id, $data = []): bool
  {
    $receipt = $this->get($id);
    return $receipt ? $receipt->update($data) : false;
  }
  public function getByCode($code): ?Model
  {
    return PendingTransaction::where('receipt_code', $code)->with(['seller', 'consumer'])->first();
  }

  public function isExpire(Model $pending): bool
  {
    if ($pending->status  == "expired") return true;
    return $pending->expires_at ? Carbon::parse($pending->expires_at)->isPast() : false;
  }
  public function totalQuantityByConsumer($consumer_id): int
  {
    return PendingTransaction::where('claimed_by_consumer_id', $consumer_id)
      ->sum('total_quantity');
  }
}
