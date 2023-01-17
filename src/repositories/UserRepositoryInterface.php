<?php

declare(strict_types=1);

namespace repositories;

use entities\User;

interface UserRepositoryInterface extends RepositoryInterface {
    public function create(User $entity);
}
