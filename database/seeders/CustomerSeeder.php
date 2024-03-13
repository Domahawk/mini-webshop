<?php

namespace Database\Seeders;

use App\Models\PriceList;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $priceLists = PriceList::all();
        $count = 0;

        User::factory()->count(10)->create([
            'price_list_id' => function () use (&$count, $priceLists) {
                $id = $count % 2 === 0 ? $priceLists[1]->id : $priceLists[0]->id;
                $count++;

                return $id;
            }
        ]);
    }
}
