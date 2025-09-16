<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EarnHistory extends Model
{
    protected $fillable = [
        'consumer_id',
        'earned',
        'coins',
        'spent',
        'seller_id'
    ];
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
