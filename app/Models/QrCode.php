<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QrCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'item_id',
        'consumer_id',
        'code',
        'type',
        'active',
        'expires_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Relationship: QR code belongs to a seller (nullable)
     */
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    /**
     * Relationship: QR code belongs to an item (nullable)
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Relationship: QR code belongs to a consumer (nullable)
     */
    public function consumer()
    {
        return $this->belongsTo(Consumer::class);
    }

    /**
     * Relationship: QR code has many point transactions
     */
    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class);
    }

    /**
     * Check if QR code is valid (active and not expired)
     */
    public function isValid()
    {
        if (!$this->active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Scope: Get only consumer QR codes
     */
    public function scopeConsumerType($query)
    {
        return $query->where('type', 'consumer_profile');
    }

    /**
     * Scope: Get only seller item QR codes
     */
    public function scopeSellerItemType($query)
    {
        return $query->where('type', 'seller_item');
    }
}