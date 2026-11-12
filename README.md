# CSEB5223-OLMS

## Online Local Mart System (OLMS)

The Online Local Mart System (OLMS) is a web-based application developed for the **CSEB5223 Software Construction** course at Universiti Tenaga Nasional (UNITEN).

The system allows customers to browse available grocery products and view their details, while administrators can manage product information and inventory through a secure admin portal.

## Technologies Used

- **Frontend:** HTML, CSS
- **Backend:** PHP
- **Database:** MySQL
- **Local Development Environment:** Laragon
- **Database Management:** HeidiSQL
- **Version Control:** Git and GitHub
- **API:** PHP-based REST-style Product API (GET)

## Main Features

### Customer Module

- Browse available products without logging in
- Search products by name
- View product details, including price, description, expiry date, and quantity
- Retrieve product listings through the Product API

### Admin Module

- Secure admin login and logout
- Admin dashboard
- Add new products
- View existing products
- Edit product information
- Delete products
- Search products
- Manage product inventory

### Security Features

- Prepared SQL statements to reduce SQL injection risks
- Password hashing and verification
- Session-based admin authentication
- CSRF protection for administrative forms
- Server-side input validation
- HTML output escaping
- Database error handling and logging

## Project Structure

```text
olms-group/
├── admin/
│   ├── admin_dashboard.php
│   ├── admin_login.php
│   ├── admin_logout.php
│   └── auth.php
├── api/
│   └── product.php
├── classes/
│   └── Product.php
├── config/
│   └── database.php
├── css/
│   └── style.css
├── customer/
│   ├── customer_browse.php
│   └── product_details.php
├── database/
│   └── olms.sql
├── product/
│   ├── add_product.php
│   ├── edit_product.php
│   ├── delete_product.php
│   ├── search_product.php
│   └── view_product.php
├── index.php
└── README.md
```

## Installation and Setup

### 1. Clone the Repository

Open a terminal in Laragon's `www` directory and run:

```bash
git clone https://github.com/Zhrdnsh/CSEB5223-OLMS.git olms-group
```

Alternatively, download the repository as a ZIP file and extract it into:

```text
C:\laragon\www\olms-group
```

### 2. Start Laragon

1. Open Laragon.
2. Click **Start All**.
3. Ensure Apache and MySQL are running.

### 3. Create the Database

1. Open HeidiSQL through Laragon.
2. Connect to the local MySQL server.
3. Open the SQL file:

   `database/olms.sql`

4. Execute the SQL script.

This creates the `olms` database and the `products` and `admins` tables.

### 4. Configure Database Connection

Open:

`config/database.php`

For a default local Laragon installation, the configuration is:

```php
$host = "localhost";
$username = "root";
$password = "";
$database = "olms";
```

Update these values if your local MySQL configuration is different.

**Note:** The empty-password root account is intended only for local development. Do not use it for production deployment.

### 5. Create an Admin Account

Admin passwords must be stored as hashes rather than plain text.

Open a terminal with PHP available and run:

```bash
php -r "echo password_hash('YOUR_PASSWORD_HERE', PASSWORD_DEFAULT), PHP_EOL;"
```

Replace `YOUR_PASSWORD_HERE` with your chosen password.

Copy the generated hash.

Next, open HeidiSQL and execute:

```sql
USE olms;

INSERT INTO admins (username, password)
VALUES ('admin', 'PASTE_GENERATED_HASH_HERE');
```

Replace `PASTE_GENERATED_HASH_HERE` with the generated password hash.

The example username is `admin`, but group members may choose a different username.

**Security reminders:**
- Do not store plain-text passwords in the database.
- Do not commit real passwords or credentials to GitHub.
- Do not share admin credentials publicly.
- Generate a new password hash for each locally configured admin account.

### 6. Run the Application

Open your browser and visit:

**Homepage**

`http://localhost/olms-group/`

**Customer Product Browsing**

`http://localhost/olms-group/customer/customer_browse.php`

**Admin Login**

`http://localhost/olms-group/admin/admin_login.php`

Log in using the admin credentials created during setup.

## Product API

OLMS provides a read-only Product API for retrieving product information.

**Retrieve all products:**

```http
GET /olms-group/api/product.php
```

**Search products:**

```http
GET /olms-group/api/product.php?search=milk
```

The API returns JSON responses containing product information.

## Testing

The application should be tested for:

- Successful and unsuccessful admin login
- Authentication and access restrictions
- Adding products
- Editing products
- Deleting products
- Searching products
- Customer product browsing
- Product API responses
- Invalid input handling
- CSRF protection
- Database connection failures

## Contributors

Developed as a group project for CSEB5223 Software Construction, Universiti Tenaga Nasional (UNITEN).

## Academic Purpose

This project was developed for educational purposes as part of the Software Construction coursework.


## Team Members

- Muhammad Zahir Danish
- Nurimaneesa
- Nur Aina Aliah

## Coding Standards

The project follows defined naming conventions, consistent
code formatting, and function/method modularity standards
to improve readability and maintainability.
