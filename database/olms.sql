CREATE DATABASE IF NOT EXISTS olms;

USE olms;

CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    expiry_date DATE NOT NULL,
    quantity INT NOT NULL DEFAULT 0
);

INSERT INTO products (name, description, price, expiry_date, quantity)
VALUES
('Farm Fresh Full Cream Milk', 'Full cream fresh milk 1L', 8.50, '2026-10-20', 10),
('Milo 3-in-1', 'Chocolate malt drink', 12.50, '2027-05-10', 20);