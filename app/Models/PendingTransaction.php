<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingTransaction extends Model
{
    protected $fillable = [
        'receipt_code',
        'seller_id',
        'items',
        'total_points',
        'total_quantity',
        'status',
        'expires_at',
        'claimed_at',
        'claimed_by_consumer_id',
    ];

    protected $casts = [
        'items' => 'array',
        'expires_at' => 'datetime',
        'claimed_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function consumer()
    {
        return $this->belongsTo(Consumer::class, 'claimed_by_consumer_id');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
