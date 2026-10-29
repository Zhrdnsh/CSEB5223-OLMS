
<?php
require_once "auth.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - OLMS</title>
    <link rel="stylesheet" href="../css/style.css">

    <style>
        body {
            background: #fffaf5;
            font-family: Arial, sans-serif;
            color: #554b47;
            margin: 0;
            padding: 0;
        }

        .dashboard-container {
            max-width: 1100px;
            margin: 50px auto;
            padding: 25px;
        }

        .dashboard-header {
            background: #fbe6ea;
            padding: 40px;
            border-radius: 20px;
            margin-bottom: 35px;
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

        .section-title {
            color: #76515d;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns:
                repeat(auto-fit, minmax(230px, 1fr));
            gap: 22px;
            margin-bottom: 40px;
        }

        .dashboard-card {
            background: white;
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

        .dashboard-link {
            display: inline-block;
            background: #a5b99c;
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: bold;
            text-decoration: none;
            margin-top: 10px;
        }

        .dashboard-link:hover {
            background: #829a78;
        }

        .account-section {
            background: white;
            padding: 25px 30px;
            border-radius: 18px;
            border: 1px solid #f0e1e4;
        }

        .account-section h3 {
            color: #76515d;
            margin-top: 0;
        }

        .logout-form {
            background: transparent;
            padding: 0;
            max-width: none;
        }

        .logout-button {
            background: #fbe6ea;
            color: #76515d;
            font-weight: bold;
            padding: 12px 25px;
            border-radius: 10px;
            cursor: pointer;
        }

        .logout-button:hover {
            background: #f2cbd5;
        }

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
        }
    </style>
</head>

<body>

<div class="dashboard-container">

    <div class="dashboard-header">
        <h1>OLMS Admin Dashboard ♡</h1>

        <p>
            Welcome back,
            <strong>
                <?= htmlspecialchars(
                    $_SESSION["admin_username"]
                ) ?>
            </strong>!
        </p>
    </div>

    <h2 class="section-title">Product Management</h2>

    <div class="dashboard-grid">

        <div class="dashboard-card">
            <h3>Add Product</h3>
            <p>
                Add new products to your local mart
                and manage their information.
            </p>
            <a href="add_product.php"
               class="dashboard-link">
                Add Product
            </a>
        </div>

        <div class="dashboard-card">
            <h3>View Products</h3>
            <p>
                View, edit, and delete existing
                products in your inventory.
            </p>
            <a href="view_product.php"
               class="dashboard-link">
                View Products
            </a>
        </div>

        <div class="dashboard-card">
            <h3>Search Products</h3>
            <p>
                Quickly find products by their
                name or product ID.
            </p>
            <a href="search_product.php"
               class="dashboard-link">
                Search Products
            </a>
        </div>

    </div>

    <div class="account-section">

        <h3>Account</h3>


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
            $_SESSION["csrf_token"]
        ) ?>"
    >

    <button type="submit" class="logout-button">
        Logout
    </button>

</form>


    </div>

</div>

</body>
</html>
