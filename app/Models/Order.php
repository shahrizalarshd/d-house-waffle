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
        'status',
        'pickup_location',
        'pickup_time',
        'payment_status',
        'payment_ref',
        'payment_method',
        'paid_at',
        'payment_proof',
        'payment_notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'seller_amount' => 'decimal:2',
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
}
