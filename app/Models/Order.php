<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasUuids;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'order_products',
            'order_id',
            'product_id'
        )
            ->using(OrderProduct::class)
            ->withTimestamps();
    }

    public function priceModifiers(): BelongsToMany
    {
        return $this->belongsToMany(
            PriceModifier::class,
            'order_price_modifier',
            'order_id',
            'price_modifier_id'
        )
            ->using(OrderPriceModifier::class);
    }
}
