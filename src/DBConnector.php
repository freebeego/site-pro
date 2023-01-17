<?php

declare(strict_types=1);

class DBConnector
{
    private static ?mysqli $connection = null;

    /**
     * @throws Exception
     */
    public static function getConnection(): mysqli
    {
        if (self::$connection) {
            return self::$connection;
        }

        ['DB_HOST' => $host, 'DB_USER' => $user, 'DB_PASSWORD' => $password, 'DB_DATABASE' => $name] = $_SERVER;
        $connection = new mysqli($host, $user, $password, $name);

        if ($connection->ping()) {
            self::$connection = $connection;

            return self::$connection;
        }

        throw new Exception(
            "Database server is unavailable! $connection->connect_error",
            $connection->connect_errno,
        );
    }
}
