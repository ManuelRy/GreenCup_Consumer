<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SellerPhoto extends Model
{
    use HasFactory;

    protected $table = 'seller_photos';
    
    protected $fillable = [
        'seller_id',
        'photo_url',
        'caption',
        'category',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function isFrozen(): bool
    {
        return str_starts_with($photo->caption ?? '', '[FROZEN]');
    }

    public function trimCaption(): string {
        return preg_replace('/^\[frozen\]\s*/i', '', $this->caption);
    }
}
