<?php

namespace App\Repository;

use App\Models\Item;
use Illuminate\Database\Eloquent\Model;

class RewardRepository
{
  public function get($id): ?Model
  {
    return Item::find($id);
  }
}
