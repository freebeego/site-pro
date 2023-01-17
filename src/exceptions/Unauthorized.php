<?php

declare(strict_types=1);

namespace exceptions;

class Unauthorized extends HTTPError
{
    public function __construct()
    {
        parent::__construct('Authorization required', 401);
    }
}
