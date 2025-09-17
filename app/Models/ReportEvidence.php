<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportEvidence extends Model
{
  protected $table = "report_evidences";
  protected $fillable = [
    'report_id',
    'file_url',
  ];

  public function report()
  {
    return $this->belongsTo(Report::class);
  }
}
