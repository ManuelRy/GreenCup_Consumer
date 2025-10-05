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
        'photo_caption',
        'caption',
        'category',
        'is_featured',
        'sort_order'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'sort_order' => 'integer'
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function isFrozen(): bool
    {
        return str_starts_with($this->photo_caption ?? '', '[FROZEN] ');
    }

    /**
     * Get the original caption without the frozen prefix
     */
    public function getOriginalCaptionAttribute(): ?string
    {
        $caption = $this->photo_caption ?? '';

        if (str_starts_with($caption, '[FROZEN] ')) {
            return substr($caption, 9);
        }

        return $caption ?: null;
    }
}
