<?php

namespace App\Enums\Product;

use App\Interfaces\Enum\CanBeUndefined;
use App\Interfaces\Enum\FilterSort;
use App\Traits\UndefinedEnum;
use Illuminate\Database\Eloquent\Builder;

enum Filter: string implements CanBeUndefined, FilterSort
{
    use UndefinedEnum;

    case NAME = 'name';

    case PRICE = 'price';

    case CATEGORY = 'category';

    case UNDEFINED = 'undefined';

    public function isCorrectType(mixed $value): bool
    {
        return match ($this->getFilterType()) {
            'string' => is_string($value),
            'number' => is_numeric($value),
            default => false,
        };
    }

    public function getFilterType(): string
    {
        return match ($this) {
            self::NAME, self::CATEGORY => 'string',
            self::PRICE => 'number',
            default => '',
        };
    }
}
