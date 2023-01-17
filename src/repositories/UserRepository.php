<?php

declare(strict_types=1);

namespace repositories;

use entities\User;
use Error;

class UserRepository extends Repository implements UserRepositoryInterface {

    public function create(User $entity): User
    {
        $email = $entity->getEmail();
        $password = $entity->getPassword();
        $username = $entity->getUsername();

        $query = "INSERT INTO users (email, password, username) VALUES(?, ?, ?)";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new Error($stmt->error);
        }

        $result = $stmt->bind_param("sss", $email, $password, $username);
        if (!$result) {
            throw new Error($stmt->error);
        }

        $result = $stmt->execute();
        if (!$result) {
            throw new Error($stmt->error);
        }

        $user = new User($email, $password, $username);
        $user->setId($stmt->insert_id);

        return $user;
    }

    public function getById(int $id): ?User
    {
        $query = "SELECT * FROM users WHERE id = ?";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new Error($stmt->error);
        }

        $result = $stmt->bind_param("i", $id);
        if (!$result) {
            throw new Error($stmt->error);
        }

        $result = $stmt->execute();
        if (!$result) {
            throw new Error($stmt->error);
        }

        $result = $stmt->get_result();
        if (!$result) {
            throw new Error($stmt->error);
        }

        $result = $result->fetch_assoc();

        if (!$result) {
            return null;
        }

        $user = new User($result['email'], $result['password'], $result['username']);
        $user->setId($result['id']);

        return $user;
    }

    public function getByEmail(string $email): ?User
    {
        $query = "SELECT * FROM users WHERE email = ?";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new Error($stmt->error);
        }

        $result = $stmt->bind_param("s", $email);
        if (!$result) {
            throw new Error($stmt->error);
        }

        $result = $stmt->execute();
        if (!$result) {
            throw new Error($stmt->error);
        }

        $result = $stmt->get_result();
        if (!$result) {
            throw new Error($stmt->error);
        }

        $result = $result->fetch_assoc();

        if (!$result) {
            return null;
        }

        $user = new User($result['email'], $result['password'], $result['username']);
        $user->setId($result['id']);

        return $user;
    }

    public function delete(int $id)
    {
        // TODO: Implement delete() method.
    }

    public function getUsersByEmailOrUsername(string $email, string $username): array
    {
        $query = "SELECT * FROM users WHERE email = ? OR username = ?";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new Error($stmt->error);
        }

        $result = $stmt->bind_param("ss", $email, $username);
        if (!$result) {
            throw new Error($stmt->error);
        }

        $result = $stmt->execute();
        if (!$result) {
            throw new Error($stmt->error);
        }

        $result = $stmt->get_result();
        if (!$result) {
            throw new Error($stmt->error);
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
