DROP TABLE IF EXISTS account_charts;
CREATE TABLE IF NOT EXISTS account_charts (
    id int UNSIGNED NOT NULL AUTO_INCREMENT,
    skr03 varchar(5) NULL UNIQUE COMMENT 'Code SKR03',
    skr04 varchar(5) NOT NULL UNIQUE COMMENT 'Code SKR04',
    en_GB varchar(255) NULL UNIQUE COMMENT 'Label T-Account',
    de_DE varchar(255) NOT NULL UNIQUE COMMENT 'Label T-Account',
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE InnoDB DEFAULT CHARSET utf8mb4;

INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('0400', NULL, 'Technische Anlagen und Maschinen');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('0420', 'Technical equipment', 'Technische Anlagen');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('0440', NULL, 'Maschinen');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('1550', NULL, 'Schecks');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('1600', 'Cash', 'Kasse');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('1800', 'Bank', 'Bank');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('2000', 'Fixed capital', 'Festkapital');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('2020', NULL, 'Fremdkapital');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('2030', NULL, 'Eigenkapital');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('2100', NULL, 'Privatentnahmen allgemein');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('3100', NULL, 'Anleihen');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('3150', NULL, 'Verbindlichkeiten gegenüber Kreditinstituten');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('3250', NULL, 'Erhaltene Anzahlungen auf Bestellungen');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('3500', NULL, 'Sonstige Verbindlichkeiten');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('3501', NULL, 'Restlaufzeit bis 1 Jahr');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('3504', NULL, 'Restlaufzeit 1 bis 5 Jahre');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('3790', NULL, 'Lohn- und Gehaltsverrechnungskonto');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('4200', NULL, 'Erlöse');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('4300', NULL, 'Erlöse 7 % USt');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('4340', NULL, 'Erlöse 16 % USt');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('4400', NULL, 'Erlöse 19 % USt');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('4830', NULL, 'Sonstige betriebliche Erträge');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('5200', NULL, 'Wareneingang');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('5300', NULL, 'Wareneingang 7 % Vorsteuer');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('5349', NULL, 'Wareneingang ohne Vorsteuerabzug');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('5400', NULL, 'Wareneingang 19 % Vorsteuer');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('5600', NULL, 'Nicht abziehbare Vorsteuer');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('5610', NULL, 'Nicht abziehbare Vorsteuer 7 %');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('5660', NULL, 'Nicht abziehbare Vorsteuer 19 %');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('5700', NULL, 'Nachlässe');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('5900', NULL, 'Fremdleistungen');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('5906', NULL, 'Fremdleistungen 19 % Vorsteuer');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('5908', NULL, 'Fremdleistungen 7 % Vorsteuer');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('6000', NULL, 'Löhne und Gehälter');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('6010', NULL, 'Löhne');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('6020', NULL, 'Gehälter');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('6030', NULL, 'Aushilfslöhne');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('6035', NULL, 'Löhne für Minijobs');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('6222', NULL, 'Abschreibungen auf Kfz');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('6233', NULL, 'Absetzung für außergewöhnliche technische und wirtschaftliche Abnutzung sonstiger Wirtschaftsgüter');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('6260', NULL, 'Sofortabschreibungen geringwertiger Wirtschaftsgüter');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('6300', NULL, 'Sonstige betriebliche Aufwen- dungen');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('6305', NULL, 'Raumkosten');
INSERT INTO account_charts (skr04, en_GB, de_DE) VALUES ('7300', NULL, 'Zinsen und ähnliche Aufwendungen');












