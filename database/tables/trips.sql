DROP TABLE IF EXISTS trips;
CREATE TABLE IF NOT EXISTS trips (
    id int unsigned NOT NULL,
    user_id int unsigned NOT NULL,
    gross_fare decimal(5,2) NOT NULL,
    vat tinyint unsigned NOT NULL,
    occupied_distance decimal(5,2) NOT NULL,
    started_at datetime NOT NULL,
    ended_at datetime NOT NULL,
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY started_ended_unique(started_at, ended_at)
) ENGINE InnoDB DEFAULT CHARSET = utf8mb4;

SELECT sum(gross_fare) 'gross revenue', sum(occupied_distance) 'occupied distance' FROM trips;
