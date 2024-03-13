<?php

namespace App\Exceptions\BadRequestExceptions;

use App\Exceptions\BaseException;

class FailToCreateOrderException extends BaseException
{
    public function __construct(string $message)
    {
        parent::__construct(
            "Fail to create order",
            400,
            [$message]
        );
    }
}
