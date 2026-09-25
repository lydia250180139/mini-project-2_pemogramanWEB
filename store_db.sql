CREATE DATABASE IF NOT EXISTS store_db;

USE store_db;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    category VARCHAR(50) NOT NULL DEFAULT 'Umum',
    price DECIMAL(12,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO products (name, category, price, stock) VALUES
('Laptop ASUS VivoBook', 'Laptop', 7500000, 8),
('Laptop Lenovo IdeaPad', 'Laptop', 6800000, 5),
('Mouse Logitech M331', 'Aksesoris', 350000, 15);