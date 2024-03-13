<?php

namespace App\Interfaces\Enum;

interface FilterSort
{
    public function isCorrectType(mixed $value): bool;
}
