<?php

namespace App\Exceptions\BadRequestExceptions;

use App\Exceptions\BaseException;

class IncorrectSortException extends BaseException
{
    public function __construct(string $sortValues)
    {
        parent::__construct(
            "Allowed sort values are: $sortValues",
            400
        );
    }
}
