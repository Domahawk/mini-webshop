<?php

namespace App\Enums;

use App\Enums\Product\Filter;
use App\Enums\Product\Sort;
use App\Interfaces\Enum\CanBeUndefined;
use App\Traits\UndefinedEnum;

enum FilterableSortableRoute: string implements CanBeUndefined
{
    use UndefinedEnum;

    case PRODUCTS = 'api/products/filter';

    case UNDEFINED = 'undefined';

    public function filterSortConfiguration(): array
    {
        return match ($this) {
            self::PRODUCTS => [
                'filter' => Filter::UNDEFINED,
                'sort' => Sort::UNDEFINED,
            ],
            default => [
                'filter' => null,
                'sort' => null,
            ],
        };
    }
}
