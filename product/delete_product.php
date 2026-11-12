
<?php
// ============================================
// OLMS Delete Product
// Admin authentication and secure deletion
// ============================================

// Require administrator authentication
require_once "../admin/auth.php";

// Connect to database
require_once "../config/database.php";

// Default values
$message = "";
$row = null;

// Ensure CSRF token exists
if (
    !isset($_SESSION["csrf_token"]) ||
    !is_string($_SESSION["csrf_token"])
) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// Validate product ID
$productID = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT,
    ["options" => ["min_range" => 1]]
);

if ($productID === false || $productID === null) {

    http_response_code(400);
    $message = "Invalid product ID.";

} else {

    try {

        // Retrieve product details
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
        $row = $result->fetch_assoc();

        $result->free();
        $stmt->close();

        // Check if product exists
        if (!$row) {

            http_response_code(404);
            $message = "Product not found.";

        } elseif ($_SERVER["REQUEST_METHOD"] === "POST") {

            // Validate CSRF token
            $submittedToken = $_POST["csrf_token"] ?? "";
            $sessionToken = $_SESSION["csrf_token"];

            if (
                !is_string($submittedToken) ||
                !hash_equals($sessionToken, $submittedToken)
            ) {

                http_response_code(403);
                $message = "Invalid request. Please try again.";

            } else {

                // Delete product using prepared statement
                $deleteSql = "DELETE FROM products
                              WHERE product_id = ?";

                $deleteStmt = $conn->prepare($deleteSql);
                $deleteStmt->bind_param("i", $productID);
                $deleteStmt->execute();

                $deletedRows = $deleteStmt->affected_rows;
                $deleteStmt->close();

                if ($deletedRows > 0) {

                    // Redirect after successful deletion
                    header("Location: view_product.php?deleted=1");
                    exit();

                } else {

                    $message = "Product could not be deleted.";
                }
            }
        }

    } catch (mysqli_sql_exception $e) {

        // Log technical errors privately
        error_log(
            "Delete Product Error: " . $e->getMessage()
        );

        http_response_code(500);
        $message = "Unable to process the request. Please try again later.";

        // Prevent showing an actionable delete form
        $row = null;
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

    <title>Delete Product - OLMS</title>

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

        .delete-container {
            max-width: 800px;
            margin: 40px auto;
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

        .delete-card {
            background: #ffffff;
            padding: 35px;
            border-radius: 18px;
            border: 1px solid #f0e1e4;
            box-shadow: 0 5px 18px rgba(100, 70, 80, 0.06);
        }

        .warning-title {
            color: #a04e65;
            font-size: 21px;
            margin-top: 0;
            margin-bottom: 10px;
        }

        .warning-text {
            color: #806d70;
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 25px;
        }

        .product-info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .product-info th,
        .product-info td {
            padding: 15px;
            border-bottom: 1px solid #f3e8e8;
            text-align: left;
            font-size: 14px;
            line-height: 1.6;
        }

        .product-info th {
            background: #fff5f7;
            color: #76515d;
            width: 35%;
            font-weight: bold;
        }

        .product-info td {
            color: #554b47;
            overflow-wrap: anywhere;
        }

        .delete-form {
            background: transparent;
            padding: 0;
            max-width: none;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .delete-button {
            background: #c77b8e;
            color: #ffffff;
            padding: 14px 25px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        .delete-button:hover {
            background: #a95870;
        }

        .cancel-button {
            display: inline-block;
            background: #a5b99c;
            color: #ffffff;
            padding: 14px 25px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: bold;
            text-decoration: none;
        }

        .cancel-button:hover {
            background: #829a78;
        }

        .delete-container .error-message {
            background: #fbe6ea;
            color: #a04e65;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
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

            .delete-card {
                padding: 25px 20px;
            }

            .product-info th,
            .product-info td {
                padding: 12px 8px;
                font-size: 13px;
            }

            .delete-form {
                flex-direction: column;
                align-items: stretch;
            }

            .delete-button,
            .cancel-button {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>

<body>

<div class="delete-container">

    <div class="page-header">
        <h2>Delete Product ♡</h2>
        <p>Review the product details before deleting.</p>
    </div>

    <div class="delete-card">

        <?php if ($row): ?>

            <h3 class="warning-title">
                Are you sure you want to delete this product?
            </h3>

            <p class="warning-text">
                This action will permanently remove the product
                from your inventory and cannot be undone.
            </p>

        <?php else: ?>

            <h3 class="warning-title">
                Product Unavailable
            </h3>

        <?php endif; ?>

        <!-- Error Message -->
        <?php if ($message !== ""): ?>

            <p class="error-message">
                <strong>
                    <?= htmlspecialchars(
                        $message,
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </strong>
            </p>

        <?php endif; ?>

        <?php if ($row): ?>

            <!-- Product Information -->
            <table class="product-info">

                <tr>
                    <th>Product ID</th>
                    <td>
                        <?= (int) $row["product_id"] ?>
                    </td>
                </tr>

                <tr>
                    <th>Product Name</th>
                    <td>
                        <?= htmlspecialchars(
                            $row["name"],
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>
                    </td>
                </tr>

                <tr>
                    <th>Description</th>
                    <td>
                        <?= htmlspecialchars(
                            $row["description"],
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>
                    </td>
                </tr>

                <tr>
                    <th>Price (RM)</th>
                    <td>
                        RM <?= number_format(
                            (float) $row["price"],
                            2
                        ) ?>
                    </td>
                </tr>

                <tr>
                    <th>Expiry Date</th>
                    <td>
                        <?= htmlspecialchars(
                            $row["expiry_date"],
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>
                    </td>
                </tr>

                <tr>
                    <th>Quantity</th>
                    <td>
                        <?= (int) $row["quantity"] ?>
                    </td>
                </tr>

            </table>

            <!-- Delete Confirmation Form -->
            <form method="POST" class="delete-form">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars(
                        $_SESSION["csrf_token"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>"
                >

                <button type="submit" class="delete-button">
                    Yes, Delete Product
                </button>

                <a href="view_product.php" class="cancel-button">
                    Cancel
                </a>

            </form>

        <?php else: ?>

            <a href="view_product.php" class="cancel-button">
                Back to Products
            </a>

        <?php endif; ?>

    </div>

</div>

</body>
</html>
