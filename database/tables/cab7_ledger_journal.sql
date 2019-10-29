SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS cab7_ledger_journal;-- Be careful with this!
SET FOREIGN_KEY_CHECKS = 1;
CREATE TABLE IF NOT EXISTS cab7_ledger_journal (
    id int unsigned NOT NULL AUTO_INCREMENT,
    user_id int unsigned NOT NULL COMMENT 'Associated user',
    date date NOT NULL COMMENT 'Date of booking',
    bill_own_number varchar(99) NULL DEFAULT NULL,
    sequence_number varchar(15) NOT NULL UNIQUE,
    amount varchar(9) NOT NULL COMMENT 'Not editable booking amount as string',
    vat_code tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '0% [0], 7% [8], 19% [9]',
    client_details varchar(255) NOT NULL COMMENT 'User comment about current booking entry',
    system_details varchar(255) NOT NULL COMMENT 'System log about internal booking details',
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE InnoDB DEFAULT CHARSET utf8mb4;
