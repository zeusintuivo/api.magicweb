SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS cab7_skr04_accounts;
SET FOREIGN_KEY_CHECKS = 1;
CREATE TABLE IF NOT EXISTS cab7_skr04_accounts (
    id int UNSIGNED NOT NULL,
    en_GB varchar(255) NULL UNIQUE COMMENT 'Label T-Account',
    de_DE varchar(255) NOT NULL UNIQUE COMMENT 'Label T-Account',
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE InnoDB DEFAULT CHARSET utf8mb4;

# Standard charts :: Sachkontenrhmen
INSERT INTO cab7_skr04_accounts (id, en_GB, de_DE) VALUES
(400, 'Technical equipment and machinery', 'Technische Anlagen und Maschinen'),
(420, 'Technical equipment', 'Technische Anlagen'),
(440, 'Machinery', 'Maschinen'),
(1200, 'Trade receivables', 'Forderungen aus Lieferungen und Leistungen'),
(1401, 'Deductible input tax, 7%', 'Abziehbare Vorsteuer 7%'),
(1406, 'Deductible input tax, 19%', 'Abziehbare Vorsteuer 19%'),
(1550, 'Cheques', 'Schecks'),
(1600, 'Cash', 'Kasse'),
(1800, 'Bank', 'Bank'),
(2000, 'Fixed capital', 'Festkapital'),
(2020, 'Partner loans', 'Fremdkapital'),
(2100, 'Private withdrawals', 'Privatentnahmen allgemein'),
(3100, 'Non-convertible bonds', 'Anleihen'),
(3150, 'Liabilities to banks', 'Verbindlichkeiten gegenüber Kreditinstituten'),
(3250, 'Payments received on account of orders', 'Erhaltene Anzahlungen auf Bestellungen'),
(3300, 'Trade payables', 'Verbindlichkeiten aus Lieferungen und Leistungen'),
(3500, 'Other liabilities', 'Sonstige Verbindlichkeiten'),
(3501, 'Other liabilities - due within 1 year', 'Sonstige Verbindlichkeiten - Restlaufzeit bis 1 Jahr'),
(3504, 'Other liabilities - due between 1 and 5 years', 'Sonstige Verbindlichkeiten - Restlaufzeit 1 bis 5 Jahre'),
(3790, 'Payroll allocation', 'Lohn- und Gehaltsverrechnungskonto'),
(3801, 'VAT, 7%', 'Umsatzsteuer 7%'),
(3806, 'VAT, 19%', 'Umsatzsteuer 19%'),
(4200, 'Revenue', 'Erlöse'),
(4300, 'Revenue, 7% VAT', 'Erlöse 7% USt'),
(4340, 'Revenue, 16% VAT', 'Erlöse 16% USt'),
(4400, 'Revenue, 19% VAT', 'Erlöse 19% USt'),
(4830, 'Other operating income', 'Sonstige betriebliche Erträge'),
(5200, 'Cost of merchandise', 'Wareneingang'),
(5300, 'Cost of merchandise, 7% input tax', 'Wareneingang 7% Vorsteuer'),
(5349, 'Cost of merchandise without input tax deduction', 'Wareneingang ohne Vorsteuerabzug'),
(5400, 'Cost of merchandise, 19% input tax', 'Wareneingang 19% Vorsteuer'),
(5600, 'Non-deductible input tax', 'Nicht abziehbare Vorsteuer'),
(5610, 'Non-deductible input tax, 7%', 'Nicht abziehbare Vorsteuer 7%'),
(5660, 'Non-deductible input tax, 19%', 'Nicht abziehbare Vorsteuer 19%'),
(5700, 'Trade discounts', 'Nachlässe'),
(5900, 'Purchased services', 'Fremdleistungen'),
(5906, 'Purchased services, 19% input tax', 'Fremdleistungen 19% Vorsteuer'),
(5908, 'Purchased services, 7% input tax', 'Fremdleistungen 7% Vorsteuer'),
(6000, 'Wages and salaries', 'Löhne und Gehälter'),
(6010, 'Wages', 'Löhne'),
(6020, 'Salaries', 'Gehälter'),
(6030, 'Casual labour wages', 'Aushilfslöhne'),
(6035, 'Wages for marginal part-time work', 'Löhne für Minijobs'),
(6222, 'Depreciation of motor vehicles', 'Abschreibungen auf Kfz'),
(6233, 'Write-downs for extraordinary technical and economic wear and tear of other assets', 'Absetzung für außergewöhnliche technische und wirtschaftliche Abnutzung sonstiger Wirtschaftsgüter'),
(6260, 'Immediate write-off of lowvalue assets', 'Sofortabschreibungen geringwertiger Wirtschaftsgüter'),
(6300, 'Other operating expenses', 'Sonstige betriebliche Aufwendungen'),
(6305, 'Occupancy costs', 'Raumkosten'),
(6815, 'Office supplies', 'Bürobedarf'),
(7300, 'Interest and similar expenses', 'Zinsen und ähnliche Aufwendungen');












