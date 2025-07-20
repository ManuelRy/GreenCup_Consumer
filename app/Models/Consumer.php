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
     * Relationship: Consumer has one QR code
     */
    public function qrCode()
    {
        return $this->hasOne(QrCode::class, 'consumer_id');
    }

    /**
     * Relationship: Consumer has many point transactions
     */
    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class);
    }

    /**
     * Generate QR code for this consumer
     */
    public function generateQrCode()
    {
        // Check if consumer already has a QR code
        if ($this->qrCode) {
            return $this->qrCode;
        }

        // Generate unique secure token for this consumer
        $token = 'GC_' . $this->id . '_' . strtoupper(substr(md5($this->email . time()), 0, 8));
        
        // Create URL that sellers will scan to award points to this consumer
        $qrUrl = url('/award-points/' . $token);

        return QrCode::create([
            'consumer_id' => $this->id,
            'seller_id' => null,
            'item_id' => null,
            'code' => $qrUrl, // Now contains a functional URL that identifies this consumer
            'type' => 'consumer_profile',
            'active' => true,
            'expires_at' => null, // Consumer QR codes don't expire
        ]);
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
     * Boot method to auto-generate QR code when consumer is created
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($consumer) {
            // Generate QR code after consumer is created
            $consumer->generateQrCode();
        });
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