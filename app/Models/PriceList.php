<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PriceList extends Model
{
    use HasUuids, HasFactory;

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'price_list_product',
            'price_list_id',
            'sku',
        )->using(PriceListProduct::class)
            ->withPivot(['price']);
    }
}
