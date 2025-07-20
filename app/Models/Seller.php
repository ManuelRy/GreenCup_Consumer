<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_name',
        'email',
        'description',
        'working_hours',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function qrCodes(): HasMany
    {
        return $this->hasMany(QrCode::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(SellerLocation::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(SellerPhoto::class);
    }
}