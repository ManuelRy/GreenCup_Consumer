<?php

namespace App\Repository;

use App\Models\Consumer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ConsumerRepository
{

  public function create(array $data): Model
  {
    return Consumer::create($data);
  }
  public function update(int $id, array $data)
  {
    return  Consumer::find($id)->update($data);
  }
}
