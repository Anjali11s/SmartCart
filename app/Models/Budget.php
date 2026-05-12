<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount'
    ];

    // Relationship: Budget belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Check if budget is exceeded by given amount
    public function isExceededBy($amount)
    {
        return $amount > $this->amount;
    }

    // Get remaining budget after subtracting amount
    public function getRemainingAfter($amount)
    {
        return max(0, $this->amount - $amount);
    }

    // Get percentage used for given amount
    public function getPercentageUsed($amount)
    {
        if ($this->amount == 0) return 0;
        return min(100, ($amount / $this->amount) * 100);
    }
}