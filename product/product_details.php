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
    body {
        background: #fffaf5;
        color: #554b47;
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 30px;
    }

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
        margin-top: 0;
        margin-bottom: 15px;
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


<nav>
    <a href="../index.php">Home</a>
    |
    <a href="customer_browse.php">Browse Products</a>
    |
    <a href="admin_login.php">Admin Login</a>
</nav>


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