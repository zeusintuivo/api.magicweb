DROP TABLE IF EXISTS cab7_insika_shifts;
CREATE TABLE IF NOT EXISTS cab7_insika_shifts (
    id int UNSIGNED NOT NULL,
    user_id int unsigned NOT NULL,
    began_at datetime NOT NULL,
    ended_at datetime NOT NULL,
    duration time NOT NULL,
    driver varchar(99) NOT NULL,
    vehicle varchar(9) NOT NULL,
    charge_total decimal(7,2) NOT NULL,
    charge_tarif decimal(7,2) NOT NULL,
    charge_extra decimal(7,2) NOT NULL,
    km_total decimal(7,2) NOT NULL,
    km_taken decimal(7,2) NOT NULL,
    km_empty decimal(7,2) NOT NULL,
    trip_count tinyint NOT NULL,
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE KEY unique_shift(began_at, ended_at, vehicle)
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4;

# SELECT sum(charge_total), sum(km_total) FROM cab7_insika_shifts;

# Driver log with km counter
# CREATE OR REPLACE VIEW cab7_driver_log AS# View's SELECT contains a variable or parameter
SELECT * FROM (
    SELECT id, began_at, ended_at, driver, vehicle, km_total, @c := ROUND(@c + km_total, 2) AS mileage, trip_count, created_at, updated_at, deleted_at
    FROM (SELECT @c := 0.00) AS excel, cab7_insika_shifts AS s
) mileage
ORDER BY began_at DESC;

# Total-Km
SELECT SUM(km_total) km FROM cab7_insika_shifts;






