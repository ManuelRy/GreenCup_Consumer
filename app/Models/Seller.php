<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_name',
        'email',
        'description',
        'working_hours',
        'password',
        'address',
        'latitude',
        'longitude',
        'photo_url',
        'phone',
        'is_active',
        'total_points',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude'  => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function rewards() {
        return $this->hasMany(Item::class);
    }
    /**
     * Scope: only active stores with valid coords.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
                     ->whereNotNull('latitude')
                     ->whereNotNull('longitude');
    }

    /**
     * Seller has many QR codes.
     */
    public function qrCodes()
    {
        return $this->hasMany(QrCode::class);
    }

    /**
     * Seller has many point transactions.
     */
    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class, 'seller_id');
    }

    /**
     * Seller has many photos
     */
    public function photos()
    {
        return $this->hasMany(SellerPhoto::class)->orderByDesc('is_featured')->orderBy('sort_order')->orderByDesc('created_at');
    }
}
