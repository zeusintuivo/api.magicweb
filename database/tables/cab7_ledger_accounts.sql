# DROP TABLE IF EXISTS tmp_ledger_accounts;
# CREATE TEMPORARY TABLE tmp_ledger_accounts SELECT * FROM cab7_ledger_accounts;
# SELECT * FROM tmp_ledger_accounts;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS cab7_ledger_accounts;-- be careful!
SET FOREIGN_KEY_CHECKS = 1;
CREATE TABLE IF NOT EXISTS cab7_ledger_accounts(
    id int unsigned NOT NULL AUTO_INCREMENT,
    journal_id int unsigned NOT NULL COMMENT 'Associated general ledger entry',
    skr04 int unsigned NOT NULL COMMENT 'Associated SKR04 account',
    skr04_ref int unsigned NOT NULL COMMENT 'Associated SKR04 offset account',
    date date NOT NULL COMMENT 'Day booking belongs to',
    debit decimal(7, 2) NOT NULL DEFAULT 0.00 COMMENT 'Debere',
    credit decimal(7, 2) NOT NULL DEFAULT 0.00 COMMENT 'Credere',
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (journal_id) REFERENCES cab7_ledger_journal(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (skr04) REFERENCES cab7_skr04_accounts(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (skr04_ref) REFERENCES cab7_skr04_accounts(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4;

# INSERT INTO cab7_ledger_accounts (skr04, skr04_ref, journal_id, date, debit, credit)
#     SELECT * FROM tmp_ledger_accounts;

# Trial balance
# SELECT date, skr04, skr04_ref, debit, credit, @b := ROUND(@b + s.debit - s.credit, 2) AS balance
# FROM (SELECT @b := 0.00) AS excel, cab7_ledger_accounts AS s
# WHERE skr04 IN (4400, 3806)
# ORDER BY id;
