<?php

declare(strict_types=1);

namespace repositories;

use mysqli;

abstract class Repository {
    public function __construct(protected mysqli $db)
    {}
}
