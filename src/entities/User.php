<?php

declare(strict_types=1);

namespace entities;

class User extends Entity {
    public function __construct(private string $email, private string $password, private string $username)
    {}

    /**
     * @param string $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
}
