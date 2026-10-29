<?php

// Connect to the database
require_once "auth.php";
require_once "../config/database.php";

// Retrieve all products from the database in ascending Product ID order
$sql = "SELECT * FROM products ORDER BY product_id ASC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Product List</title>
    <link rel="stylesheet" href="../css/style.css">


<style>
    * {
        box-sizing: border-box;
    }

    body {
        background: #fffaf5;
        font-family: Arial, sans-serif;
        color: #554b47;
        margin: 0;
        padding: 30px;
    }

    .product-container {
        max-width: 1200px;
        margin: 30px auto;
    }

    .page-header {
        background: #fbe6ea;
        padding: 30px;
        border-radius: 18px;
        margin-bottom: 25px;
    }

    .page-header h2 {
        color: #76515d;
        font-size: 30px;
        margin: 0 0 10px;
    }

    .page-header p {
        color: #806d70;
        margin: 0;
    }

    .product-container nav {
        background: #ffffff;
        padding: 18px 22px;
        border-radius: 12px;
        margin-bottom: 25px;
    }

    .product-container nav a {
        color: #a56b7b;
        font-weight: bold;
        margin-right: 18px;
        text-decoration: none;
    }

    .product-container nav a:hover {
        color: #76515d;
    }

    .table-wrapper {
        background: #ffffff;
        border: 1px solid #f0e1e4;
        border-radius: 18px;
        box-shadow: 0 5px 18px rgba(100, 70, 80, 0.06);
        overflow-x: auto;
        padding: 15px;
    }

    .product-table {
        width: 100%;
        border-collapse: collapse;
        background: #ffffff;
        margin: 0;
    }

    .product-table th {
        background: #fbe6ea;
        color: #76515d;
        padding: 16px 14px;
        text-align: left;
        font-size: 14px;
        border-bottom: 1px solid #ead5db;
    }

    .product-table td {
        padding: 16px 14px;
        border-bottom: 1px solid #f3e8e8;
        font-size: 14px;
        vertical-align: middle;
        line-height: 1.6;
    }

    .product-table tr:hover td {
        background: #fffafc;
    }

    .product-table tr:last-child td {
        border-bottom: none;
    }

    .product-name {
        color: #76515d;
        font-weight: bold;
    }

    .product-price {
        color: #ad687b;
        font-weight: bold;
        white-space: nowrap;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .product-table .edit-button,
    .product-table .delete-button {
        display: inline-block;
        padding: 9px 14px;
        border-radius: 9px;
        font-size: 13px;
        font-weight: bold;
        text-decoration: none;
        white-space: nowrap;
    }

    .product-table .edit-button {
        background: #a5b99c;
        color: #ffffff;
    }

    .product-table .edit-button:hover {
        background: #829a78;
    }

    .product-table .delete-button {
        background: #fbe6ea;
        color: #a04e65;
    }

    .product-table .delete-button:hover {
        background: #f2cbd5;
    }

    .product-container .success-message {
        padding: 15px 20px;
        background: #e8f2e4;
        color: #527449;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .empty-message {
        text-align: center;
        color: #927e81;
        padding: 30px !important;
    }

    @media (max-width: 600px) {
        body {
            padding: 15px;
        }

        .page-header {
            padding: 25px;
        }

        .page-header h2 {
            font-size: 25px;
        }

        .product-container nav {
            line-height: 2;
        }

        .product-table {
            min-width: 850px;
        }
    }
</style>

</head>


<body>

<div class="product-container">

    <div class="page-header">
        <h2>Product List ♡</h2>
        <p>View and manage your local mart inventory.</p>
    </div>

    <nav>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="add_product.php">Add Product</a>
        <a href="search_product.php">Search Products</a>
    </nav>

    <?php if (isset($_GET["deleted"])): ?>
        <p class="success-message">
            <strong>Product deleted successfully!</strong>
        </p>
    <?php endif; ?>

    <div class="table-wrapper">

        <table class="product-table">

            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Price (RM)</th>
                    <th>Expiry Date</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php if ($result->num_rows > 0): ?>

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars(
                                    (string) $row["product_id"]
                                ) ?>
                            </td>

                            <td class="product-name">
                                <?= htmlspecialchars($row["name"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $row["description"]
                                ) ?>
                            </td>

                            <td class="product-price">
                                RM <?= number_format(
                                    $row["price"], 2
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $row["expiry_date"]
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    (string) $row["quantity"]
                                ) ?>
                            </td>

                            <td>
                                <div class="action-buttons">

                                    <a
                                        class="edit-button"
                                        href="edit_product.php?id=<?= urlencode($row["product_id"]) ?>"
                                    >
                                        Edit
                                    </a>

                                    <a
                                        class="delete-button"
                                        href="delete_product.php?id=<?= urlencode($row["product_id"]) ?>"
                                    >
                                        Delete
                                    </a>

                                </div>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="7" class="empty-message">
                            No products found.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>

</html>
