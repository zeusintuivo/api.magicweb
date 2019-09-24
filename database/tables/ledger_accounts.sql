DROP TABLE IF EXISTS ledger_accounts;-- Be careful!
CREATE TABLE IF NOT EXISTS ledger_accounts(
    id int unsigned NOT NULL AUTO_INCREMENT,
    user_id int unsigned NOT NULL COMMENT 'Associated user',
    account_chart_id int unsigned NOT NULL COMMENT 'Associated account',
    ledger_journal_id int unsigned NOT NULL COMMENT 'General ledger entry',
    skr varchar(5) NOT NULL COMMENT 'Code SKR',
    lang varchar(5) NOT NULL COMMENT 'Code lang',
    date date NOT NULL COMMENT 'Day booking belongs to',
    refer varchar(255) NOT NULL COMMENT 'Booking t-account reference',
    debit decimal(7, 2) NULL DEFAULT NULL COMMENT 'Debere',
    credit decimal(7, 2) NULL DEFAULT NULL COMMENT 'Credere',
    balance decimal(7, 2) NULL COMMENT 'Balance inkl. current entry',
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (account_chart_id) REFERENCES account_charts(id),
    FOREIGN KEY (ledger_journal_id) REFERENCES ledger_journal(id)
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4;
