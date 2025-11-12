<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountReward extends Model
{
    protected $fillable = [
        'seller_id',
        'name',
        'discount_percentage',
        'points_cost',
        'is_active',
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'points_cost' => 'integer',
        'is_active' => 'boolean',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    
    public function pendingTransactions()
    {
        return $this->hasMany(PendingTransaction::class, 'discount_reward_id');
    }
}
