<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    private const CATEGORIES = [
        'Women' => [
            'Dresses' => [
                'Skirts',
            ],
            'Tops',
            'Pants',
            'Women Shoes',
        ],
        'Men' => [
            'Shirts',
            'Jeans',
            'Suits',
            'Men Shoes',
        ],
    ];
    public function run(): void
    {
        $this->createCategories();
    }

    private function createCategories(array $categories = self::CATEGORIES, string $parentId = null): void
    {
        foreach ($categories as $key => $element) {
            if (is_array($element)) {
                $parent = Category::factory()->create([
                        'name' => $key,
                        'description' => "$key products",
                        'parent_id' => $parentId
                ]);

                $this->createCategories($element, $parent->id);

                continue;
            }

            Category::factory()->create([
                'name' => $element,
                'description' => "$element products",
                'parent_id' => $parentId
            ]);
        }
    }
}
