<?php

namespace App\Repository;

use App\Models\EarnHistory;

class EarnHistoryRepository
{
  public function create($data) {
    return EarnHistory::create($data);
  }
  // TODO: Implement this is a repository for the list of earning history for a user (previously transaction) here
  // TODO: Implement create a function to get the list of earning for a consumer in this repo here

}
