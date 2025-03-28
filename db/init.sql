DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_tables WHERE tablename = 'users') THEN
        CREATE TABLE users (
            id SERIAL PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    END IF;

IF NOT EXISTS (SELECT 1 FROM pg_tables WHERE tablename = 'ufo_sightings') THEN
        CREATE TABLE ufo_sightings (
            id SERIAL PRIMARY KEY,
            date_time TIMESTAMP,
            date_documented TIMESTAMP,
            year INTEGER,
            month INTEGER,
            hour INTEGER,
            season VARCHAR(20),
            country_code CHAR(6),
            country VARCHAR(100),
            region VARCHAR(100),
            locale VARCHAR(100),
            latitude DECIMAL(10, 8),
            longitude DECIMAL(11, 8),
            ufo_shape VARCHAR(50),
            encounter_seconds INTEGER,
            encounter_duration VARCHAR(100),
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE INDEX idx_ufo_sightings_date ON ufo_sightings(date_time);
        CREATE INDEX idx_ufo_sightings_location ON ufo_sightings(latitude, longitude);

        COPY ufo_sightings(
            id,
            date_time,
            date_documented,
            year,
            month,
            hour,
            season,
            country_code,
            country,
            region,
            locale,
            latitude,
            longitude,
            ufo_shape,
            encounter_seconds,
            encounter_duration,
            description
        ) FROM '/docker-entrypoint-initdb.d/data/ufo_sightings.csv' WITH (FORMAT csv, HEADER true, DELIMITER ',');
    END IF;
END $$;