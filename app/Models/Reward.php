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
    public function isValid(): bool
    {
        $now = Carbon::now();

        $hasStock = $this->quantity > $this->quantity_redeemed;
        $withinDateRange = $now->between($this->valid_from, $this->valid_until);
        $isActive = (bool) $this->is_active;

        return $hasStock && $withinDateRange && $isActive;
    }
}
