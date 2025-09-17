<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{

    static $priorities = ["Critical", "High", 'Medium', 'Low'];
    static $status  = ['Resolve', 'Warning', 'Suspend', 'Investigate'];
    static $reporter = ['consumer', 'seller'];
    static $tag = [
        "App Bug",
        "Store Issue",
        "Payment",
        "Account",
        "QR Scan",
        "Other"
    ];

    protected $fillable = [
        'title',
        'priority',
        'tag',
        'description',
        'reporter',
        'reporter_id',
        'status',
    ];

    public function evidences()
    {
        return $this->hasMany(ReportEvidence::class);
    }
}
