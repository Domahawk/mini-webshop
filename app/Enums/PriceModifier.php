<?php

namespace App\Enums;

use App\Enums\Product\Filter;
use App\Enums\Product\Sort;
use App\Interfaces\Enum\CanBeUndefined;
use App\Traits\UndefinedEnum;
use Illuminate\Support\Facades\Auth;

enum PriceModifier: string implements CanBeUndefined
{
    use UndefinedEnum;

    case LARGE_ORDER_DISCOUNT = 'largeOrderDiscount';

    case SEASONAL_DISCOUNT = 'seasonalDiscount';

    case VAT = 'vat';

    case UNDEFINED = 'undefined';

    public function getModifierType(): PriceModifierType
    {
        return match ($this) {
            self::VAT, self::LARGE_ORDER_DISCOUNT=> PriceModifierType::PERCENT,
            self::SEASONAL_DISCOUNT => PriceModifierType::FLAT_RATE,
            default => PriceModifierType::UNDEFINED,
        };
    }

    public function getModifierAmount(): int
    {
        return match ($this) {
            self::VAT => 25,
            self::LARGE_ORDER_DISCOUNT => 10,
            self::SEASONAL_DISCOUNT => 5,
            default => PriceModifier::UNDEFINED,
        };
    }

    public function isVat(): bool
    {
        return $this === self::VAT;
    }

    public function isLargeOrderDiscount(): bool
    {
        return $this === self::LARGE_ORDER_DISCOUNT;
    }


    public function isApplicableByUser(): bool
    {
        return $this !== self::VAT || $this !== self::LARGE_ORDER_DISCOUNT;
    }
}
