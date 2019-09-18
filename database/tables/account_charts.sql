DROP TABLE IF EXISTS account_charts;
CREATE TABLE IF NOT EXISTS account_charts (
    id int UNSIGNED NOT NULL AUTO_INCREMENT,
    skr03 smallint UNSIGNED NULL UNIQUE COMMENT 'Standardkontenrahmen 03',
    skr04 smallint UNSIGNED NOT NULL UNIQUE COMMENT 'Standardkontenrahmen 04',
    name varchar(21) NOT NULL UNIQUE UNIQUE COMMENT 'Name of T-Account',
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE InnoDB DEFAULT CHARSET utf8mb4;

INSERT INTO account_charts (skr04, name) VALUES (200, 'Fixed capital');
INSERT INTO account_charts (skr04, name) VALUES (420, 'Technical equipment');
INSERT INTO account_charts (skr04, name) VALUES (1600, 'Cash');
INSERT INTO account_charts (skr04, name) VALUES (1800, 'Bank');
