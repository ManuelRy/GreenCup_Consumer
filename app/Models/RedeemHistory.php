<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RedeemHistory extends Model
{
    protected $fillable = [
        'consumer_id',
        'reward_id',
        'is_redeemed',
    ];

    public function consumer()
    {
        return $this->belongsTo(Consumer::class);
    }

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }
};
