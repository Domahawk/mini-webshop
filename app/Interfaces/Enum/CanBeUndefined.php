<?php

namespace App\Interfaces\Enum;

interface CanBeUndefined
{
    public function isUndefined(): bool;

    public static function from(string|int $value): static;

    public static function create(string $value): self;
}
