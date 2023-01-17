<?php

declare(strict_types=1);

use exceptions\NotFound;
use exceptions\Unauthorized;
use services\auth\Auth;

class Router
{
    private array $routeMap = [];

    public function __construct(private Auth $auth)
    {}

    public function register(string $path, string $httpMethod, callable $callback, bool $isPrivate = true): Router
    {
        $this->routeMap[] = [
            'path' => $path,
            'method' => $httpMethod,
            'controller' => $callback,
            'isPrivate' => $isPrivate,
        ];

        return $this;
    }

    /**
     * @throws NotFound
     * @throws Unauthorized
     */
    public function run(): void
    {
        if(($pos = strpos($_SERVER['REQUEST_URI'], '?')) !== false) {
            $uri = substr($_SERVER['REQUEST_URI'], 0, $pos);
        } else {
            $uri = $_SERVER['REQUEST_URI'];
        }

        $routeIndex = array_search(
            $uri,
            array_column($this->routeMap, 'path'),
            true,
        );

        if ($routeIndex !== false && ($route = $this->routeMap[$routeIndex])['method'] === $_SERVER['REQUEST_METHOD']) {
            if ($route['isPrivate'] && !$this->auth->checkAuth()) {
                throw new Unauthorized();
            }

            $route['controller']();
            http_response_code(200);
        } else {
            throw new NotFound();
        }
    }
}
