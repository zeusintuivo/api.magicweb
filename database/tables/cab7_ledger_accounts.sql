DROP TABLE IF EXISTS tmp_ledger_accounts;
CREATE TEMPORARY TABLE tmp_ledger_accounts SELECT * FROM cab7_ledger_accounts;
# SELECT * FROM tmp_ledger_accounts;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS cab7_ledger_accounts;-- be careful!
SET FOREIGN_KEY_CHECKS = 1;
CREATE TABLE IF NOT EXISTS cab7_ledger_accounts(
    id int unsigned NOT NULL AUTO_INCREMENT,
    journal_id int unsigned NOT NULL COMMENT 'Associated general ledger entry',
    skr04_id int unsigned NOT NULL COMMENT 'Associated SKR04 account',
    skr04_ref_id int unsigned NOT NULL COMMENT 'Associated SKR04 offset account',
    debit decimal(7, 2) NOT NULL DEFAULT 0.00 COMMENT 'Debere',
    credit decimal(7, 2) NOT NULL DEFAULT 0.00 COMMENT 'Credere',
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (journal_id) REFERENCES cab7_ledger_journal(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (skr04_id) REFERENCES cab7_skr04_accounts(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (skr04_ref_id) REFERENCES cab7_skr04_accounts(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4;

# INSERT INTO cab7_ledger_accounts SELECT * FROM tmp_ledger_accounts;
# ALTER TABLE cab7_ledger_accounts DROP COLUMN date;

# Trial balance
# SELECT @b := ROUND(@b + s.debit - s.credit, 2) AS balance
# FROM (SELECT @b := 0.00) AS excel, cab7_ledger_accounts AS s
# ORDER BY id;

# Ledger accounts' balance
# CREATE OR REPLACE VIEW cab7_ledger_accounts_balance AS
SELECT skr04.id, round(sum(debit) - sum(credit), 2) balance, skr04.de_DE, skr04.en_GB, skr04.pid, skr04.side, skr04.vat_code
FROM cab7_ledger_journal journal, cab7_ledger_accounts account, cab7_skr04_accounts skr04
WHERE journal.id = account.journal_id AND account.skr04_id = skr04.id AND journal.deleted_at IS NULL
GROUP BY skr04.id
ORDER BY skr04.id;









