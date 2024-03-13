<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PriceListSeeder::class,
            CountrySeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            PriceListProductSeeder::class,
            CustomerSeeder::class,
            ContractListSeeder::class,
            AddressSeeder::class,
        ]);
    }
}
