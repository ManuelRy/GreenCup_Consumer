<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'points_per_unit',
        'image_url',
    ];
    protected $casts = [
        'points_per_unit' => 'integer',
    ];

    public function qrCodes(): HasMany
    {
        return $this->hasMany(QrCode::class);
    }
}
