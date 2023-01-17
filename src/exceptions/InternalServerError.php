<?php

declare(strict_types=1);

namespace exceptions;

class InternalServerError extends HTTPError
{
    public function __construct()
    {
        parent::__construct('Internal Server Error.', 500);
    }
}
