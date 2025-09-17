<?php

namespace App\Repository;

use App\Models\Report;
use App\Models\ReportEvidence;
use Illuminate\Database\Eloquent\Model;

class ReportRepository
{
  public function create($data = []): Model
  {
    return Report::create([...$data,    'reporter'     => 'consumer', 'status'       => 'Investigate',]);
  }

  public function createEvidence($data = [])
  {
    return ReportEvidence::create($data);
  }
}
