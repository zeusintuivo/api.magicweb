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
(135, 'Computer software (license)', 'EDV-Software (Lizenzen)', 0, 0, 0),
(144, 'Computer software (assets)', 'EDV-Software (Vermögen)', 0, 0, 0),
(400, 'Technical equipment and machinery', 'Technische Anlagen und Maschinen', 0, 0, 0),
(420, 'Technical equipment', 'Technische Anlagen', 0, 0, 0),
(440, 'Machinery', 'Maschinen', 0, 0, 0),
(500, 'Other equipment, operating and office equipment', 'Andere Anlagen, Betriebs- und Geschäftsausstattung', 0, 0, 0),
(510, 'Other equipment', 'Andere Anlagen', 0, 0, 0),
(520, 'Passenger cars', 'Pkw', 0, 0, 0),
(521, 'Passenger cars, 19% input tax', 'Pkw 19% Vorsteuer', 1406, 9, 0),
(540, 'Heavy goods vehicles', 'Lkw', 0, 0, 0),
(560, 'Other transportation resources', 'Sonstige Transportmittel', 0, 0, 0),
(620, 'Tools', 'Werkzeuge', 0, 0, 0),
(630, 'Operating equipment', 'Betriebsausstattung', 0, 0, 0),
(635, 'Office equipment', 'Geschäftsausstattung', 0, 0, 0),
(640, 'Shop fittings', 'Ladeneinrichtung', 0, 0, 0),
(650, 'Office fittings', 'Büroeinrichtung', 0, 0, 0),
(660, 'Scaffolding and formwork materials', 'Gerüst- und Schalungsmaterial', 0, 0, 0),
(670, 'Low-value assets', 'Geringwertige Wirtschaftsgüter', 0, 0, 0),
(675, 'Assets (collective item)', 'Wirtschaftsgüter (Sammelposten)', 0, 0, 0),
(680, 'Leasehold improvements', 'Einbauten in fremde Grundstücke', 0, 0, 0),
(690, 'Other operating and office equipment', 'Sonstige Betriebs- und Geschäftsausstattung', 0, 0, 0),
(1200, 'Trade receivables', 'Forderungen aus Lieferungen und Leistungen', 0, 0, 1),
(1370, 'Items in transit', 'Durchlaufende Posten', 0, 0, 0),
(1400, 'Deductible input tax', 'Abziehbare Vorsteuer', 0, 0, 0),
(1401, 'Deductible input tax, 7%', 'Abziehbare Vorsteuer 7%', 0, 8, 1),
(1406, 'Deductible input tax, 19%', 'Abziehbare Vorsteuer 19%', 0, 9, 1),
(1420, 'Accounts receivable from VAT advance payments', 'Forderungen aus UmsatzsteuerVorauszahlungen', 0, 0, 0),
(1421, 'VAT receivables, current year', 'Umsatzsteuerforderungen laufendes Jahr', 0, 0, 0),
(1422, 'VAT receivables, previous year', 'Umsatzsteuerforderungen Vorjahr', 0, 0, 0),
(1425, 'VAT receivables, earlier years', 'Umsatzsteuerforderungen frühere Jahre', 0, 0, 0),
(1460, 'Cash in transit', 'Geldtransit', 0, 0, 0),
(1550, 'Cheques', 'Schecks', 0, 0, 0),
(1600, 'Cash', 'Kasse', 0, 0, 0),
(1800, 'Bank', 'Bank', 0, 0, 0),
(2000, 'Fixed capital', 'Festkapital', 0, 0, 0),
(2020, 'Partner loans', 'Fremdkapital', 0, 0, 0),
(2100, 'Private withdrawals', 'Privatentnahmen allgemein', 0, 0, 0),
(2130, 'Non-cash withdrawals', 'Unentgeltliche Wertabgaben', 0, 0, 0),
(2150, 'Private taxes', 'Privatsteuern', 0, 0, 0),
(2180, 'Private contributions', 'Privateinlagen', 0, 0, 0),
(2280, 'Extraordinary expenses', 'Außergewöhnliche Belastungen', 0, 0, 0),
(3100, 'Non-convertible bonds', 'Anleihen', 0, 0, 0),
(3150, 'Liabilities to banks', 'Verbindlichkeiten gegenüber Kreditinstituten', 0, 0, 0),
(3151, 'Liabilities to banks - due within 1 year', 'Verbindlichkeiten gegenüber Kreditinstituten - Restlaufzeit bis 1 Jahr', 0, 0, 0),
(3160, 'Liabilities to banks - due between 1 and 5 years', 'Verbindlichkeiten gegenüber Kreditinstituten - Restlaufzeit 1 bis 5 Jahre', 0, 0, 0),
(3170, 'Liabilities to banks - due after more than 5 years', 'Verbindlichkeiten gegenüber Kreditinstituten - Restlaufzeit größer 5 Jahre', 0, 0, 0),
(3180, 'Liabilities to banks under instalment credit agreements', 'Verbindlichkeiten gegenüber Kreditinstituten aus Teilzahlungsverträgen', 0, 0, 0),
(3181, 'Liabilities to banks under instalment credit agreements - due within 1 year', 'Verbindlichkeiten gegenüber Kreditinstituten aus Teilzahlungsverträgen - Restlaufzeit bis 1 Jahr', 0, 0, 0),
(3190, 'Liabilities to banks under instalment credit agreements - due between 1 and 5 years', 'Verbindlichkeiten gegenüber Kreditinstituten aus Teilzahlungsverträgen - Restlaufzeit 1 bis 5 Jahre', 0, 0, 0),
(3200, 'Liabilities to banks under instalment credit agreements - due after more than 5 years', 'Verbindlichkeiten gegenüber Kreditinstituten aus Teilzahlungsverträgen - Restlaufzeit größer 5 Jahre', 0, 0, 0),
(3250, 'Payments received on account of orders', 'Erhaltene Anzahlungen auf Bestellungen', 0, 0, 0),
(3300, 'Trade payables', 'Verbindlichkeiten aus Lieferungen und Leistungen', 0, 0, 1),
(3500, 'Other liabilities', 'Sonstige Verbindlichkeiten', 0, 0, 0),
(3501, 'Other liabilities - due within 1 year', 'Sonstige Verbindlichkeiten - Restlaufzeit bis 1 Jahr', 0, 0, 0),
(3504, 'Other liabilities - due between 1 and 5 years', 'Sonstige Verbindlichkeiten - Restlaufzeit 1 bis 5 Jahre', 0, 0, 0),
(3790, 'Payroll allocation', 'Lohn- und Gehaltsverrechnungskonto', 0, 0, 0),
(3801, 'VAT, 7%', 'Umsatzsteuer 7%', 0, 8, 1),
(3806, 'VAT, 19%', 'Umsatzsteuer 19%', 0, 9, 1),
(3820, 'VAT prepayments', 'Umsatzsteuer-Vorauszahlungen', 0, 0, 0),
(3840, 'VAT, current year', 'Umsatzsteuer laufendes Jahr', 0, 0, 0),
(3841, 'VAT, previous year', 'Umsatzsteuer Vorahr', 0, 0, 0),
(3845, 'VAT, earlier years', 'Umsatzsteuer frühere Jahre', 0, 0, 0),
(4200, 'Revenue', 'Erlöse', 0, 0, 0),
(4300, 'Revenue, 7% VAT', 'Erlöse 7% USt.', 3801, 8, 0),
(4400, 'Revenue, 19% VAT', 'Erlöse 19% USt.', 3806, 9, 0),
(4830, 'Other operating income', 'Sonstige betriebliche Erträge', 0, 0, 0),
(4845, 'Revenue from sales of tangible fixed assets, 19% VAT (book gain)', 'Erlöse aus Verkäufen Sachanlagevermögen 19 % USt (bei Buchgewinn)', 3806, 9, 0),
(5100, 'Raw materials, consumables and supplies, 19% input tax', 'Einkauf Roh-, Hilfs- und Betriebsstoffe 19% Vorsteuer', 1406, 9, 0),
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
(5923, 'Other services supplied by a contractor in another EU country, 19% input tax and 19% VAT', 'Sonstige Leistungen eines im anderen EU-Land ansässigen Unternehmers 19 % Vorsteuer und 19 % Umsatzsteuer', 0, 0, 0),
(6000, 'Wages and salaries', 'Löhne und Gehälter', 0, 0, 0),
(6010, 'Wages', 'Löhne', 0, 0, 0),
(6020, 'Salaries', 'Gehälter', 0, 0, 0),
(6030, 'Casual labour wages', 'Aushilfslöhne', 0, 0, 0),
(6035, 'Wages for marginal part-time work', 'Löhne für Minijobs', 0, 0, 0),
(6120, 'Contributions to occupational health and safety agency', 'Beiträge zur Berufsgenossenschaft', 0, 0, 0),
(6222, 'Depreciation of motor vehicles', 'Abschreibungen auf Kfz', 0, 0, 0),
(6233, 'Write-downs for extraordinary technical and economic wear and tear of other assets', 'Absetzung für außergewöhnliche technische und wirtschaftliche Abnutzung sonstiger Wirtschaftsgüter', 0, 0, 0),
(6260, 'Immediate write-off of lowvalue assets', 'Sofortabschreibungen geringwertiger Wirtschaftsgüter', 0, 0, 0),
(6300, 'Other operating expenses', 'Sonstige betriebliche Aufwendungen', 0, 0, 0),
(6305, 'Occupancy costs', 'Raumkosten', 0, 0, 0),
(6310, 'Rent (immovable property)', 'Miete (unbewegliche Wirtschaftsgüter)', 0, 0, 0),
(6400, 'Insurance premiums', 'Versicherungen', 0, 0, 0),
(6420, 'Contributions', 'Beiträge', 0, 0, 0),
(6430, 'Other levies', 'Sonstige Abgaben', 0, 0, 0),
(6495, 'Hardware and software maintenance expenses', 'Wartungskosten für Hard- und Software', 0, 0, 0),
(6500, 'Vehicle expenses', 'Fahrzeugkosten', 0, 0, 0),
(6520, 'Motor vehicle insurance', 'Kfz-Versicherungen', 0, 0, 0),
(6530, 'Current motor vehicle operating costs', 'Laufende Kfz-Betriebskosten', 0, 0, 0),
(6540, 'Motor vehicle repairs', 'Kfz-Reparaturen', 0, 0, 0),
(6541, 'Motor vehicle repairs, 19% input tax', 'Kfz-Reparaturen 19% Vorsteuer', 1406, 9, 0),
(6550, 'Garage rent', 'Garagenmiete', 0, 0, 0),
(6560, 'Operating leases (motor vehicles)', 'Mietleasing Kfz', 0, 0, 0),
(6570, 'Other motor vehicle expenses', 'Sonstige Kfz-Kosten', 0, 0, 0),
(6673, 'Business owner travel expenses, cost of travel', 'Reisekosten Unternehmer Fahrtkosten', 0, 0, 0),
(6674, 'Business owner travel expenses, additional subsistence costs', 'Reisekosten Unternehmer Verpflegungsmehraufwand', 0, 0, 0),
(6680, 'Business owner travel expenses, accommodation costs and incidental travel expenses', 'Reisekosten Unternehmer Übernachtungsaufwand und Reisenebenkosten', 0, 0, 0),
(6681, 'Business owner travel expenses, accommodation costs and incidental travel expenses, 19% input tax', 'Reisekosten Unternehmer Übernachtungsaufwand und Reisenebenkosten 19% Vorsteuer', 1406, 9, 0),
(6682, 'Business owner travel expenses, accommodation costs and incidental travel expenses, 7% input tax', 'Reisekosten Unternehmer Übernachtungsaufwand und Reisenebenkosten 7% Vorsteuer', 1401, 8, 0),
(6800, 'Postage', 'Porto', 0, 0, 0),
(6805, 'Telephone', 'Telefon', 0, 0, 0),
(6810, 'Fax and Internet costs', 'Telefax und Internetkosten', 0, 0, 0),
(6815, 'Office supplies', 'Bürobedarf', 0, 0, 0),
(6816, 'Office supplies, 19% input tax', 'Bürobedarf 19% Vorsteuer', 1406, 9, 0),
(6820, 'Newspapers, books (specialist literature)', 'Zeitschriften, Bücher (Fachliteratur)', 0, 0, 0),
(6821, 'Training costs', 'Fortbildungskosten', 0, 0, 0),
(6825, 'Legal and consulting costs', 'Rechts- und Beratungskosten', 0, 0, 0),
(6830, 'Bookkeeping costs', 'Buchführungskosten', 0, 0, 0),
(6831, 'Bookkeeping costs, 19% input tax', 'Buchführungskosten 19% Vorsteuer', 1406, 9, 0),
(6855, 'Incidental monetary transaction costs', 'Nebenkosten des Geldverkehrs', 0, 0, 0),
(7300, 'Interest and similar expenses', 'Zinsen und ähnliche Aufwendungen', 0, 0, 0),
(7326, 'Borrowing costs for fixed assets', 'Zinsen zur Finanzierung des Anlagevermögens', 0, 0, 0),
(7685, 'Motor vehicle tax', 'Kfz-Steuer', 0, 0, 0),
(9000, 'Balances brought forward, G/L accounts', 'Saldenvorträge, Sachkonten', 0, 0, 0);











