
-- ============================================
-- Online Local Mart System (OLMS)
-- Database Setup
-- ============================================

-- Create database
CREATE DATABASE IF NOT EXISTS olms
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE olms;

-- ============================================
-- 1. Products Table
-- ============================================

CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    expiry_date DATE NOT NULL,
    quantity INT NOT NULL DEFAULT 0
);

-- ============================================
-- 2. Admins Table
-- ============================================

CREATE TABLE IF NOT EXISTS admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- ============================================
-- 3. Sample Product Data
-- ============================================

-- Insert sample products only if the
-- products table is currently empty.

INSERT INTO products
    (name, description, price, expiry_date, quantity)
SELECT
    'Farm Fresh Full Cream Milk',
    'Full cream fresh milk 1L',
    8.50,
    '2027-10-20',
    10
WHERE NOT EXISTS (
    SELECT 1 FROM products
);

INSERT INTO products
    (name, description, price, expiry_date, quantity)
SELECT
    'Milo 3-in-1',
    'Chocolate malt drink',
    12.50,
    '2027-05-10',
    20
WHERE NOT EXISTS (
    SELECT 1
    FROM products
    WHERE name = 'Milo 3-in-1'
);

-- ============================================
-- 4. Admin Account Setup
-- ============================================

-- Admin passwords must be hashed using
-- PHP password_hash() before insertion.
--
-- Do not store plain-text passwords.
-- Do not commit real credentials to GitHub.
--
-- Example:
--
-- INSERT INTO admins (username, password)
-- VALUES ('admin', '<generated_password_hash>');
--
-- Replace <generated_password_hash> with
-- a real hash generated locally using PHP.

-- ============================================
-- End of OLMS Database Setup
-- ============================================
