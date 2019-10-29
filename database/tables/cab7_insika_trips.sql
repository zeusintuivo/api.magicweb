DROP TABLE IF EXISTS cab7_insika_trips;
CREATE TABLE IF NOT EXISTS cab7_insika_trips (
    id int UNSIGNED NOT NULL,
    user_id int UNSIGNED NOT NULL,
    began_at datetime NOT NULL,
    ended_at datetime NOT NULL,
    duration time NOT NULL,
    driver varchar(99) NOT NULL,
    vehicle varchar(9) NOT NULL,
    fare decimal(7,2) NOT NULL,
    vat tinyint unsigned NOT NULL,
    km decimal(7,2) NOT NULL,
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE KEY unique_trip(began_at, ended_at, vehicle)
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4;

# SELECT sum(gross_fare) AS 'gross revenue', sum(occupied_distance) AS 'occupied distance' FROM cab7_insika_trips;
# SELECT sum(gross_fare) AS 'umsatz 09', count(*) AS 'touren 09', GROUP_CONCAT(DISTINCT vat SEPARATOR ', ') AS 'ust. sätze' FROM cab7_insika_trips
#     WHERE started_at BETWEEN '2019-09-01' AND '2019-10-01';
