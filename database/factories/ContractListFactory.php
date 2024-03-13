<?php

namespace Database\Factories;

use App\Models\ContractList;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContractList>
 */
class ContractListFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'price' => $this->faker->randomFloat(2, 9, 199)
        ];
    }
}
