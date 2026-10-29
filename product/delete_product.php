
<?php

// Connect to the database
require_once "auth.php";
require_once "../config/database.php";

$message = "";

// Check if a valid Product ID is provided
$productID = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT,
    ["options" => ["min_range" => 1]]
);

if (!$productID) {
    die("Product not found.");
}

// Get the selected product information
$sql = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productID);
$stmt->execute();

$result = $stmt->get_result();

// Check if the product exists
if ($result->num_rows == 0) {
    die("Product not found.");
}

$row = $result->fetch_assoc();
$stmt->close();

// Delete the product after confirmation
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Validate CSRF token
    $submittedToken = $_POST["csrf_token"] ?? "";
    $sessionToken = $_SESSION["csrf_token"] ?? "";

    if (
        $sessionToken === "" ||
        !is_string($submittedToken) ||
        !hash_equals($sessionToken, $submittedToken)
    ) {
        $message = "Invalid request. Please try again.";

    } else {

        // Prepare SQL statement to delete the product
        $sql = "DELETE FROM products WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $productID);

        if ($stmt->execute() && $stmt->affected_rows > 0) {

            $stmt->close();

            // Redirect after successful deletion
            header("Location: view_product.php?deleted=1");
            exit();

        } else {
            $message = "Failed to delete product.";
        }

        $stmt->close();
    }
}

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

        <h3 class="warning-title">
            Are you sure you want to delete this product?
        </h3>

        <p class="warning-text">
            This action will permanently remove the product
            from your inventory and cannot be undone.
        </p>

        <?php if ($message != ""): ?>
            <p class="error-message">
                <strong><?= htmlspecialchars($message) ?></strong>
            </p>
        <?php endif; ?>

        <table class="product-info">

            <tr>
                <th>Product ID</th>
                <td>
                    <?= htmlspecialchars(
                        (string) $row["product_id"]
                    ) ?>
                </td>
            </tr>

            <tr>
                <th>Product Name</th>
                <td>
                    <?= htmlspecialchars($row["name"]) ?>
                </td>
            </tr>

            <tr>
                <th>Description</th>
                <td>
                    <?= htmlspecialchars($row["description"]) ?>
                </td>
            </tr>

            <tr>
                <th>Price (RM)</th>
                <td>
                    RM <?= number_format($row["price"], 2) ?>
                </td>
            </tr>

            <tr>
                <th>Expiry Date</th>
                <td>
                    <?= htmlspecialchars($row["expiry_date"]) ?>
                </td>
            </tr>

            <tr>
                <th>Quantity</th>
                <td>
                    <?= htmlspecialchars(
                        (string) $row["quantity"]
                    ) ?>
                </td>
            </tr>

        </table>

        <form method="POST" class="delete-form">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(
                    $_SESSION["csrf_token"]
                ) ?>"
            >

            <button type="submit" class="delete-button">
                Yes, Delete Product
            </button>

            <a href="view_product.php" class="cancel-button">
                Cancel
            </a>

        </form>

    </div>

</div>

</body>
</html>
