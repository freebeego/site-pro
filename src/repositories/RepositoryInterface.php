<?php

declare(strict_types=1);

namespace repositories;

use entities\Entity;

interface RepositoryInterface {
    public function getById(int $id);
    public function delete(int $id);
}
