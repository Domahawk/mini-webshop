<?php

namespace App\Models;

use App\Builders\ProductBuilder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasUuids, HasFactory;

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'category_product',
            'product_id',
            'category_id',
        )->withTimestamps();
    }

    public function priceLists(): BelongsToMany
    {
        return $this->belongsToMany(
            PriceList::class,
            'price_list_product',
            'sku',
            'price_list_id',
        )->using(PriceListProduct::class)
            ->withPivot(['price'])
            ->withTimestamps();
    }

    public function contractLists(): HasMany
    {
        return $this->hasMany(ContractList::class, 'SKU', 'SKU');
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(
            Order::class,
            'order_product',
            'product_id',
            'order_id'
        )->using(OrderProduct::class)
            ->withPivot(['amount', 'price'])
            ->withTimestamps();
    }

    public function newEloquentBuilder($query): ProductBuilder
    {
        return new ProductBuilder($query);
    }
}
