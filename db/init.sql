CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sightings (
    id SERIAL PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    sighting_date TIMESTAMP NOT NULL,
    location VARCHAR(100) NOT NULL,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    user_id INT REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    image_path VARCHAR(255),
    xml_data XML -- Nový sloupec pro ukládání XML dat
);

-- Vložení testovacích dat
INSERT INTO sightings (title, description, sighting_date, location, latitude, longitude) VALUES
('Bright lights over Prague', 'Three bright lights forming a triangle pattern moving silently across the sky.', '2023-05-15 21:30:00', 'Prague, Czech Republic', 50.0755, 14.4378),
('Strange object near Brno', 'Disk-shaped object hovering for about 10 minutes before disappearing suddenly.', '2023-06-22 19:45:00', 'Brno, Czech Republic', 49.1951, 16.6068);