<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

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
}