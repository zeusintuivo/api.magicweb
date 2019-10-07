SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS users;
CREATE TABLE IF NOT EXISTS users (
    id int UNSIGNED NOT NULL AUTO_INCREMENT,
    client enum('mweb', 'cab7', 'izgrev') NOT NULL DEFAULT 'mweb',
    email varchar(50) NOT NULL DEFAULT '500.Internal@Server.Error',
    last_email_at TIMESTAMP NULL DEFAULT NULL,
    first_name varchar(99) NOT NULL DEFAULT 'Server',
    last_name varchar(99) NOT NULL DEFAULT 'Error',
    verified tinyint(1) UNSIGNED DEFAULT '0',
    active tinyint(1) UNSIGNED DEFAULT '0',
    gdpr tinyint(1) UNSIGNED DEFAULT '0',
    news tinyint(1) UNSIGNED DEFAULT '1',
    api_token varchar(80) NULL DEFAULT NULL,
    remember tinyint unsigned NULL DEFAULT NULL,
    password varchar(80) NOT NULL DEFAULT '$2y$10$nxJTYhv9W4PxHVR5eKCGr.X9p3cZqtJQvFMW4Z32zI9X0fQBQtiae',
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY unique_email(email),
    UNIQUE KEY unique_api_token(api_token)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

# INSERT INTO users (email, first_name, last_name, api_token) VALUES
# ('vativa4c@gmail.com', 'Tichomir', 'Rangelov', 'ST617AtzyuMct9GTlYhQBGldfEvvzK3aNg0f5Tvxk58J7ODxD01TX9EKRpLw');
SET FOREIGN_KEY_CHECKS = 1;

DROP TABLE IF EXISTS tokens;
CREATE TABLE IF NOT EXISTS tokens (
    hash varchar(255) NOT NULL UNIQUE,
    user_id int unsigned NOT NULL UNIQUE,
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (hash),
    FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE
);

# Dump all referencing child tables on account hard delete
USE information_schema;
SELECT table_name FROM key_column_usage
    WHERE table_schema = 'cab7' AND referenced_table_name = 'users' AND referenced_column_name = 'id';



