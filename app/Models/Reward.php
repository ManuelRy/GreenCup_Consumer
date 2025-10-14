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

    public function isExpiringSoon(): bool
    {
        if ($this->isExpired()) {
            return false;
        }
        $now = Carbon::now();
        $hoursUntilExpiry = $now->diffInHours($this->valid_until, false);
        return $hoursUntilExpiry <= 48 && $hoursUntilExpiry > 0;
    }

    public function isComingSoon(): bool
    {
        return !$this->hasStarted();
    }

    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }
        if ($this->isExpired()) {
            return 'expired';
        }
        if ($this->remaining_stock <= 0) {
            return 'out_of_stock';
        }
        if ($this->isComingSoon()) {
            return 'coming_soon';
        }
        if ($this->isExpiringSoon()) {
            return 'expiring_soon';
        }
        return 'available';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'expired' => 'Expired',
            'out_of_stock' => 'Out of Stock',
            'coming_soon' => 'Coming Soon',
            'expiring_soon' => 'Expiring Soon',
            'inactive' => 'Inactive',
            default => 'Available'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'expired' => 'danger',
            'out_of_stock' => 'secondary',
            'coming_soon' => 'info',
            'expiring_soon' => 'warning',
            'inactive' => 'dark',
            default => 'success'
        };
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

    public function getExpiryDetailsAttribute(): array
    {
        $now = Carbon::now();

        if ($this->isExpired()) {
            return [
                'status' => 'expired',
                'text' => 'Expired ' . $this->valid_until->diffForHumans(),
                'date' => $this->valid_until->format('M d, Y'),
                'urgency' => 'expired'
            ];
        }

        if ($this->isComingSoon()) {
            return [
                'status' => 'coming_soon',
                'text' => 'Starts ' . $this->valid_from->diffForHumans(),
                'date' => $this->valid_from->format('M d, Y'),
                'urgency' => 'future'
            ];
        }

        $hoursUntilExpiry = $now->diffInHours($this->valid_until, false);

        if ($hoursUntilExpiry <= 24) {
            return [
                'status' => 'urgent',
                'text' => 'Expires in ' . $now->diffForHumans($this->valid_until, true),
                'date' => $this->valid_until->format('M d, Y h:i A'),
                'urgency' => 'urgent'
            ];
        }

        if ($hoursUntilExpiry <= 48) {
            return [
                'status' => 'warning',
                'text' => 'Expires ' . $this->valid_until->diffForHumans(),
                'date' => $this->valid_until->format('M d, Y'),
                'urgency' => 'soon'
            ];
        }

        return [
            'status' => 'normal',
            'text' => 'Valid until ' . $this->valid_until->format('M d, Y'),
            'date' => $this->valid_until->format('M d, Y'),
            'urgency' => 'normal'
        ];
    }

    public function canRedeemQuantity(int $quantity): bool
    {
        return $this->remaining_stock >= $quantity && $quantity > 0;
    }
}
