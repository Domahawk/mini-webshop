<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PriceModifier extends Model
{
    use HasUuids, HasFactory;

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(
            Order::class,
            'order_price_modifier',
            'price_modifier_id',
            'order_id'
        )->using(OrderPriceModifier::class);
    }
}
