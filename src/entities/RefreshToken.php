<?php

declare(strict_types=1);

namespace entities;

class RefreshToken extends Entity {
    public function __construct($id, private string $refreshToken, private int $expires)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * @return int
     */
    public function getExpires(): int
    {
        return $this->expires;
    }
}
