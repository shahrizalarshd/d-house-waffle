<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'apartment_id',
        'buyer_id',
        'seller_id',
        'order_no',
        'total_amount',
        'platform_fee',
        'seller_amount',
        'subtotal',
        'discount_amount',
        'discount_type',
        'discount_details',
        'status',
        'pickup_location',
        'pickup_time',
        'payment_status',
        'payment_ref',
        'payment_method',
        'paid_at',
        'payment_proof',
        'payment_notes',
        // Guest fields
        'guest_name',
        'guest_phone',
        'guest_block',
        'guest_unit_no',
        'guest_token',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'seller_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_details' => 'array',
        'pickup_time' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // Status checks
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isPreparing()
    {
        return $this->status === 'preparing';
    }

    public function isReady()
    {
        return $this->status === 'ready';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    // Payment method checks
    public function isCashPayment()
    {
        return $this->payment_method === 'cash';
    }

    public function isOnlinePayment()
    {
        return $this->payment_method === 'online';
    }

    public function isQRPayment()
    {
        return $this->payment_method === 'qr';
    }

    public function hasPaymentProof()
    {
        return !empty($this->payment_proof);
    }

    public function getPaymentProofUrl()
    {
        return $this->payment_proof 
            ? asset('storage/' . $this->payment_proof)
            : null;
    }

    // Guest order checks
    public function isGuestOrder(): bool
    {
        return is_null($this->buyer_id);
    }

    public function isRegisteredOrder(): bool
    {
        return !is_null($this->buyer_id);
    }

    /**
     * Get customer name (works for both guest and registered)
     */
    public function getCustomerName(): string
    {
        if ($this->isGuestOrder()) {
            return $this->guest_name ?? 'Guest';
        }
        return $this->buyer->name ?? 'Unknown';
    }

    /**
     * Get customer phone (works for both guest and registered)
     */
    public function getCustomerPhone(): ?string
    {
        if ($this->isGuestOrder()) {
            return $this->guest_phone;
        }
        return $this->buyer->phone ?? null;
    }

    /**
     * Get customer block (works for both guest and registered)
     */
    public function getCustomerBlock(): ?string
    {
        if ($this->isGuestOrder()) {
            return $this->guest_block;
        }
        return $this->buyer->block ?? null;
    }

    /**
     * Get customer unit number (works for both guest and registered)
     */
    public function getCustomerUnitNo(): ?string
    {
        if ($this->isGuestOrder()) {
            return $this->guest_unit_no;
        }
        return $this->buyer->unit_no ?? null;
    }

    /**
     * Get full customer address
     */
    public function getCustomerAddress(): string
    {
        $block = $this->getCustomerBlock();
        $unit = $this->getCustomerUnitNo();
        
        if ($block && $unit) {
            return "Block {$block}, Unit {$unit}";
        }
        return $unit ?? $block ?? 'No address';
    }

    /**
     * Check if order has discount applied
     */
    public function hasDiscount(): bool
    {
        return $this->discount_amount > 0;
    }

    /**
     * Scope to find order by guest token
     */
    public function scopeByGuestToken($query, string $token)
    {
        return $query->where('guest_token', $token);
    }

    /**
     * Scope for guest orders only
     */
    public function scopeGuestOrders($query)
    {
        return $query->whereNull('buyer_id');
    }

    /**
     * Scope for registered user orders only
     */
    public function scopeRegisteredOrders($query)
    {
        return $query->whereNotNull('buyer_id');
    }
}
