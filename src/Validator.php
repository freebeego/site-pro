<?php

declare(strict_types=1);

class Validator {
    static function name(?string $name): bool
    {
        if ($name) {
            return (bool) preg_match("/^[a-zA-Z]{4,20}$/", $name);
        }

        return false;
    }

    static function email(?string $email): bool
    {
        if ($email) {
            return (bool) preg_match(
                "/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/",
                $email,
            );
        }

        return false;
    }

    static function password(?string $password): bool
    {
        if ($password) {
            return (bool) preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/", $password);
        }

        return false;
    }
}
