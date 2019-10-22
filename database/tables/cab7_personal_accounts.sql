# Subaccounts :: Personenkonten
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS cab7_personal_accounts;
CREATE TABLE IF NOT EXISTS cab7_personal_accounts (
    id int UNSIGNED NOT NULL,
    skr04 int unsigned NOT NULL,
    name varchar(99) NOT NULL UNIQUE,
    kind enum ('debitor', 'creditor') NOT NULL DEFAULT 'debitor',
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (skr04) REFERENCES cab7_skr04_accounts(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE InnoDB DEFAULT CHARSET utf8mb4;

# Debitors (customers) :: Kundenkonten :: 10000 - 69999 :: parent: #1200
INSERT INTO cab7_personal_accounts (id, skr04, name, kind) VALUES (10001, 1200, 'Thomas Lange', 'debitor');

# Creditors (suppliers) :: Lieferantenkonten :: 70000 - 99999 :: parent: #3300
INSERT INTO cab7_personal_accounts (id, skr04, name, kind) VALUES (70001, 3300, 'Apple Computers Inc.', 'creditor');

SET FOREIGN_KEY_CHECKS = 1;
