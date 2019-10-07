DROP TABLE IF EXISTS tmp_ledger_accounts;
CREATE TEMPORARY TABLE tmp_ledger_accounts
    SELECT user_id, account_chart_id, ledger_journal_id, skr, lang, date, refer, debit, credit
    FROM cab7_ledger_accounts;
SELECT * FROM tmp_ledger_accounts;

DROP TABLE IF EXISTS cab7_ledger_accounts;-- Be careful!
CREATE TABLE IF NOT EXISTS cab7_ledger_accounts(
    id int unsigned NOT NULL AUTO_INCREMENT,
    user_id int unsigned NOT NULL COMMENT 'Associated user',
    account_chart_id int unsigned NOT NULL COMMENT 'Associated account',
    ledger_journal_id int unsigned NOT NULL COMMENT 'General ledger entry',
    skr varchar(5) NOT NULL COMMENT 'Code SKR',
    lang varchar(5) NOT NULL COMMENT 'Code lang',
    date date NOT NULL COMMENT 'Day booking belongs to',
    refer varchar(255) NOT NULL COMMENT 'Booking t-account reference',
    debit decimal(7, 2) NOT NULL DEFAULT 0.00 COMMENT 'Debere',
    credit decimal(7, 2) NOT NULL DEFAULT 0.00 COMMENT 'Credere',
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (account_chart_id) REFERENCES cab7_account_charts(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (ledger_journal_id) REFERENCES cab7_ledger_journal(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4;
INSERT INTO cab7_ledger_accounts (user_id, account_chart_id, ledger_journal_id, skr, lang, date, refer, debit, credit)
    SELECT * FROM tmp_ledger_accounts;

# Trial balance
SELECT date, refer, debit, credit, @b := @b + s.debit - s.credit AS balance
FROM (SELECT @b := 0.00) AS excel
     CROSS JOIN cab7_ledger_accounts AS s
ORDER BY created_at;
