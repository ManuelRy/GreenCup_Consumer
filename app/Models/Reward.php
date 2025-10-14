<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Traits\NormalizesRemoteUrl;

class Reward extends Model
{
    use NormalizesRemoteUrl;

    protected $fillable = [
        'name',
        'description',
        'points_required',
        'quantity',
        'quantity_redeemed',
        'image_path',
        'valid_from',
        'valid_until',
        'is_active',
        'seller_id',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function getImagePathAttribute($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (str_starts_with($value, 'images/')) {
            return asset($value);
        }

        return $this->normalizeRemoteUrl($value);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path;
    }

    public function isValid(): bool
    {
        $now = Carbon::now();

        $hasStock = $this->quantity > $this->quantity_redeemed;
        $withinDateRange = $now->between($this->valid_from, $this->valid_until);
        $isActive = (bool) $this->is_active;

        return $hasStock && $withinDateRange && $isActive;
    }

    public function isExpired(): bool
    {
        return Carbon::now()->isAfter($this->valid_until);
    }

    public function hasStarted(): bool
    {
        return Carbon::now()->isAfter($this->valid_from);
    }

    public function getRemainingStockAttribute(): int
    {
        return max(0, $this->quantity - $this->quantity_redeemed);
    }

    public function getTimeUntilStartAttribute(): ?string
    {
        if ($this->hasStarted()) {
            return null;
        }
        return $this->valid_from->diffForHumans();
    }

    public function getTimeUntilExpiryAttribute(): ?string
    {
        if ($this->isExpired()) {
            return 'Expired';
        }
        return $this->valid_until->diffForHumans();
    }

    public function canRedeemQuantity(int $quantity): bool
    {
        return $this->remaining_stock >= $quantity && $quantity > 0;
    }
}
