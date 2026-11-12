
<?php
// ============================================
// OLMS Admin Dashboard
// Administrator-only product management
// ============================================

// Require administrator authentication
require_once "auth.php";

// Ensure a CSRF token exists for logout
if (
    !isset($_SESSION["csrf_token"]) ||
    !is_string($_SESSION["csrf_token"]) ||
    $_SESSION["csrf_token"] === ""
) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// Retrieve administrator username safely
$adminUsername = $_SESSION["admin_username"] ?? "Administrator";

if (!is_string($adminUsername)) {
    $adminUsername = "Administrator";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard - OLMS</title>

    <link rel="stylesheet" href="../css/style.css">

    <style>
        /* General Page Styling */
        * {
            box-sizing: border-box;
        }

        body {
            background: #fffaf5;
            font-family: Arial, sans-serif;
            color: #554b47;
            margin: 0;
            padding: 0;
        }

        /* Dashboard Container */
        .dashboard-container {
            max-width: 1100px;
            margin: 50px auto;
            padding: 25px;
        }

        /* Dashboard Header */
        .dashboard-header {
            background: #fbe6ea;
            padding: 40px;
            border-radius: 20px;
            margin-bottom: 35px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }

        .dashboard-header h1 {
            color: #76515d;
            font-size: 34px;
            margin: 0 0 15px;
        }

        .dashboard-header p {
            color: #806d70;
            font-size: 16px;
            margin: 0;
        }

        /* Logout Form */
        .logout-form {
            background: transparent;
            padding: 0;
            margin: 0;
            max-width: none;
            width: auto;
            flex-shrink: 0;
        }

        /* Logout Button */
        .logout-button {
            background: #fffaf5;
            color: #76515d;
            border: 1px solid #e8d7d9;
            font-weight: bold;
            padding: 12px 25px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.2s ease;
        }

        .logout-button:hover {
            background: #f2cbd5;
        }

        /* Product Management Section */
        .section-title {
            color: #76515d;
            font-size: 24px;
            margin-bottom: 20px;
        }

        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 22px;
            margin-bottom: 40px;
        }

        /* Dashboard Cards */
        .dashboard-card {
            background: #ffffff;
            padding: 30px;
            border: 1px solid #f0e1e4;
            border-radius: 18px;
            box-shadow: 0 5px 18px rgba(100, 70, 80, 0.06);
            transition: transform 0.2s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-4px);
        }

        .dashboard-card h3 {
            color: #76515d;
            margin-top: 0;
        }

        .dashboard-card p {
            color: #806d70;
            font-size: 14px;
            line-height: 1.7;
            min-height: 50px;
        }

        /* Dashboard Navigation Buttons */
        .dashboard-link {
            display: inline-block;
            background: #a5b99c;
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: bold;
            text-decoration: none;
            margin-top: 10px;
        }

        .dashboard-link:hover {
            background: #829a78;
        }

        /* Responsive Design */
        @media (max-width: 600px) {

            .dashboard-container {
                margin: 20px auto;
                padding: 15px;
            }

            .dashboard-header {
                padding: 25px;
            }

            .dashboard-header h1 {
                font-size: 27px;
            }

            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .logout-form,
            .logout-button {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="dashboard-container">

    <!-- Dashboard Header -->
    <div class="dashboard-header">

        <div class="header-content">

            <div class="header-text">

                <h1>OLMS Admin Dashboard ♡</h1>

                <p>
                    Welcome back,
                    <strong>
                        <?= htmlspecialchars(
                            $adminUsername,
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>
                    </strong>!
                </p>

            </div>

            <!-- Administrator Logout -->
            <form
                action="admin_logout.php"
                method="POST"
                class="logout-form"
                onsubmit="return confirm('Are you sure you want to log out of your admin account?');"
            >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars(
                        $_SESSION["csrf_token"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>"
                >

                <button type="submit" class="logout-button">
                    Logout
                </button>

            </form>

        </div>

    </div>

    <!-- Product Management Section -->
    <h2 class="section-title">Product Management</h2>

    <div class="dashboard-grid">

        <!-- Add Product -->
        <div class="dashboard-card">

            <h3>Add Product</h3>

            <p>
                Add new products to your local mart
                and manage their information.
            </p>

            <a
                href="../product/add_product.php"
                class="dashboard-link"
            >
                Add Product
            </a>

        </div>

        <!-- View Products -->
        <div class="dashboard-card">

            <h3>View Products</h3>

            <p>
                View, edit, and delete existing
                products in your inventory.
            </p>

            <a
                href="../product/view_product.php"
                class="dashboard-link"
            >
                View Products
            </a>

        </div>

        <!-- Search Products -->
        <div class="dashboard-card">

            <h3>Search Products</h3>

            <p>
                Quickly find products by their
                name or product ID.
            </p>

            <a
                href="../product/search_product.php"
                class="dashboard-link"
            >
                Search Products
            </a>

        </div>

    </div>

</div>

</body>
</html>
