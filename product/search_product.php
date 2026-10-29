<?php

// Connect to the database
require_once "auth.php";
require_once "../config/database.php";

// Initialize search variables
$search = "";
$result = null;
$searched = false;

// Check if a search value has been submitted
if (isset($_GET["search"])) {

    // Get and clean the search input
    $search = trim($_GET["search"]);
    $searched = true;

    // Search for a product by partial name or exact Product ID
    $sql = "SELECT * FROM products
            WHERE name LIKE ?
            OR CAST(product_id AS CHAR) = ?";

    $stmt = $conn->prepare($sql);

    // Add wildcards to allow partial product name searches
    $searchName = "%" . $search . "%";

    // Bind search values and execute the query
    $stmt->bind_param("ss", $searchName, $search);
    $stmt->execute();

    $result = $stmt->get_result();
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Search Product</title>
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

    .search-container {
        max-width: 1100px;
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

    .search-container nav {
        background: #ffffff;
        padding: 18px 22px;
        border-radius: 12px;
        margin-bottom: 25px;
    }

    .search-container nav a {
        color: #a56b7b;
        font-weight: bold;
        margin-right: 18px;
        text-decoration: none;
    }

    .search-container nav a:hover {
        color: #76515d;
    }

    .search-form {
        background: #ffffff;
        padding: 30px;
        border: 1px solid #f0e1e4;
        border-radius: 18px;
        box-shadow: 0 5px 18px rgba(100, 70, 80, 0.06);
        max-width: none;
        margin-bottom: 30px;
    }

    .search-form label {
        display: block;
        color: #76515d;
        font-weight: bold;
        font-size: 15px;
        margin-bottom: 15px;
    }

    .search-input-row {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .search-form input {
        flex: 1;
        min-width: 0;
        padding: 14px;
        border: 1px solid #e8d7d9;
        border-radius: 10px;
        background: #fffdfb;
        font-size: 15px;
        margin: 0;
    }

    .search-form input:focus {
        outline: 2px solid #d7b0bb;
        border-color: #ad687b;
    }

    .search-button {
        background: #a5b99c;
        color: white;
        padding: 14px 28px;
        border: none;
        border-radius: 10px;
        font-size: 15px;
        font-weight: bold;
        cursor: pointer;
    }

    .search-button:hover {
        background: #829a78;
    }

    .results-title {
        color: #76515d;
        font-size: 23px;
        margin-bottom: 20px;
    }

    .table-wrapper {
        background: white;
        padding: 15px;
        border: 1px solid #f0e1e4;
        border-radius: 18px;
        box-shadow: 0 5px 18px rgba(100, 70, 80, 0.06);
        overflow-x: auto;
    }

    .result-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }

    .result-table th {
        background: #fbe6ea;
        color: #76515d;
        padding: 16px 14px;
        text-align: left;
        font-size: 14px;
    }

    .result-table td {
        padding: 16px 14px;
        border-bottom: 1px solid #f3e8e8;
        font-size: 14px;
        line-height: 1.6;
    }

    .result-table tr:hover td {
        background: #fffafc;
    }

    .result-table tr:last-child td {
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

    .search-container .error-message {
        background: #fbe6ea;
        color: #a04e65;
        padding: 18px 22px;
        border-radius: 12px;
        margin-bottom: 25px;
    }

    .view-all-link {
        display: inline-block;
        margin-top: 25px;
        color: #ad687b;
        font-weight: bold;
        text-decoration: none;
    }

    .view-all-link:hover {
        color: #76515d;
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

        .search-container nav {
            line-height: 2;
        }

        .search-form {
            padding: 22px;
        }

        .search-input-row {
            flex-direction: column;
            align-items: stretch;
        }

        .search-button {
            width: 100%;
        }

        .result-table {
            min-width: 750px;
        }
    }
</style>

</head>


<body>

<div class="search-container">

    <div class="page-header">
        <h2>Search Products ♡</h2>
        <p>Find products in your local mart inventory.</p>
    </div>

    <nav>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="add_product.php">Add Product</a>
        <a href="view_product.php">View Products</a>
    </nav>

    <form method="GET" class="search-form">

        <label for="search">
            Enter Product Name or Product ID
        </label>

        <div class="search-input-row">

            <input
                type="text"
                id="search"
                name="search"
                placeholder="Search by product name or ID..."
                value="<?= htmlspecialchars($search) ?>"
                required
            >

            <button type="submit" class="search-button">
                Search
            </button>

        </div>

    </form>

    <?php if ($searched): ?>

        <?php if ($result && $result->num_rows > 0): ?>

            <h3 class="results-title">
                Search Results ♡
            </h3>

            <div class="table-wrapper">

                <table class="result-table">

                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Description</th>
                            <th>Price (RM)</th>
                            <th>Expiry Date</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>

                    <tbody>

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

                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <p class="error-message">
                <strong>Product not found.</strong>
            </p>

        <?php endif; ?>

    <?php endif; ?>

    <a href="view_product.php" class="view-all-link">
        ← View All Products
    </a>

</div>

</body>

</html>
