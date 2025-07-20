<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PointTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'consumer_id',
        'seller_id',
        'qr_code_id',
        'units_scanned',
        'points',
        'type',
        'description',
        'scanned_at',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    /**
     * Relationship: Transaction belongs to a consumer
     */
    public function consumer()
    {
        return $this->belongsTo(Consumer::class);
    }

    /**
     * Relationship: Transaction belongs to a seller
     */
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    /**
     * Relationship: Transaction belongs to a QR code
     */
    public function qrCode()
    {
        return $this->belongsTo(QrCode::class);
    }

    /**
     * Scope: Get earning transactions
     */
    public function scopeEarnings($query)
    {
        return $query->where('type', 'earn');
    }

    /**
     * Scope: Get spending transactions
     */
    public function scopeSpending($query)
    {
        return $query->where('type', 'spend');
    }
}