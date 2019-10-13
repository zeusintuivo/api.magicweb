SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS users;
CREATE TABLE IF NOT EXISTS users (
    id int UNSIGNED NOT NULL AUTO_INCREMENT,
    client enum ('mweb', 'cab7', 'izgrev') NOT NULL DEFAULT 'mweb',
    email varchar(50) NOT NULL DEFAULT '500.Internal@Server.Error',
    last_email_at timestamp NULL DEFAULT NULL,
    first_name varchar(99) NOT NULL DEFAULT 'Server',
    last_name varchar(99) NOT NULL DEFAULT 'Error',
    verified tinyint(1) UNSIGNED DEFAULT '0',
    active tinyint(1) UNSIGNED DEFAULT '0',
    gdpr tinyint(1) UNSIGNED DEFAULT '0',
    news tinyint(1) UNSIGNED DEFAULT '1',
    api_token varchar(80) NULL DEFAULT NULL,
    password varchar(80) NOT NULL,
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY unique_email(email),
    UNIQUE KEY unique_api_token(api_token)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

DROP TABLE IF EXISTS email_authentications;
CREATE TABLE IF NOT EXISTS email_authentications (
    id int unsigned NOT NULL AUTO_INCREMENT,
    token varchar(60) NOT NULL UNIQUE,
    user_id int UNSIGNED NOT NULL UNIQUE,
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;


