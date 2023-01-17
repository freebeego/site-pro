<?php

declare(strict_types=1);

namespace entities;

abstract class Entity {
    protected int | string $id;

    public function getId(): int | string
    {
        return $this->id;
    }
}
