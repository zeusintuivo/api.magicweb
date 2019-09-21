DROP TABLE IF EXISTS account_charts;
CREATE TABLE IF NOT EXISTS account_charts (
    id int UNSIGNED NOT NULL AUTO_INCREMENT,
    skr03 varchar(5) NULL UNIQUE COMMENT 'Standardkontenrahmen 03',
    skr04 varchar(5) NOT NULL UNIQUE COMMENT 'Standardkontenrahmen 04',
    en_GB varchar(21) NULL UNIQUE COMMENT 'Name of T-Account',
    de_DE varchar(21) NULL UNIQUE COMMENT 'Name of T-Account',
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE InnoDB DEFAULT CHARSET utf8mb4;

INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('0420', 'Technical equipment', 'Technische Anlagen');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('1600', 'Cash', 'Kasse');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('1800', 'Bank', 'Bank');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('2000', 'Fixed capital', 'Festkapital');
