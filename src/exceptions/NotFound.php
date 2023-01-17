<?php

declare(strict_types=1);

namespace exceptions;

class NotFound extends HTTPError
{
    public function __construct()
    {
        parent::__construct('Page not found.', 404);
    }
}
