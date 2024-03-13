<?php

namespace App\Enums\Product;

use App\Interfaces\Enum\CanBeUndefined;
use App\Interfaces\Enum\FilterSort;
use App\Traits\UndefinedEnum;

enum Sort: string implements CanBeUndefined, FilterSort
{
    use UndefinedEnum;

    case PRICE = 'price';

    case NAME = 'name';

    case UNDEFINED = 'undefined';

    public function isCorrectType(mixed $value): bool
    {
        return is_string($value) && in_array($value, ['asc', 'desc']);
    }
}
