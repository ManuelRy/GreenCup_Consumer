<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SellerPhoto extends Model
{
    use HasFactory;

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

    /**
     * Seller photo belongs to a seller
     */
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
