<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderPriceModifier extends Pivot
{
    use HasUuids;

    protected $primaryKey = 'id';
}
