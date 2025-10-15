<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RedeemHistory extends Model
{
    protected $fillable = [
        'consumer_id',
        'reward_id',
        'quantity',
        'is_redeemed',
        'status',
        'approved_at',
        'rejected_at',
        'rejection_reason',
    ];

    protected $casts = [
        'is_redeemed' => 'boolean',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
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
