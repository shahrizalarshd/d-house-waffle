<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'type',
        'description',
        'stamps_change',
    ];

    protected $casts = [
        'stamps_change' => 'integer',
    ];

    // Transaction types
    const TYPE_STAMP_EARNED = 'stamp_earned';
    const TYPE_DISCOUNT_UNLOCKED = 'discount_unlocked';
    const TYPE_DISCOUNT_USED = 'discount_used';
    const TYPE_TIER_UPGRADED = 'tier_upgraded';
    const TYPE_STAMP_REVERSED = 'stamp_reversed';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scope for a specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for recent transactions
     */
    public function scopeRecent($query, int $limit = 10)
    {
        return $query->latest()->limit($limit);
    }
}
