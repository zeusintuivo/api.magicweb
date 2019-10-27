SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS cab7_skr04_accounts;
SET FOREIGN_KEY_CHECKS = 1;
CREATE TABLE IF NOT EXISTS cab7_skr04_accounts (
    id int UNSIGNED NOT NULL,
    pid int UNSIGNED NOT NULL DEFAULT 0,
    en_GB varchar(255) NULL UNIQUE COMMENT 'Label T-Account',
    de_DE varchar(255) NOT NULL UNIQUE COMMENT 'Label T-Account',
    vat_code tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '0% [0], 7% [8], 19% [9]',
    private bool NOT NULL DEFAULT 0 COMMENT 'Accounts not available for the client',
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE InnoDB DEFAULT CHARSET utf8mb4;

# Standard charts :: Sachkontenrhmen
INSERT INTO cab7_skr04_accounts (id, en_GB, de_DE, pid, vat_code, private) VALUES
(400, 'Technical equipment and machinery', 'Technische Anlagen und Maschinen', 0, 0, 0),
(420, 'Technical equipment', 'Technische Anlagen', 0, 0, 0),
(440, 'Machinery', 'Maschinen', 0, 0, 0),
(1200, 'Trade receivables', 'Forderungen aus Lieferungen und Leistungen', 0, 0, 0),# TODO: set to private!
(1401, 'Deductible input tax, 7%', 'Abziehbare Vorsteuer 7%', 0, 8, 1),
(1406, 'Deductible input tax, 19%', 'Abziehbare Vorsteuer 19%', 0, 9, 1),
(1550, 'Cheques', 'Schecks', 0, 0, 0),
(1600, 'Cash', 'Kasse', 0, 0, 0),
(1800, 'Bank', 'Bank', 0, 0, 0),
(2000, 'Fixed capital', 'Festkapital', 0, 0, 0),
(2020, 'Partner loans', 'Fremdkapital', 0, 0, 0),
(2100, 'Private withdrawals', 'Privatentnahmen allgemein', 0, 0, 0),
(3100, 'Non-convertible bonds', 'Anleihen', 0, 0, 0),
(3150, 'Liabilities to banks', 'Verbindlichkeiten gegenüber Kreditinstituten', 0, 0, 0),
(3250, 'Payments received on account of orders', 'Erhaltene Anzahlungen auf Bestellungen', 0, 0, 0),
(3300, 'Trade payables', 'Verbindlichkeiten aus Lieferungen und Leistungen', 0, 0, 0),# TODO: set to private!
(3500, 'Other liabilities', 'Sonstige Verbindlichkeiten', 0, 0, 0),
(3501, 'Other liabilities - due within 1 year', 'Sonstige Verbindlichkeiten - Restlaufzeit bis 1 Jahr', 0, 0, 0),
(3504, 'Other liabilities - due between 1 and 5 years', 'Sonstige Verbindlichkeiten - Restlaufzeit 1 bis 5 Jahre', 0, 0, 0),
(3790, 'Payroll allocation', 'Lohn- und Gehaltsverrechnungskonto', 0, 0, 0),
(3801, 'VAT, 7%', 'Umsatzsteuer 7%', 0, 8, 1),
(3806, 'VAT, 19%', 'Umsatzsteuer 19%', 0, 9, 1),
(4200, 'Revenue', 'Erlöse', 0, 0, 0),
(4300, 'Revenue, 7% VAT', 'Erlöse 7% USt.', 3801, 8, 0),
(4400, 'Revenue, 19% VAT', 'Erlöse 19% USt.', 3806, 9, 0),
(4830, 'Other operating income', 'Sonstige betriebliche Erträge', 0, 0, 0),
(5200, 'Cost of merchandise', 'Wareneingang', 0, 0, 0),
(5300, 'Cost of merchandise, 7% input tax', 'Wareneingang 7% Vorsteuer', 1401, 8, 0),
(5349, 'Cost of merchandise without input tax deduction', 'Wareneingang ohne Vorsteuerabzug', 0, 0, 0),
(5400, 'Cost of merchandise, 19% input tax', 'Wareneingang 19% Vorsteuer', 1406, 9, 0),
(5600, 'Non-deductible input tax', 'Nicht abziehbare Vorsteuer', 0, 0, 0),
(5610, 'Non-deductible input tax, 7%', 'Nicht abziehbare Vorsteuer 7%', 0, 0, 0),
(5660, 'Non-deductible input tax, 19%', 'Nicht abziehbare Vorsteuer 19%', 0, 0, 0),
(5700, 'Trade discounts', 'Nachlässe', 0, 0, 0),
(5900, 'Purchased services', 'Fremdleistungen', 0, 0, 0),
(5906, 'Purchased services, 19% input tax', 'Fremdleistungen 19% Vorsteuer', 1406, 9, 0),
(5908, 'Purchased services, 7% input tax', 'Fremdleistungen 7% Vorsteuer', 1401, 8, 0),
(6000, 'Wages and salaries', 'Löhne und Gehälter', 0, 0, 0),
(6010, 'Wages', 'Löhne', 0, 0, 0),
(6020, 'Salaries', 'Gehälter', 0, 0, 0),
(6030, 'Casual labour wages', 'Aushilfslöhne', 0, 0, 0),
(6035, 'Wages for marginal part-time work', 'Löhne für Minijobs', 0, 0, 0),
(6222, 'Depreciation of motor vehicles', 'Abschreibungen auf Kfz', 0, 0, 0),
(6233, 'Write-downs for extraordinary technical and economic wear and tear of other assets', 'Absetzung für außergewöhnliche technische und wirtschaftliche Abnutzung sonstiger Wirtschaftsgüter', 0, 0, 0),
(6260, 'Immediate write-off of lowvalue assets', 'Sofortabschreibungen geringwertiger Wirtschaftsgüter', 0, 0, 0),
(6300, 'Other operating expenses', 'Sonstige betriebliche Aufwendungen', 0, 0, 0),
(6305, 'Occupancy costs', 'Raumkosten', 0, 0, 0),
(6815, 'Office supplies', 'Bürobedarf', 0, 0, 0),
(7300, 'Interest and similar expenses', 'Zinsen und ähnliche Aufwendungen', 0, 0, 0);












