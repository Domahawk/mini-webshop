<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BaseException extends Exception
{
    public function __construct(
        string $message = "",
        int $code = 0,
        private readonly array $errors = [],
    ) {
        parent::__construct($message, $code);
    }

    public function render(Request $request): Response
    {
        return response(
            [
                'message' => $this->message,
                'errors' => $this->errors
            ],
            $this->getCode());
    }
}
