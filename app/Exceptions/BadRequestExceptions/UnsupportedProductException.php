<?php

namespace App\Exceptions\BadRequestExceptions;

use App\Exceptions\BaseException;

class UnsupportedProductException extends BaseException
{
    public function __construct(string $productIds)
    {
        parent::__construct(
            "Cannot place an order for products: $productIds",
            400
        );
    }
}
