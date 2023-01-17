<?php

declare(strict_types=1);

namespace services\auth;

interface AuthInterface
{
    public function checkAuth(): bool;
    public function setTokens(int $userId): void;
    public function unsetTokens(): void;
}
