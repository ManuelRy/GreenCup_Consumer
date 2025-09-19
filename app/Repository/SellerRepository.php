<?php

namespace App\Repository;

use App\Models\Seller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SellerRepository
{
  public function list(): Collection
  {
    return Seller::with(['rewards'])->get();
  }

  public function get($id): ?Model
  {
    return Seller::find($id);
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
