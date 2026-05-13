<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'quantity',
        'image',
        'seller_id'
    ];

    // Seller relation
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
    
     // Image URL accessor
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : 'https://placehold.co/400x300?text=No+Image';
    }

    public function isInStock()
    {
        return $this->quantity > 0;
    }
}