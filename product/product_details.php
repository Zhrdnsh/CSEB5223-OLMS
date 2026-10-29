<?php
require_once "../config/database.php";

// Get the selected product ID
$productID = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
$product = null;

// Retrieve the selected product safely
if ($productID !== false && $productID !== null && $productID > 0) {
    $sql = "SELECT * FROM products WHERE product_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productID);
    $stmt->execute();

    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    $stmt->close();
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
        .details-container {
            max-width: 700px;
            margin: 40px auto;
            padding: 25px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #fff;
        }

        .product-price {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
        }

        .back-button {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 15px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
</head>

<body>

<div class="details-container">

    <?php if ($product): ?>

        <h1>
            <?php echo htmlspecialchars($product["name"]); ?>
        </h1>

        <p class="product-price">
            RM <?php echo number_format((float)$product["price"], 2); ?>
        </p>

        <h3>Product Description</h3>
        <p>
            <?php
            echo nl2br(htmlspecialchars($product["description"]));
            ?>
        </p>

        <p>
            <strong>Product ID:</strong>
            <?php echo (int)$product["product_id"]; ?>
        </p>

        <p>
            <strong>Expiry Date:</strong>
            <?php echo htmlspecialchars($product["expiry_date"]); ?>
        </p>

        <p>
            <strong>Quantity Available:</strong>
            <?php echo (int)$product["quantity"]; ?>
        </p>

        <p>
            <strong>Status:</strong>
            <?php
            if ((int)$product["quantity"] <= 0) {
                echo "Out of stock";
            } elseif ($product["expiry_date"] < date("Y-m-d")) {
                echo "Expired";
            } else {
                echo "In stock";
            }
            ?>
        </p>

    <?php else: ?>

        <h1>Product Not Found</h1>
        <p>The product does not exist or the product ID is invalid.</p>

    <?php endif; ?>

    <a class="back-button" href="customer_browse.php">
        Back to Products
    </a>

</div>

</body>
</html>