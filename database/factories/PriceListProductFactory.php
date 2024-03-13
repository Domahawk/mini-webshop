<?php

namespace Database\Factories;

use App\Models\PriceListProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PriceListProduct>
 */
class PriceListProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'price' => $this->faker->randomFloat(2, 9, 199),
        ];
    }
}
