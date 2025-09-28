<?php

namespace App\Repository;

use App\Models\Seller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SellerRepository
{
  public function list(): Collection
  {
    return Seller::with(['rewards', 'items'])->get();
  }

  public function get($id): ?Model
  {
    return Seller::with(['items'])->where('id', $id)->first();
  }

  public function addPoints($id, $points): bool
  {
    $seller = $this->get($id);
    if (!$seller) return false;

    $seller->total_points += $points;
    $seller->save();
    return true;
  }
}
