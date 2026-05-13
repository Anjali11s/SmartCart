<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class OrderItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Use withTrashed() to show product info even if deleted
    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }
    
    // Get product name (handles deleted products)
    public function getProductDisplayNameAttribute()
    {
        if ($this->product && !$this->product->trashed()) {
            return $this->product->name;
        }
        return '<span class="text-red-500 line-through">Product Unavailable (Deleted)</span>';
    }
    
    // Check if product is available
    public function getIsProductAvailableAttribute()
    {
        return $this->product && !$this->product->trashed();
    }
}