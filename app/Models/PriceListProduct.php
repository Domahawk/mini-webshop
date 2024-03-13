<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PriceListProduct extends Pivot
{
    use HasFactory;

    protected $primaryKey = ['sku', 'price_list_id'];
}
