<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

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
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'password' => 'hashed',
    ];

    /**
     * Get consumer's available points (earned - spent)
     */
    public function getAvailablePoints()
    {
        $totalEarned = DB::table('point_transactions')
            ->where('consumer_id', $this->id)
            ->where('type', 'earn')
            ->sum('points');
            
        $totalSpent = DB::table('point_transactions')
            ->where('consumer_id', $this->id)
            ->where('type', 'spend')
            ->sum('points');
            
        return $totalEarned - $totalSpent;
    }

    /**
     * Get consumer's total earned points
     */
    public function getTotalEarnedPoints()
    {
        return DB::table('point_transactions')
            ->where('consumer_id', $this->id)
            ->where('type', 'earn')
            ->sum('points');
    }

    /**
     * Get consumer's total spent points
     */
    public function getTotalSpentPoints()
    {
        return DB::table('point_transactions')
            ->where('consumer_id', $this->id)
            ->where('type', 'spend')
            ->sum('points');
    }

    /**
     * Get consumer's recent transactions
     */
    public function getRecentTransactions($limit = 5)
    {
        return DB::table('point_transactions as pt')
            ->leftJoin('sellers as s', 's.id', '=', 'pt.seller_id')
            ->where('pt.consumer_id', $this->id)
            ->select([
                'pt.id',
                'pt.points',
                'pt.type',
                'pt.description',
                'pt.receipt_code',
                'pt.scanned_at',
                's.business_name as store_name'
            ])
            ->orderBy('pt.scanned_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Relationship with point transactions
     */
    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class);
    }
}