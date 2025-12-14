<?php

namespace Database\Factories;

use App\Models\Apartment;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApartmentFactory extends Factory
{
    protected $model = Apartment::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Apartment',
            'address' => fake()->address(),
            'service_fee_percent' => fake()->randomFloat(2, 0, 10),
            'pickup_location' => 'Lobby Utama',
            'pickup_start_time' => '09:00:00',
            'pickup_end_time' => '21:00:00',
            'status' => 'active',
            'payment_online_enabled' => true,
            'payment_qr_enabled' => true,
            'payment_cash_enabled' => true,
        ];
    }
}
