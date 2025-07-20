<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consumer extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'phone_number',
        'gender',
        'date_of_birth',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'password' => 'hashed',
    ];

    // Relationship to point transactions
    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class);
    }

    // Get total points earned
    public function getTotalPointsEarnedAttribute(): int
    {
        return $this->pointTransactions()
            ->where('type', 'earn')
            ->sum('points') ?? 0;
    }

    // Get total points spent
    public function getTotalPointsSpentAttribute(): int
    {
        return $this->pointTransactions()
            ->where('type', 'spend')
            ->sum('points') ?? 0;
    }

    // Get available points (earned - spent)
    public function getAvailablePointsAttribute(): int
    {
        return $this->total_points_earned - $this->total_points_spent;
    }

    // Get current rank based on total earned points
    public function getCurrentRankAttribute()
    {
        $totalEarned = $this->total_points_earned;
        
        return Rank::where('min_points', '<=', $totalEarned)
            ->orderBy('min_points', 'desc')
            ->first();
    }

    // Get recent transactions with details
    public function getRecentTransactionsAttribute()
    {
        return $this->pointTransactions()
            ->with(['seller', 'qrCode.item'])
            ->orderBy('scanned_at', 'desc')
            ->limit(10)
            ->get();
    }
}