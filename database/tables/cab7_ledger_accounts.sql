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
    debit decimal(7, 2) NOT NULL DEFAULT 0.00 COMMENT 'Debere',
    credit decimal(7, 2) NOT NULL DEFAULT 0.00 COMMENT 'Credere',
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (journal_id) REFERENCES cab7_ledger_journal(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (skr04_id) REFERENCES cab7_skr04_accounts(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4;

INSERT INTO cab7_ledger_accounts SELECT id, journal_id, skr04_id, debit, credit, created_at, updated_at, deleted_at FROM tmp_ledger_accounts;
# ALTER TABLE cab7_ledger_accounts DROP COLUMN date;

# Trial balance
# SELECT @b := ROUND(@b + s.debit - s.credit, 2) AS balance
# FROM (SELECT @b := 0.00) AS excel, cab7_ledger_accounts AS s
# ORDER BY id;

# SELECT journal_id, skr04_id FROM mweb.cab7_ledger_journal jrn, cab7_skr04_accounts skr, cab7_ledger_accounts acc
# WHERE jrn.id = acc.journal_id AND acc.skr04_id = skr.id AND jrn.deleted_at IS NULL;
# Net income method
SELECT j.id, j.internal_bill_number, j.date, IF(s.balance_side = 'dead', a.debit - a.credit, a.credit - a.debit) amount,
    j.vat_code, o.skr04_id offset_account, s.id direct_account, j.client_details, j.system_details, j.original_bill_number, j.created_at
    FROM cab7_ledger_journal j, cab7_ledger_accounts a, cab7_skr04_accounts s, (
        SELECT acc.journal_id, acc.skr04_id FROM mweb.cab7_ledger_journal jrn, cab7_skr04_accounts skr, cab7_ledger_accounts acc
        WHERE jrn.id = acc.journal_id AND acc.skr04_id = skr.id AND jrn.deleted_at IS NULL
    ) o
WHERE j.id = a.journal_id AND a.skr04_id = s.id AND j.deleted_at IS NULL AND s.id IN (1600, 1800)
    AND o.journal_id = j.id AND s.id <> o.skr04_id AND o.skr04_id NOT IN (1401, 1406, 3801, 3806)
    AND j.date LIKE '2018%'
ORDER BY j.date, j.id;

# Ledger accounts' balance
SELECT skr04.id, round(sum(debit) - sum(credit), 2) balance, skr04.de_DE, skr04.en_GB, skr04.pid, skr04.balance_side, skr04.vat_code
FROM cab7_ledger_journal journal, cab7_ledger_accounts account, cab7_skr04_accounts skr04
WHERE journal.id = account.journal_id AND account.skr04_id = skr04.id AND journal.deleted_at IS NULL
GROUP BY skr04.id
ORDER BY skr04.id;









