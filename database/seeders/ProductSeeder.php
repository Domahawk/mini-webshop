<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    private const CATEGORY_PRODUCTS = [
    'Dresses' => [
        'Summer Floral Dress - Pink Cotton' => 59.99,
        'Elegant Evening Gown - Black Silk' => 129.99,
    ],
    'Skirts' => [
        'Pleated Midi Skirt - Navy Polyester' => 34.99,
    ],
    'Tops' => [
        'Silk Blouse - Ivory Silk'     => 45.99,
        'Casual T-shirt - Grey Cotton' => 19.99,
    ],
    'Pants' => [
        'High-Waisted Trousers - Navy Wool' => 89.99,
        'Slim Fit Jeans - Light Blue Denim' => 54.99,
    ],
    'Women Shoes' => [
        'Stiletto Heels - Black Leather' => 79.99,
        'Running Sneakers - White Mesh'  => 49.99,
    ],
    'Shirts' => [
        'Casual Linen Shirt - White Linen'  => 39.99,
        'Formal Dress Shirt - Black Cotton' => 59.99,
    ],
    'Jeans' => [
        'Rugged Denim Jeans - Dark Blue Denim' => 69.99,
        'Chinos - Beige Cotton'                => 49.99,
    ],
    'Suits' => [
        'Business Suit - Grey Wool' => 299.99,
        'Tuxedo - Black Silk'       => 499.99,
    ],
    'Men Shoes' => [
        'Classic Leather Loafers - Brown Leather' => 99.99,
        'Sporty Sneakers - Black Mesh'            => 59.99,
    ],
];

    public function run(): void
    {
        $categories = Category::whereIn('name', array_keys(self::CATEGORY_PRODUCTS))->get();

        foreach ($categories as $category) {
            $this->createProducts($category);
        }
    }

    private function createProducts(Category $category): void
    {
        foreach (self::CATEGORY_PRODUCTS[$category->name] as $productName => $price) {
            Product::create([
                'name' => $productName,
                'description' => $productName,
                'sku' => $this->generateSku($productName),
                'price' => $price,
                'published' => 1,
            ])->categories()->attach($category->id);
        }
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
