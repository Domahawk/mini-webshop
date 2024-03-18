<?php

namespace Database\Factories;

use App\Models\PriceList;
use App\Models\PriceListProduct;
use App\Models\Product;
use App\Models\User;
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
            'sku' => Product::factory()->create(),
            'price_list_id' => PriceList::factory()->create(),
            'price' => $this->faker->randomFloat(2, 9, 199),
        ];
    }
}
