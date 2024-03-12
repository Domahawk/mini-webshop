<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Category;
use App\Models\ContractList;
use App\Models\PriceList;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    private const CATEGORY_PRODUCTS = [
        'Women' => [
            'type' => 'category',
            'Dresses' => [
                'type' => 'category',
                'Summer Floral Dress - Pink Cotton' => 59.99,
                'Elegant Evening Gown - Black Silk' => 129.99,
            ],
            'Tops' => [
                'type' => 'category',
                'Silk Blouse - Ivory Silk' => 45.99,
                'Casual T-shirt - Grey Cotton' => 19.99,
            ],
            'Pants' => [
                'type' => 'category',
                'High-Waisted Trousers - Navy Wool' => 89.99,
                'Slim Fit Jeans - Light Blue Denim' => 54.99,
            ],
            'Women Shoes' => [
                'type' => 'category',
                'Stiletto Heels - Black Leather' => 79.99,
                'Running Sneakers - White Mesh' => 49.99,
            ],
        ],
        'Men' => [
            'type' => 'category',
            'Shirts' => [
                'type' => 'category',
                'Casual Linen Shirt - White Linen' => 39.99,
                'Formal Dress Shirt - Black Cotton' => 59.99,
            ],
            'Jeans' => [
                'type' => 'category',
                'Rugged Denim Jeans - Dark Blue Denim' => 69.99,
                'Chinos - Beige Cotton' => 49.99,
            ],
            'Suits' => [
                'type' => 'category',
                'Business Suit - Grey Wool' => 299.99,
                'Tuxedo - Black Silk' => 499.99,
            ],
            'Men Shoes' => [
                'type' => 'category',
                'Classic Leather Loafers - Brown Leather' => 99.99,
                'Sporty Sneakers - Black Mesh' => 59.99,
            ],
        ],
    ];

    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $customers = User::factory()->count(10)->create();
        User::factory()->createOne([
            'role'=> Role::ADMIN->name
        ]);
        $products = $this->createProductsAndCategories(self::CATEGORY_PRODUCTS);

        /** @var Product $product */
        foreach ($products as $index => $product) {
            PriceList::create([
                'name' => "$product->name Retail",
                'price' => $product->price + 10.00,
                'sku' => $product->sku
            ]);

            if ($index % 2 > 0) {
                PriceList::create([
                    'name' => "$product->name Wholesale",
                    'price' => $product->price - 10.00,
                    'sku' => $product->sku
                ]);
            }
        }

        foreach ($customers as $index => $user) {
            if ($index % 2 !== 0) {
                $product = $products[$index] ?? null;

                if (empty($product)) {
                    continue;
                }

                ContractList::create([
                    'user_id' => $user->id,
                    'sku' => $product->sku,
                    'price' => $product->price - round($product->price * 0.13, 2, PHP_ROUND_HALF_DOWN),
                ]);
            }
        }

        $this->call([
            PriceModifierSeeder::class,
        ]);
    }

    private function createProductsAndCategories(array $categories, string $parentId = null): array
    {
        $products = [];

        foreach ($categories as $key => $element) {
            if ($key === 'type') {
                continue;
            }

            if ($element['type'] ?? 'product' === 'category') {
                $parent = Category::create([
                    'name' => $key,
                    'description' => "$key products",
                    'parent_id' => $parentId
                ]);

                $products = array_merge($products, $this->createProductsAndCategories($element, $parent->id));

                continue;
            }

            $product = Product::create([
                'name' => $key,
                'description' => $key,
                'sku' => $this->generateSku($key),
                'price' => $element,
                'published' => 1,
            ]);
            $product->categories()->attach($parentId);

            $products[] = $product;
        }

        return $products;
    }

    private function generateSku(string $key): string
    {
        $name = collect(explode(' ', $key))
            ->map(fn (string $part) => $part !== '-' ? $part[0] : null)
            ->take(5)
            ->implode(fn (string | null $part) => $part ?? null)
        ;

        return $name . now()->format('u');
    }
}
