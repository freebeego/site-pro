<?php

declare(strict_types=1);

namespace repositories;

use entities\RefreshToken;
use Error;

class RefreshTokenRepository extends Repository implements RefreshTokenInterface {

    public function create(RefreshToken $entity): RefreshToken
    {
        $id = $entity->getId();
        $refreshToken = $entity->getRefreshToken();
        $expires = $entity->getExpires();

        $query = "INSERT INTO jwt_refresh_tokens (id, refresh_token, expires) VALUES(?, ?, ?)";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new Error($stmt->error);
        }

        $result = $stmt->bind_param("isi", $id, $refreshToken, $expires);
        if (!$result) {
            throw new Error($stmt->error);
        }

        $result = $stmt->execute();
        if (!$result) {
            throw new Error($stmt->error);
        }

        return $entity;
    }

    public function getById(int $id): ?RefreshToken
    {
        $query = "SELECT * FROM jwt_refresh_tokens WHERE id = ?";

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

        return new RefreshToken($result['id'], $result['refresh_token'], $result['expires']);
    }

    public function getByToken(string $token): ?RefreshToken
    {
        $query = "SELECT * FROM jwt_refresh_tokens WHERE refresh_token = ?";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new Error($stmt->error);
        }

        $result = $stmt->bind_param("s", $token);
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

        return new RefreshToken($result['id'], $result['refresh_token'], $result['expires']);
    }

    public function delete(int $id)
    {
        $query = "DELETE FROM jwt_refresh_tokens WHERE id = ?";

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
    }
}
