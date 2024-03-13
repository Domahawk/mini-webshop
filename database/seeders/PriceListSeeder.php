<?php

namespace Database\Seeders;

use App\Models\PriceList;
use Illuminate\Database\Seeder;

class PriceListSeeder extends Seeder
{
    private const PRICE_LISTS = [
        'RETAIL' => 'Retail customers',
        'WHOLESALE' => 'Wholesale customers'
    ];

    public function run(): void
    {
        foreach (self::PRICE_LISTS as $name => $description) {
            PriceList::factory()->create([
                'name' => $name,
                'description' => $description,
            ]);
        }

    }
}
