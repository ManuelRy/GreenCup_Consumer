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
        'expires_at',
        'approved_at',
        'rejected_at',
        'rejection_reason',
    ];

    protected $casts = [
        'is_redeemed' => 'boolean',
        'expires_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Check if this redemption receipt has expired
     */
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }
        return now('Asia/Phnom_Penh')->isAfter($this->expires_at);
    }

    /**
     * Check if redemption can still be used/approved
     */
    public function canBeUsed(): bool
    {
        return $this->status === 'pending' && !$this->isExpired();
    }

    public function consumer()
    {
        return $this->belongsTo(Consumer::class);
    }

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }
};
