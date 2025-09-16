<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumerPoint extends Model
{
    protected $fillable = [
        'consumer_id',
        'coins',
        'seller_id'
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
