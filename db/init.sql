CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    material VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE calculations (
    id SERIAL PRIMARY KEY,
    product_id INTEGER REFERENCES products(id),
    material VARCHAR(50),
    operation_type VARCHAR(50),
    tool_material VARCHAR(50),
    cutting_depth DECIMAL(10,2),
    cutting_speed DECIMAL(10,2),
    feed_rate DECIMAL(10,2),
    spindle_speed INTEGER,
    surface_roughness DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE cutting_modes (
    id SERIAL PRIMARY KEY,
    material VARCHAR(255),
    operation_type VARCHAR(255),
    tool_material VARCHAR(255),
    recommended_speed NUMERIC,
    recommended_feed NUMERIC,
    recommended_depth NUMERIC
);