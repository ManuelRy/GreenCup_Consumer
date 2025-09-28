<?php

namespace App\Repository;

use App\Models\Item;
use Illuminate\Database\Eloquent\Collection;

class ItemRepository
{
  public function list(): Collection
  {
    return Item::with(['seller'])->get();
  }
}
