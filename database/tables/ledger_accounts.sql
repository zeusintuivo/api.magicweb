CREATE TABLE IF NOT EXISTS ledger_accounts(
    id int unsigned NOT NULL AUTO_INCREMENT,
    user_id int unsigned NOT NULL COMMENT 'Associated user',
    name varchar(21) NOT NULL UNIQUE COMMENT 'Name of T-Account',
    date date NOT NULL COMMENT 'Day booking belongs to',
    details varchar(255) NOT NULL COMMENT 'Booking t-account reference',
    debit decimal(7, 2) NULL DEFAULT NULL COMMENT 'Debere',
    credit decimal(7, 2) NULL DEFAULT NULL COMMENT 'Credere',
    balance decimal(7, 2) NOT NULL COMMENT 'Balance inkl. current entry',
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4;
