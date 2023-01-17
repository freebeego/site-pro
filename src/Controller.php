<?php

declare(strict_types=1);

use entities\User;
use exceptions\BadRequest;
use exceptions\Conflict;
use exceptions\Unauthorized;
use repositories\UserRepository;
use services\auth\Auth;

class Controller {
    private ?array $postData;

    public function __construct(
        private UserRepository $userRepository,
        private Auth $authService,
    )
    {
        $data = file_get_contents('php://input');
        $this->postData = $data ? json_decode($data, true) : null;
    }

    public function me()
    {
        $userId = $this->authService->extractUserId();
        $user = $this->userRepository->getById($userId);

        if ($user) {
            echo json_encode([
                "email" => $user->getEmail(),
                "username" => $user->getUsername(),
            ]);
        } else {
            throw new Error();
        }
    }

    /**
     * @throws BadRequest
     */
    public function login()
    {
        ["email" => $email, "password" => $password] = $this->postData;
        if (Validator::email($email) && Validator::password($password)) {
            $this->authService->login($email, $password);
        } else {
            throw new BadRequest('Incorrect login and password');
        }
    }

    /**
     * @throws BadRequest
     * @throws Conflict
     */
    public function signup()
    {
        ["email" => $email, "password" => $password, "username" => $username] = $this->postData;
        if (
            Validator::email($email) &&
            Validator::password($password) &&
            Validator::name($username)
        ) {
            $users = $this->userRepository->getUsersByEmailOrUsername($email, $username);

            if (count($users)) {
                throw new Conflict("User already exist");
            }

            $user = $this->userRepository->create(
                new User(
                    $email,
                    password_hash($password, PASSWORD_DEFAULT),
                    $username),
            );
            $this->authService->setTokens($user->getId());
        } else {
            throw new BadRequest('Validation Error');
        }
    }

    public function logout()
    {
        $this->authService->unsetTokens();
    }

    /**
     * @throws Unauthorized
     */
    public function refreshToken()
    {
        $this->authService->refreshTokens();
    }
}
