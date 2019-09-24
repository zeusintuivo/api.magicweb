SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS ledger_journal;-- Be careful with this!
CREATE TABLE IF NOT EXISTS ledger_journal (
    id int unsigned NOT NULL AUTO_INCREMENT,
    date date NOT NULL COMMENT 'Date of booking',
    bill int unsigned NOT NULL COMMENT 'Monthly based number',
    amount decimal(7,2) NOT NULL COMMENT 'Booking amount',
    details varchar(255) NOT NULL COMMENT 'Booking hint',
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY unique_entry (date, bill)
) ENGINE InnoDB DEFAULT CHARSET utf8mb4;
SET FOREIGN_KEY_CHECKS = 1;
