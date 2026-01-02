<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'apartment_id',
        'name',
        'email',
        'password',
        'phone',
        'qr_code_image',
        'qr_code_type',
        'qr_code_instructions',
        'role',
        'unit_no',
        'block',
        'status',
        // Loyalty fields
        'loyalty_stamps',
        'lifetime_orders',
        'lifetime_spent',
        'loyalty_tier',
        'loyalty_discount_available',
        'loyalty_discount_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'loyalty_discount_available' => 'boolean',
            'loyalty_discount_expires_at' => 'datetime',
            'lifetime_spent' => 'decimal:2',
        ];
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    public function buyerOrders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function sellerOrders()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    // Alias for buyerOrders for easier access
    public function orders()
    {
        return $this->buyerOrders();
    }

    public function loyaltyTransactions()
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    // New role structure for D'house Waffle
    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    public function isOwner()
    {
        return $this->role === 'owner';
    }

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    // Staff or Owner can manage the business
    public function canManageBusiness()
    {
        return in_array($this->role, ['staff', 'owner']);
    }

    // Only owner can access business settings & revenue
    public function canAccessBusinessSettings()
    {
        return $this->role === 'owner';
    }

    // Backward compatibility (temporary)
    public function isBuyer()
    {
        return $this->isCustomer();
    }

    public function isSeller()
    {
        return $this->canManageBusiness();
    }

    public function isAdmin()
    {
        return $this->isOwner();
    }

    public function hasQRCode()
    {
        return !empty($this->qr_code_image);
    }

    public function getQRCodeUrl()
    {
        return $this->qr_code_image 
            ? asset('storage/' . $this->qr_code_image)
            : null;
    }

    // ==========================================
    // Loyalty System Methods
    // ==========================================

    /**
     * Check if user has available loyalty discount
     */
    public function hasLoyaltyDiscount(): bool
    {
        return $this->loyalty_discount_available && 
               $this->loyalty_discount_expires_at && 
               $this->loyalty_discount_expires_at->isFuture();
    }

    /**
     * Get loyalty tier display name
     */
    public function getLoyaltyTierDisplay(): string
    {
        return match($this->loyalty_tier) {
            'gold' => '🥇 Gold',
            'silver' => '🥈 Silver',
            default => '🥉 Bronze',
        };
    }

    /**
     * Get loyalty tier emoji
     */
    public function getLoyaltyTierEmoji(): string
    {
        return match($this->loyalty_tier) {
            'gold' => '🥇',
            'silver' => '🥈',
            default => '🥉',
        };
    }

    /**
     * Get completed orders count (for loyalty)
     */
    public function getCompletedOrdersCount(): int
    {
        return $this->buyerOrders()->where('status', 'completed')->count();
    }

    /**
     * Calculate stamps progress percentage
     */
    public function getStampsProgressPercent(int $stampsRequired = 5): int
    {
        if ($stampsRequired <= 0) return 100;
        return min(100, (int) round(($this->loyalty_stamps / $stampsRequired) * 100));
    }

    /**
     * Get stamps remaining until discount
     */
    public function getStampsRemaining(int $stampsRequired = 5): int
    {
        return max(0, $stampsRequired - $this->loyalty_stamps);
    }

    /**
     * Check if user is close to unlocking discount (1-2 stamps away)
     */
    public function isCloseToDiscount(int $stampsRequired = 5): bool
    {
        $remaining = $this->getStampsRemaining($stampsRequired);
        return $remaining > 0 && $remaining <= 2;
    }
}
