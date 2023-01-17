<?php

declare(strict_types=1);

use exceptions\HTTPError;
use repositories\RefreshTokenRepository;
use repositories\UserRepository;
use services\auth\Auth;

spl_autoload_register(function ($class) {
    $path = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    require_once($path);
});

try {
    $authService = new Auth(
        new RefreshTokenRepository(DBConnector::getConnection()),
        new UserRepository(DBConnector::getConnection()),
    );

    $router = new Router(
        $authService,
    );
    $controller = new Controller(
        new UserRepository(DBConnector::getConnection()),
        $authService,
    );

    $router
        ->register(
            '/me',
            'GET',
            function () use ($controller)
            {
                $controller->me();
            },
        )
        ->register(
            '/signup',
            'POST',
            function () use ($controller)
            {
                $controller->signup();
            },
            false,
        )
        ->register(
            '/login',
            'POST',
            function () use ($controller)
            {
                $controller->login();
            },
            false,
        )
        ->register(
            '/logout',
            'POST',
            function () use ($controller)
            {
                $controller->logout();
            },
        )
        ->register(
            '/refresh',
            'POST',
            function () use ($controller)
            {
                $controller->refreshToken();
            },
            false,
        )
        ->run();
} catch (Exception $error) {
    // Log the Error

    if ($error instanceof HTTPError) {
        http_response_code($error->getCode());
        exit($error->getMessage());
    }

    http_response_code(500);
    exit('Internal Server Error.');
}
