<?php

declare(strict_types=1);

namespace services\auth;

use entities\RefreshToken;
use exceptions\BadRequest;
use exceptions\Unauthorized;
use repositories\RefreshTokenRepository;
use repositories\UserRepository;

class Auth implements AuthInterface
{
    const ACCESS_TOKEN_TTL = 60 * 5; // 5 minutes
    const REFRESH_TOKEN_TTL = 60 * 20; // 20 minutes
    const CIPHER_METHOD = "AES-192-CBC";

    private string $secret;
    private string $domain;

    public function __construct(
        private RefreshTokenRepository $refreshTokenRepository,
        private UserRepository $userRepository,
    )
    {
        $this->secret = $_SERVER['SECRET'];
        $this->domain = $_SERVER['HTTP_DOMAIN'];
    }

    /**
     * @throws BadRequest
     */
    public function login(string $email, string $password): void
    {
        $user = $this->userRepository->getByEmail($email);

        if (!$user || !password_verify($password, $user->getPassword())) {
            throw new BadRequest('Incorrect login and password');
        }

        $this->setTokens($user->getId());
    }

    /**
     * @throws Unauthorized
     */
    public function refreshTokens(): void
    {
        $refreshToken = $_COOKIE['refreshToken'] ?? null;

        if (!$refreshToken) {
            throw new Unauthorized();
        }

        $this->clearCookies();

        $refreshToken = $this->refreshTokenRepository->getByToken($refreshToken);

        if ($refreshToken) {
            $this->refreshTokenRepository->delete($refreshToken->getId());
        }

        if (!$refreshToken || $refreshToken->getExpires() < time()) {
            throw new Unauthorized();
        }

        $this->setTokens($refreshToken->getId());
    }

    public function checkAuth(): bool
    {
        $accessToken = $_COOKIE['accessToken'] ?? null;

        if ($accessToken) {
            [$body, $sign] = explode('.', $accessToken);
            $body = base64_decode($body);
            $sign = base64_decode($sign);
            @$sign = openssl_decrypt($sign, self::CIPHER_METHOD, $this->secret);

            return $body === $sign;
        }

        return false;
    }

    private function makeAccessToken(int $userId): string
    {
        $body = json_encode(['userId' => $userId, 'expires' => time() + self::ACCESS_TOKEN_TTL]);
        @$sign = openssl_encrypt($body, self::CIPHER_METHOD, $this->secret);

        return base64_encode($body) . '.' . base64_encode($sign);
    }

    private function makeRefreshToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    public function setTokens(int $userId): void
    {
        $accessToken = $this->makeAccessToken($userId);
        $refreshToken = $this->makeRefreshToken();

        $this->refreshTokenRepository->delete($userId);
        $this->refreshTokenRepository->create(new RefreshToken(
            $userId,
            $refreshToken,
            time() + self::REFRESH_TOKEN_TTL,
        ));

        $cookiesOptions = [
            'path' => '/',
            'domain' => $this->domain,
            'samesite' => 'strict',
            'secure' => $_SERVER['MODE'] === 'prod',
            'httponly' => true,
        ];

        setcookie(
            'accessToken',
            $accessToken,
            [...$cookiesOptions, 'expires' => time() + self::ACCESS_TOKEN_TTL],
        );
        setcookie(
            'refreshToken',
            $refreshToken,
            [...$cookiesOptions, 'expires' => time() + self::ACCESS_TOKEN_TTL],
        );
    }

    private function clearCookies(): void
    {
        $cookiesOptions = ['expires' => -1, 'path' => '/'];

        setcookie('accessToken', '', $cookiesOptions);
        setcookie('refreshToken', '', $cookiesOptions);
    }

    public function unsetTokens(): void
    {
        $userId = $this->extractUserId();
        $this->refreshTokenRepository->delete($userId);

        $this->clearCookies();
    }

    public function extractUserId(): int
    {
        $accessToken = $_COOKIE['accessToken'];
        [$body] = explode('.', $accessToken);
        $body = base64_decode($body);

        return json_decode($body, true)['userId'];
    }
}
