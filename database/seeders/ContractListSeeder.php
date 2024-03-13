<?php

namespace Database\Seeders;

use App\Models\ContractList;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContractListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();

        foreach (User::all() as $index => $user) {
            if ($index % 2 !== 0) {
                $product = $products[$index] ?? null;

                if (empty($product)) {
                    continue;
                }

                ContractList::factory()->create([
                    'user_id' => $user->id,
                    'sku' => $product->sku,
                ]);
            }
        }
    }
}
