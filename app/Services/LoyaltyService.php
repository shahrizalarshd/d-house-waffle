<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\LoyaltySetting;
use App\Models\LoyaltyTransaction;

class LoyaltyService
{
    /**
     * Get loyalty settings for an apartment
     */
    public function getSettings(int $apartmentId): LoyaltySetting
    {
        return LoyaltySetting::getForApartment($apartmentId);
    }

    /**
     * Award stamp after order completed
     */
    public function awardStamp(User $user, Order $order): void
    {
        $settings = $this->getSettings($order->apartment_id);
        
        if (!$settings->loyalty_enabled) {
            return;
        }
        
        // Add stamp
        $user->increment('loyalty_stamps');
        $user->increment('lifetime_orders');
        $user->increment('lifetime_spent', $order->total_amount);
        
        // Log transaction
        LoyaltyTransaction::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'type' => LoyaltyTransaction::TYPE_STAMP_EARNED,
            'description' => "Earned 1 stamp from Order #{$order->order_no}",
            'stamps_change' => 1,
        ]);
        
        // Check if discount unlocked
        if ($user->loyalty_stamps >= $settings->stamps_required) {
            $this->unlockDiscount($user, $settings);
        }
        
        // Check tier upgrade
        $this->checkTierUpgrade($user, $settings);
    }

    /**
     * Reverse a stamp (e.g., when order is cancelled)
     */
    public function reverseStamp(User $user, Order $order): void
    {
        if ($user->loyalty_stamps > 0) {
            $user->decrement('loyalty_stamps');
            
            LoyaltyTransaction::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'type' => LoyaltyTransaction::TYPE_STAMP_REVERSED,
                'description' => "Stamp reversed - Order #{$order->order_no} cancelled",
                'stamps_change' => -1,
            ]);
        }
    }

    /**
     * Unlock discount when stamps complete
     */
    protected function unlockDiscount(User $user, LoyaltySetting $settings): void
    {
        $user->update([
            'loyalty_stamps' => 0, // Reset stamps
            'loyalty_discount_available' => true,
            'loyalty_discount_expires_at' => now()->addDays($settings->discount_validity_days),
        ]);
        
        LoyaltyTransaction::create([
            'user_id' => $user->id,
            'type' => LoyaltyTransaction::TYPE_DISCOUNT_UNLOCKED,
            'description' => "Unlocked {$settings->stamp_discount_percent}% discount! Valid for {$settings->discount_validity_days} days.",
            'stamps_change' => -$settings->stamps_required,
        ]);
    }

    /**
     * Calculate discount for an order
     */
    public function calculateDiscount(User $user, float $subtotal): array
    {
        $discount = 0;
        $discountDetails = [];
        $settings = $this->getSettings($user->apartment_id);
        
        if (!$settings->loyalty_enabled) {
            return [
                'total_discount' => 0,
                'details' => [],
                'has_discount' => false,
            ];
        }
        
        // Stamp card discount
        if ($user->hasLoyaltyDiscount()) {
            $stampDiscount = $subtotal * ($settings->stamp_discount_percent / 100);
            $discount += $stampDiscount;
            $discountDetails[] = [
                'type' => 'loyalty',
                'name' => 'Loyalty Discount',
                'percent' => $settings->stamp_discount_percent,
                'amount' => $stampDiscount,
            ];
        }
        
        // Tier bonus
        if ($settings->tiers_enabled) {
            $tierPercent = $this->getTierBonusPercent($user, $settings);
            if ($tierPercent > 0) {
                $tierDiscount = $subtotal * ($tierPercent / 100);
                $discount += $tierDiscount;
                $discountDetails[] = [
                    'type' => 'tier_bonus',
                    'name' => ucfirst($user->loyalty_tier) . ' Member Bonus',
                    'tier' => $user->loyalty_tier,
                    'percent' => $tierPercent,
                    'amount' => $tierDiscount,
                ];
            }
        }
        
        return [
            'total_discount' => round($discount, 2),
            'details' => $discountDetails,
            'has_discount' => $discount > 0,
        ];
    }

    /**
     * Mark loyalty discount as used
     */
    public function useDiscount(User $user, Order $order): void
    {
        if ($user->hasLoyaltyDiscount()) {
            $user->update([
                'loyalty_discount_available' => false,
                'loyalty_discount_expires_at' => null,
            ]);
            
            LoyaltyTransaction::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'type' => LoyaltyTransaction::TYPE_DISCOUNT_USED,
                'description' => "Used loyalty discount on Order #{$order->order_no}",
                'stamps_change' => 0,
            ]);
        }
    }

    /**
     * Check and upgrade tier
     */
    protected function checkTierUpgrade(User $user, LoyaltySetting $settings): void
    {
        if (!$settings->tiers_enabled) {
            return;
        }
        
        $newTier = 'bronze';
        if ($user->lifetime_orders >= $settings->gold_threshold) {
            $newTier = 'gold';
        } elseif ($user->lifetime_orders >= $settings->silver_threshold) {
            $newTier = 'silver';
        }
        
        if ($newTier !== $user->loyalty_tier) {
            $oldTier = $user->loyalty_tier;
            $user->update(['loyalty_tier' => $newTier]);
            
            LoyaltyTransaction::create([
                'user_id' => $user->id,
                'type' => LoyaltyTransaction::TYPE_TIER_UPGRADED,
                'description' => "Congratulations! Upgraded from " . ucfirst($oldTier) . " to " . ucfirst($newTier) . "!",
                'stamps_change' => 0,
            ]);
        }
    }

    /**
     * Get tier bonus percentage
     */
    protected function getTierBonusPercent(User $user, LoyaltySetting $settings): float
    {
        return match($user->loyalty_tier) {
            'gold' => (float) $settings->gold_bonus_percent,
            'silver' => (float) $settings->silver_bonus_percent,
            default => 0,
        };
    }

    /**
     * Get loyalty summary for a user (for dashboard display)
     */
    public function getLoyaltySummary(User $user): array
    {
        $settings = $this->getSettings($user->apartment_id);
        
        return [
            'enabled' => $settings->loyalty_enabled,
            'stamps' => $user->loyalty_stamps,
            'stamps_required' => $settings->stamps_required,
            'stamps_remaining' => $user->getStampsRemaining($settings->stamps_required),
            'progress_percent' => $user->getStampsProgressPercent($settings->stamps_required),
            'has_discount' => $user->hasLoyaltyDiscount(),
            'discount_percent' => $settings->stamp_discount_percent,
            'discount_expires_at' => $user->loyalty_discount_expires_at,
            'tier' => $user->loyalty_tier,
            'tier_display' => $user->getLoyaltyTierDisplay(),
            'tier_emoji' => $user->getLoyaltyTierEmoji(),
            'tiers_enabled' => $settings->tiers_enabled,
            'lifetime_orders' => $user->lifetime_orders,
            'lifetime_spent' => $user->lifetime_spent,
            'is_close_to_discount' => $user->isCloseToDiscount($settings->stamps_required),
        ];
    }

    /**
     * Get recent loyalty transactions for a user
     */
    public function getRecentTransactions(User $user, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return LoyaltyTransaction::forUser($user->id)
            ->with('order:id,order_no')
            ->recent($limit)
            ->get();
    }
}

