<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Apartment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $totalAmount = fake()->randomFloat(2, 10, 100);
        $platformFee = $totalAmount * 0.05;

        return [
            'apartment_id' => Apartment::factory(),
            'buyer_id' => User::factory()->state(['role' => 'customer']),
            'seller_id' => User::factory()->state(['role' => 'owner']),
            'order_no' => 'ORD-' . strtoupper(Str::random(10)),
            'total_amount' => $totalAmount,
            'platform_fee' => $platformFee,
            'seller_amount' => $totalAmount - $platformFee,
            'status' => 'pending',
            'pickup_location' => 'Lobby Utama',
            'pickup_time' => now()->addDay(),
            'payment_method' => 'cash',
            'payment_status' => 'pending',
        ];
    }

    public function paid(): static
    {
        return $this->state(fn(array $attributes) => [
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'completed',
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function preparing(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'preparing',
        ]);
    }

    public function ready(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'ready',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    public function onlinePayment(): static
    {
        return $this->state(fn(array $attributes) => [
            'payment_method' => 'online',
        ]);
    }

    public function qrPayment(): static
    {
        return $this->state(fn(array $attributes) => [
            'payment_method' => 'qr',
        ]);
    }
}
