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
}
