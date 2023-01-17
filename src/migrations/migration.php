<?php

declare(strict_types=1);

const DB_TABLE_VERSIONS = "versions";

require_once(__DIR__ . '/../DBConnector.php');

try {
    $connection = DBConnector::getConnection();
} catch (Exception $error) {
    exit($error);
}

function getMigrationFiles(mysqli $connection)
{
    $files = scandir(__DIR__);
    $migrationsFiles = array_filter(
        $files,
        fn($file) => preg_match('/migrate_[0-9]+_.*.sql/', $file),
    );

    $query = sprintf('select name from %s', DB_TABLE_VERSIONS);
    $previousMigrations = $connection->query($query)->fetch_all(MYSQLI_ASSOC);
    $previousMigrations = array_map(fn($file) => $file['name'], $previousMigrations);

    return array_diff($migrationsFiles, $previousMigrations);
}

function migrate(mysqli $connection, string $file)
{
    $query = file_get_contents(__DIR__ . "/" . $file);
    $query = str_replace(PHP_EOL, '', $query);
    $connection->query($query);

    $query = sprintf('INSERT INTO %s (name) VALUES ("%s");', DB_TABLE_VERSIONS, $file);
    $connection->query($query);
}

$connection->query(
    'CREATE TABLE IF NOT EXISTS ' . DB_TABLE_VERSIONS .
    ' (id INT unsigned not null auto_increment,
    name VARCHAR(255) not null UNIQUE,
    created TIMESTAMP default current_timestamp,
    PRIMARY KEY (id)
    ) ENGINE = innodb DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;',
);

$requiredMigrations = getMigrationFiles($connection);

if (empty($requiredMigrations)) {
    echo 'The database is up to date.' . PHP_EOL;
} else {
    foreach ($requiredMigrations as $file) {
        migrate($connection, $file);
        echo $file . PHP_EOL;
    }
}
