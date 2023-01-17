<?php

declare(strict_types=1);

namespace repositories;

use entities\RefreshToken;

interface RefreshTokenInterface extends RepositoryInterface {
    public function create(RefreshToken $entity);
}
