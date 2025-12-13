<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'service_fee_percent',
        'pickup_location',
        'pickup_start_time',
        'pickup_end_time',
        'status',
        'payment_online_enabled',
        'payment_qr_enabled',
        'payment_cash_enabled',
    ];

    protected $casts = [
        'service_fee_percent' => 'decimal:2',
        'payment_online_enabled' => 'boolean',
        'payment_qr_enabled' => 'boolean',
        'payment_cash_enabled' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
