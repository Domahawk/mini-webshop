<?php

namespace App\Enums;

use App\Interfaces\Enum\CanBeUndefined;
use App\Traits\UndefinedEnum;

enum PriceModifierType: string implements CanBeUndefined
{
    use UndefinedEnum;

    case PERCENT = 'percent';

    case FLAT_RATE = 'flatRate';

    case UNDEFINED = 'undefined';

    public function isPercent(): bool
    {
        return $this === self::PERCENT;
    }
}
