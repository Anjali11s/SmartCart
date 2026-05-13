<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'full_name', 'phone', 'alternate_phone', 'address_line1',
        'address_line2', 'landmark', 'city', 'state', 'pincode', 'country',
        'is_default', 'address_type'
    ];

    protected $casts = [
        'is_default' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Get full address as string
    public function getFullAddressAttribute()
    {
        $address = $this->address_line1;
        if ($this->address_line2) {
            $address .= ', ' . $this->address_line2;
        }
        if ($this->landmark) {
            $address .= ', ' . $this->landmark;
        }
        $address .= ', ' . $this->city . ', ' . $this->state . ' - ' . $this->pincode;
        return $address;
    }

    // Get formatted address for display
    public function getFormattedAddressAttribute()
    {
        return "{$this->full_name}, {$this->phone}<br>{$this->full_address}";
    }
}