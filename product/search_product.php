
<?php
// ============================================
// OLMS Search Products
// Admin product search by name or exact ID
// ============================================

// Require administrator authentication
require_once "../admin/auth.php";

// Connect to database
require_once "../config/database.php";

// Initialize variables
$search = "";
$products = [];
$searched = false;
$errorMessage = "";

// Check whether a search was submitted
if (isset($_GET["search"])) {

    $searched = true;
    $searchInput = $_GET["search"];

    // Validate search parameter type
    if (!is_string($searchInput)) {

        $errorMessage = "Invalid search parameter.";

    } else {

        $search = trim($searchInput);

        // Validate search input
        if ($search === "") {

            $errorMessage = "Please enter a product name or Product ID.";

        } elseif (strlen($search) > 100) {

            $errorMessage = "Search keyword must not exceed 100 characters.";

        } else {

            try {

                // Search by partial product name or exact Product ID
                $sql = "SELECT product_id,
                               name,
                               description,
                               price,
                               expiry_date,
                               quantity
                        FROM products
                        WHERE name LIKE ?
                           OR CAST(product_id AS CHAR) = ?
                        ORDER BY product_id ASC";

                $stmt = $conn->prepare($sql);

                // Allow partial product name matching
                $searchName = "%" . $search . "%";

                $stmt->bind_param(
                    "ss",
                    $searchName,
                    $search
                );

                $stmt->execute();

                $result = $stmt->get_result();

                // Store matching products
                while ($row = $result->fetch_assoc()) {
                    $products[] = $row;
                }

                // Release database resources
                $result->free();
                $stmt->close();

            } catch (mysqli_sql_exception $e) {

                // Log technical error privately
                error_log(
                    "Search Product Error: " . $e->getMessage()
                );

                $errorMessage = "Unable to search products. Please try again later.";
            }
        }
    }
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Search Products - OLMS</title>

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

        /* Navigation */
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

        /* Search Form */
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
            color: #ffffff;
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

        /* Search Results */
        .results-title {
            color: #76515d;
            font-size: 23px;
            margin-bottom: 20px;
        }

        .table-wrapper {
            background: #ffffff;
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

        /* Error Message */
        .search-container .error-message {
            background: #fbe6ea;
            color: #a04e65;
            padding: 18px 22px;
            border-radius: 12px;
            margin-bottom: 25px;
        }

        /* Back Link */
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

        /* Responsive Design */
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

    <!-- Page Header -->
    <div class="page-header">
        <h2>Search Products ♡</h2>
        <p>Find products in your local mart inventory.</p>
    </div>

    <!-- Navigation -->
    <nav>
        <a href="../admin/admin_dashboard.php">Dashboard</a>
        <a href="add_product.php">Add Product</a>
        <a href="view_product.php">View Products</a>
    </nav>

    <!-- Search Form -->
    <form method="GET" action="search_product.php" class="search-form">

        <label for="search">
            Enter Product Name or Product ID
        </label>

        <div class="search-input-row">

            <input
                type="text"
                id="search"
                name="search"
                placeholder="Search by product name or ID..."
                maxlength="100"
                value="<?= htmlspecialchars(
                    $search,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                required
            >

            <button type="submit" class="search-button">
                Search
            </button>

        </div>

    </form>

    <!-- Error Message -->
    <?php if ($errorMessage !== ""): ?>

        <p class="error-message">
            <strong>
                <?= htmlspecialchars(
                    $errorMessage,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </strong>
        </p>

    <?php elseif ($searched): ?>

        <?php if (!empty($products)): ?>

            <!-- Results Heading -->
            <h3 class="results-title">
                Search Results ♡
            </h3>

            <!-- Results Table -->
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

                        <?php foreach ($products as $row): ?>

                            <tr>

                                <!-- Product ID -->
                                <td>
                                    <?= (int) $row["product_id"] ?>
                                </td>

                                <!-- Product Name -->
                                <td class="product-name">
                                    <?= htmlspecialchars(
                                        $row["name"],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </td>

                                <!-- Description -->
                                <td>
                                    <?= htmlspecialchars(
                                        $row["description"],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </td>

                                <!-- Price -->
                                <td class="product-price">
                                    RM <?= number_format(
                                        (float) $row["price"],
                                        2
                                    ) ?>
                                </td>

                                <!-- Expiry Date -->
                                <td>
                                    <?= htmlspecialchars(
                                        $row["expiry_date"],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </td>

                                <!-- Quantity -->
                                <td>
                                    <?= (int) $row["quantity"] ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <!-- No Results -->
            <p class="error-message">
                <strong>No matching products found.</strong>
            </p>

        <?php endif; ?>

    <?php endif; ?>

    <!-- Back to Product List -->
    <a href="view_product.php" class="view-all-link">
        ← View All Products
    </a>

</div>

</body>
</html>
