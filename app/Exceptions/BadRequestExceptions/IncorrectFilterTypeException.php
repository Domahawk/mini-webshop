<?php

namespace App\Exceptions\BadRequestExceptions;

use App\Exceptions\BaseException;

class IncorrectFilterTypeException extends BaseException
{
    public function __construct(string $filterName, string $filterType)
    {
        parent::__construct(
            "Filter '$filterName' must be a $filterType",
            400
        );
    }
}
