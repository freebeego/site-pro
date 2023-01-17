CREATE TABLE jwt_refresh_tokens (
    id INT unsigned not null,
    refresh_token VARCHAR(255) unique,
    expires INT(11) unsigned not null,
    PRIMARY KEY (id),
    CONSTRAINT fk_user
        FOREIGN KEY (id)
        REFERENCES users (id)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
) ENGINE = innodb DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
