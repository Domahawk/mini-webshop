<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Country;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'street' => $this->faker->streetAddress,
            'city' => 'Zagreb',
            'postal_code' => 10000,
            'country_code' => Country::factory()->create()->code,
            'state_id' => null,
            'user_id' => User::factory()->create(),
        ];
    }
}
