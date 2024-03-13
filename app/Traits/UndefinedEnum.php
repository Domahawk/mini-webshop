<?php

namespace App\Traits;

use ValueError;

trait UndefinedEnum
{
    public function isUndefined(): bool
    {
        return $this === self::UNDEFINED;
    }

    public static function create(string $value): self
    {
        try {
            return self::from($value);
        } catch (ValueError) {
            return self::UNDEFINED;
        }
    }
}
