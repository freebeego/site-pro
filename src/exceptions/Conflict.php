<?php

declare(strict_types=1);

namespace exceptions;

class Conflict extends HTTPError
{
    public function __construct(string $message)
    {
        parent::__construct($message, 409);
    }
}
