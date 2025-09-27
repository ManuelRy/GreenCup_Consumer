<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
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

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return null;
        }
        
        // If it's already a full URL, return as is
        if (str_starts_with($this->image_path, 'http')) {
            return $this->image_path;
        }
        
        // If it starts with 'images/', treat it as a public asset
        if (str_starts_with($this->image_path, 'images/')) {
            return asset($this->image_path);
        }
        
        // Otherwise, assume it's in storage
        return asset('storage/' . $this->image_path);
    }

    public function isValid(): bool
    {
        $now = Carbon::now();

        $hasStock = $this->quantity > $this->quantity_redeemed;
        $withinDateRange = $now->between($this->valid_from, $this->valid_until);
        $isActive = (bool) $this->is_active;

        return $hasStock && $withinDateRange && $isActive;
    }
}
