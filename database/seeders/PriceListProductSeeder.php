<?php

namespace Database\Seeders;

use App\Models\PriceList;
use App\Models\PriceListProduct;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class PriceListProductSeeder extends Seeder
{
    public function run(): void
    {
        $priceLists = PriceList::all();
        $products = Product::all();
        $this->linkProductsAndPriceLists($products, $priceLists);
    }

    private function linkProductsAndPriceLists(Collection $products, Collection $priceLists): void
    {
        /** @var Product $product */
        foreach ($products as $index => $product) {
            PriceListProduct::create([
                'price' => $product->price + 10.00,
                'sku' => $product->sku,
                'price_list_id' => $priceLists[0]->id,
            ]);

            if ($index % 2 > 0) {
                PriceListProduct::create([
                    'price' => $product->price - 10.00,
                    'sku' => $product->sku,
                    'price_list_id' => $priceLists[1]->id
                ]);
            }
        }
    }
}
