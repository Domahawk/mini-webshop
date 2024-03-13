<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderProduct extends Pivot
{
    protected $primaryKey = ['order_id', 'product_id'];

    protected $fillable = ['amount', 'price', 'product_id', 'order_id'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id')->select([
            'id',
            'name',
            'description',
            'sku',
            'published',
        ]);
    }
}
