<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EarnHistory extends Model
{
    protected $fillable = [
        'consumer_id',
        'earned',
        'seller_id'
    ];
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
        public function consumer() {
        return $this->belongsTo(Consumer::class);
    }
}
