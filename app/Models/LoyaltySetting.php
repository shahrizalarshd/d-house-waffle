<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'apartment_id',
        'guest_checkout_enabled',
        'guest_pending_limit',
        'loyalty_enabled',
        'stamps_required',
        'stamp_discount_percent',
        'discount_validity_days',
        'tiers_enabled',
        'silver_threshold',
        'gold_threshold',
        'silver_bonus_percent',
        'gold_bonus_percent',
    ];

    protected $casts = [
        'guest_checkout_enabled' => 'boolean',
        'loyalty_enabled' => 'boolean',
        'tiers_enabled' => 'boolean',
        'stamp_discount_percent' => 'decimal:2',
        'silver_bonus_percent' => 'decimal:2',
        'gold_bonus_percent' => 'decimal:2',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    /**
     * Get or create settings for an apartment
     */
    public static function getForApartment(int $apartmentId): self
    {
        return self::firstOrCreate(
            ['apartment_id' => $apartmentId],
            [
                'guest_checkout_enabled' => true,
                'guest_pending_limit' => 3,
                'loyalty_enabled' => true,
                'stamps_required' => 5,
                'stamp_discount_percent' => 10.00,
                'discount_validity_days' => 30,
                'tiers_enabled' => false,
                'silver_threshold' => 10,
                'gold_threshold' => 25,
                'silver_bonus_percent' => 2.00,
                'gold_bonus_percent' => 5.00,
            ]
        );
    }
}
