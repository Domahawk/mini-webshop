<?php

namespace Database\Seeders;

use App\Models\PriceModifier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PriceModifierSeeder extends Seeder
{
    private const PRICE_MODIFIERS = [
        'Large amount discount' => [
            'amount' => 10,
            'type' => 'percent'
        ],
        'Coupon discount' => [
            'amount' => 5,
            'type' => 'flat'
        ],
        'VAT' => [
            'amount' => 25,
            'type' => 'percent'
        ],
    ];

    public function run(): void
    {
        foreach (self::PRICE_MODIFIERS as $name => $data) {
            PriceModifier::create([
                'name' => $name,
                'amount' => $data['amount'],
                'type' => $data['type'],
            ]);
        }
    }
}
