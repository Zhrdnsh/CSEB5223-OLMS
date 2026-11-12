
<?php
// ============================================
// OLMS Customer Product Details
// Display information for a selected product
// ============================================

// Default values
$product = null;
$errorMessage = "";

// Validate product ID
$productID = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT,
    ["options" => ["min_range" => 1]]
);

if ($productID === false || $productID === null) {

    $errorMessage = "The product ID is invalid.";

} else {

    // Connect to database
    require_once "../config/database.php";

    try {

        // Retrieve selected product securely
        $sql = "SELECT product_id,
                       name,
                       description,
                       price,
                       expiry_date,
                       quantity
                FROM products
                WHERE product_id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("i", $productID);
        $stmt->execute();

        $result = $stmt->get_result();

        $product = $result->fetch_assoc();

        // Release database resources
        $result->free();
        $stmt->close();
        $conn->close();

        // Handle nonexistent product
        if (!$product) {
            $errorMessage = "The selected product does not exist.";
        }

    } catch (mysqli_sql_exception $e) {

        // Log technical error privately
        error_log(
            "Product Details Error: " . $e->getMessage()
        );

        $errorMessage = "Unable to retrieve product details. Please try again later.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Product Details - OLMS</title>

    <link rel="stylesheet" href="../css/style.css">

    <style>
        /* General Styling */
        body {
            background: #fffaf5;
            color: #554b47;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 30px;
        }

        /* Navigation */
        nav {
            max-width: 1100px;
            margin: 0 auto 35px;
            padding: 18px 25px;
            background: #ffffff;
            border-radius: 12px;
        }

        nav a {
            color: #a56b7b;
            font-weight: bold;
            margin-right: 15px;
            text-decoration: none;
        }

        nav a:hover {
            color: #76515d;
        }

        /* Product Details Container */
        .details-container {
            max-width: 750px;
            margin: 40px auto;
            padding: 45px;
            border: 1px solid #f0e1e4;
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 8px 25px rgba(100, 70, 80, 0.06);
        }

        .details-container h1 {
            color: #76515d;
            font-size: 32px;
            margin: 0 0 15px;
        }

        .details-container h3 {
            color: #76515d;
            font-size: 18px;
            margin-top: 30px;
        }

        .details-container p {
            font-size: 15px;
            line-height: 1.8;
            color: #665b57;
        }

        .product-price {
            display: inline-block;
            font-size: 28px !important;
            font-weight: bold;
            color: #ad687b !important;
            background: #fbe6ea;
            padding: 12px 20px;
            border-radius: 12px;
            margin: 10px 0;
        }

        .details-container strong {
            color: #76515d;
        }

        /* Product Status */
        .status {
            font-weight: bold;
        }

        .status.in-stock {
            color: #718b68;
        }

        .status.out-of-stock,
        .status.expired {
            color: #a04e65;
        }

        /* Error Message */
        .error-message {
            background: #fbe6ea;
            color: #a04e65 !important;
            border: 1px solid #f2cbd5;
            border-radius: 10px;
            padding: 15px 20px;
        }

        /* Back Button */
        .back-button {
            display: inline-block;
            margin-top: 30px;
            padding: 13px 25px;
            background: #a5b99c;
            color: #ffffff;
            font-weight: bold;
            text-decoration: none;
            border-radius: 10px;
            transition: background 0.2s ease;
        }

        .back-button:hover {
            background: #829a78;
        }

        /* Responsive Design */
        @media (max-width: 600px) {

            body {
                padding: 15px;
            }

            .details-container {
                padding: 25px;
                margin: 20px auto;
            }

            .details-container h1 {
                font-size: 25px;
            }

            nav {
                padding: 15px;
                line-height: 2;
            }
        }
    </style>
</head>

<body>

<!-- Navigation -->
<nav>
    <a href="../index.php">Home</a> |
    <a href="customer_browse.php">Browse Products</a> |
    <a href="../admin/admin_login.php">Admin Login</a>
</nav>

<div class="details-container">

    <?php if ($product): ?>

        <!-- Product Name -->
        <h1>
            <?= htmlspecialchars(
                $product["name"],
                ENT_QUOTES,
                "UTF-8"
            ) ?>
        </h1>

        <!-- Product Price -->
        <p class="product-price">
            RM <?= number_format(
                (float) $product["price"],
                2
            ) ?>
        </p>

        <!-- Product Description -->
        <h3>Product Description</h3>

        <p>
            <?= nl2br(
                htmlspecialchars(
                    $product["description"],
                    ENT_QUOTES,
                    "UTF-8"
                )
            ) ?>
        </p>

        <!-- Product ID -->
        <p>
            <strong>Product ID:</strong>
            <?= (int) $product["product_id"] ?>
        </p>

        <!-- Expiry Date -->
        <p>
            <strong>Expiry Date:</strong>
            <?= htmlspecialchars(
                $product["expiry_date"],
                ENT_QUOTES,
                "UTF-8"
            ) ?>
        </p>

        <!-- Quantity -->
        <p>
            <strong>Quantity Available:</strong>
            <?= (int) $product["quantity"] ?>
        </p>

        <!-- Product Availability -->
        <?php
        $quantity = (int) $product["quantity"];
        $expiryDate = $product["expiry_date"];
        $today = date("Y-m-d");

        if ($expiryDate < $today) {

            $status = "Expired";
            $statusClass = "expired";

        } elseif ($quantity <= 0) {

            $status = "Out of stock";
            $statusClass = "out-of-stock";

        } else {

            $status = "In stock";
            $statusClass = "in-stock";
        }
        ?>

        <p>
            <strong>Status:</strong>

            <span class="status <?= $statusClass ?>">
                <?= htmlspecialchars(
                    $status,
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>
            </span>
        </p>

    <?php else: ?>

        <!-- Product Not Found / Error -->
        <h1>Product Details Unavailable</h1>

        <p class="error-message">
            <?= htmlspecialchars(
                $errorMessage,
                ENT_QUOTES,
                "UTF-8"
            ) ?>
        </p>

    <?php endif; ?>

    <!-- Back Button -->
    <a class="back-button" href="customer_browse.php">
        Back to Products
    </a>

</div>

</body>
</html>
