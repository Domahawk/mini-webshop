<?php

namespace App\Exceptions\BadRequestExceptions;

use App\Exceptions\BaseException;

class UnsupportedPriceModifier extends BaseException
{
    public function __construct(string $priceModifierName)
    {
        parent::__construct(
            "Price modifier $priceModifierName is not supported",
            400
        );
    }
}
