CREATE TEMPORARY TABLE IF NOT EXISTS back_ledger_journal ENGINE InnoDB CHARSET utf8mb4 SELECT * FROM cab7_ledger_journal;
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS cab7_ledger_journal;-- Be careful with this!
SET FOREIGN_KEY_CHECKS = 1;
CREATE TABLE IF NOT EXISTS cab7_ledger_journal (
    id int unsigned NOT NULL AUTO_INCREMENT,
    user_id int unsigned NOT NULL COMMENT 'Associated user',
    date date NOT NULL COMMENT 'Date of booking',
    amount decimal(7,2) unsigned NOT NULL COMMENT 'Not editable booking amount as string',
    vat_code tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '[0]: 0%, [8]: 7%, [9]: 19%',
    client_details varchar(255) NOT NULL COMMENT 'User comment about current booking entry',
    system_details varchar(255) NOT NULL COMMENT 'System log about internal booking details',
    internal_bill_number varchar(6) NOT NULL DEFAULT '000000',
    original_bill_number varchar(99) NULL DEFAULT NULL,
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE InnoDB DEFAULT CHARSET utf8mb4;
INSERT INTO cab7_ledger_journal
    SELECT * FROM back_ledger_journal;
DROP TABLE IF EXISTS back_ledger_journal;

# DELETE FROM cab7_ledger_journal WHERE id BETWEEN 1 AND 215;
# ALTER TABLE cab7_ledger_journal AUTO_INCREMENT = 339;
# ALTER TABLE cab7_ledger_journal DROP COLUMN direct_account;
# ALTER TABLE cab7_ledger_journal DROP COLUMN offset_account;

# Number 1600 Cash entries
SELECT month(date) AS month, max(internal_bill_number) AS num_per_month FROM cab7_ledger_journal
WHERE system_details LIKE '%1600 Kasse%' AND date LIKE '2018-01%'
GROUP BY month
ORDER BY month;

# Search for dupplicates
SELECT date, count, amount_floored
FROM (
    SELECT date, floor(abs(amount)) amount_floored, count(*) AS count
    FROM cab7_ledger_journal
    WHERE deleted_at IS NULL
    GROUP BY date, amount_floored, client_details
) duplicates
WHERE count > 1 AND date > '2018-05-31'
ORDER BY date, count DESC;

SELECT floor(abs(-48.09));






