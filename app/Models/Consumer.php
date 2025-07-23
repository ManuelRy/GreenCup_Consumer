<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Consumer extends Authenticatable
{
    use HasFactory, Notifiable;

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
        'email_verified_at' => 'datetime',
    ];

    /**
     * Automatically hash password when setting it
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Relationship: Consumer has many point transactions
     */
    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class);
    }

    /**
     * Relationship: Consumer has claimed many QR codes
     */
    public function claimedQrCodes()
    {
        return $this->hasMany(QrCode::class, 'claimed_by_consumer_id');
    }

    /**
     * Get consumer's total available points
     */
    public function getAvailablePoints()
    {
        // Return 0 if no transactions exist yet
        if (!$this->pointTransactions()->exists()) {
            return 0;
        }
        
        $earned = $this->pointTransactions()->where('type', 'earn')->sum('points');
        $spent = $this->pointTransactions()->where('type', 'spend')->sum('points');
        
        return $earned - $spent;
    }

    /**
     * Get consumer's point transaction history
     */
    public function getPointHistory($limit = 10)
    {
        return $this->pointTransactions()
                    ->with(['seller'])
                    ->latest('scanned_at')
                    ->take($limit)
                    ->get();
    }

    /**
     * Get recent transactions
     */
    public function getRecentTransactions($days = 30)
    {
        return $this->pointTransactions()
                    ->with(['seller', 'qrCode'])
                    ->where('created_at', '>=', now()->subDays($days))
                    ->latest('scanned_at')
                    ->get();
    }

    /**
     * Get transactions by seller
     */
    public function getTransactionsBySeller($sellerId)
    {
        return $this->pointTransactions()
                    ->where('seller_id', $sellerId)
                    ->with(['qrCode'])
                    ->latest('scanned_at')
                    ->get();
    }

    /**
     * Check if consumer can claim a QR code
     */
    public function canClaimQrCode(QrCode $qrCode)
    {
        // Check if QR code is valid
        if (!$qrCode->isValid()) {
            return false;
        }

        // Check if already claimed
        if ($qrCode->status === 'claimed') {
            return false;
        }

        // Check if it's a transaction type QR code
        if ($qrCode->type !== 'transaction') {
            return false;
        }

        return true;
    }

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Set the token value for the "remember me" session.
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}