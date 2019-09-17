CREATE TABLE IF NOT EXISTS users (
    id int UNSIGNED NOT NULL AUTO_INCREMENT,
    email varchar(50) NOT NULL DEFAULT '500.Internal@Server.Error',
    first_name varchar(99) NOT NULL DEFAULT 'Server',
    last_name varchar(99) NOT NULL DEFAULT 'Error',
    verified tinyint(1) UNSIGNED DEFAULT '0',
    active tinyint(1) UNSIGNED DEFAULT '0',
    gdpr tinyint(1) UNSIGNED DEFAULT '0',
    news tinyint(1) UNSIGNED DEFAULT '1',
    password varchar(60) NOT NULL DEFAULT '$2y$10$nxJTYhv9W4PxHVR5eKCGr.X9p3cZqtJQvFMW4Z32zI9X0fQBQtiae',
    api_token varchar(60) DEFAULT NULL,
    remember_token varchar(60) DEFAULT NULL,
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY unique_email(email),
    UNIQUE KEY unique_api_token(api_token)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

INSERT INTO users (email, first_name, last_name, api_token) VALUES
('vativa4c@gmail.com', 'Tichomir', 'Rangelov', 'ST617AtzyuMct9GTlYhQBGldfEvvzK3aNg0f5Tvxk58J7ODxD01TX9EKRpLw');
