CREATE TABLE users (
    id INT unsigned not null auto_increment,
    email VARCHAR(255) unique,
    username VARCHAR(255) unique,
    password VARCHAR(255) unique,
    PRIMARY KEY (id)
) ENGINE = innodb DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
