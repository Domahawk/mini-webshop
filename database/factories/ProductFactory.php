<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->name;

        return [
            'name' => $name,
            'description' => $this->faker->text,
            'price' => $this->faker->randomFloat(2, 10, 200),
            'sku' => $this->generateSku($name),
            'published' => 1,
        ];
    }

    private function generateSku(string $name): string
    {
        $nameArray = explode(' ', $name);
        $sku = $nameArray[0][0] . $nameArray[1][0];

        return $sku . now()->format('u');
    }
}
