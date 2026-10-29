<?php

// Connect to the database and include the Product class
require_once "auth.php";
require_once "../config/database.php";
require_once "../classes/Product.php";

$message = "";

// Check if a Product ID is provided in the URL
if (!isset($_GET["id"])) {
    die("Product not found.");
}

$productID = $_GET["id"];

// Get the existing product data
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

// Check if the Update Product form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get and clean input values from the form
    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $price = $_POST["price"];
    $expiryDate = $_POST["expiry_date"];
    $quantity = $_POST["quantity"];

    // Validate required fields
    if ($name == "" || $description == "" || $expiryDate == "") {

        $message = "Please fill in all fields.";

    // Prevent past expiry dates
    } elseif ($expiryDate < date("Y-m-d")) {

        $message = "Expiry date cannot be in the past.";

    // Validate product price
} elseif (!is_numeric($price) || $price <= 0) {

    $message = "Price must be greater than RM0.00.";

    // Validate product quantity
    } elseif (!is_numeric($quantity) || $quantity < 0) {

        $message = "Quantity cannot be negative.";

    } else {

        // Create a Product object
        $product = new Product();

        // Set the updated product information
        $product->setProductID($productID);
        $product->setName($name);
        $product->setDescription($description);
        $product->setPrice($price);
        $product->setExpiryDate($expiryDate);
        $product->setQuantity($quantity);

        // Get the updated information from the Product object
        $name = $product->getName();
        $description = $product->getDescription();
        $price = $product->getPrice();
        $expiryDate = $product->getExpiryDate();
        $quantity = $product->getQuantity();

        // Prepare SQL statement to update the product
        $sql = "UPDATE products
                SET name = ?,
                    description = ?,
                    price = ?,
                    expiry_date = ?,
                    quantity = ?
                WHERE product_id = ?";

        $stmt = $conn->prepare($sql);

        // Bind updated product values to the SQL statement
        $stmt->bind_param(
            "ssdsii",
            $name,
            $description,
            $price,
            $expiryDate,
            $quantity,
            $productID
        );

        // Execute the UPDATE statement
        if ($stmt->execute()) {

            $message = "Product updated successfully!";

            // Get the updated product data to display in the form
            $sql = "SELECT * FROM products WHERE product_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $productID);
            $stmt->execute();

            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

        } else {

            $message = "Failed to update product.";
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Product</title>
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

    .edit-container {
        max-width: 800px;
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

    .edit-nav {
        background: #ffffff;
        padding: 18px 22px;
        border-radius: 12px;
        margin-bottom: 25px;
    }

    .edit-nav a {
        color: #a56b7b;
        font-weight: bold;
        margin-right: 18px;
        text-decoration: none;
    }

    .edit-nav a:hover {
        color: #76515d;
    }

    .edit-form {
        background: #ffffff;
        padding: 35px;
        max-width: none;
        border-radius: 18px;
        border: 1px solid #f0e1e4;
        box-shadow: 0 5px 18px rgba(100, 70, 80, 0.06);
    }

    .edit-form label {
        display: block;
        color: #76515d;
        font-weight: bold;
        font-size: 14px;
        margin-bottom: 8px;
    }

    .edit-form input,
    .edit-form textarea {
        width: 100%;
        padding: 13px;
        margin: 0 0 22px;
        border: 1px solid #e8d7d9;
        border-radius: 10px;
        background: #fffdfb;
        font-size: 15px;
        font-family: Arial, sans-serif;
    }

    .edit-form input:disabled {
        background: #f6f1ef;
        color: #927e81;
        cursor: not-allowed;
    }

    .edit-form textarea {
        min-height: 110px;
        resize: vertical;
    }

    .edit-form input:focus,
    .edit-form textarea:focus {
        outline: 2px solid #d7b0bb;
        border-color: #ad687b;
    }

    .update-button {
        background: #a5b99c;
        color: #ffffff;
        padding: 14px 28px;
        border: none;
        border-radius: 10px;
        font-size: 15px;
        font-weight: bold;
        cursor: pointer;
    }

    .update-button:hover {
        background: #829a78;
    }

    .edit-container .success-message,
    .edit-container .error-message {
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

        .edit-form {
            padding: 25px 20px;
        }

        .edit-nav {
            line-height: 2;
        }

        .update-button {
            width: 100%;
        }
    }
</style>

</head>


<body>

<div class="edit-container">

    <div class="page-header">
        <h2>Edit Product ♡</h2>
        <p>Update your product information and inventory details.</p>
    </div>

    <nav class="edit-nav">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="view_product.php">View Products</a>
        <a href="search_product.php">Search Products</a>
    </nav>

    <?php if ($message != ""): ?>

        <?php if ($message == "Product updated successfully!"): ?>

            <p class="success-message">
                <?= htmlspecialchars($message) ?>
            </p>

        <?php else: ?>

            <p class="error-message">
                <?= htmlspecialchars($message) ?>
            </p>

        <?php endif; ?>

    <?php endif; ?>

    <form method="POST" class="edit-form">

        <label for="product_id">Product ID</label>
        <input
            type="text"
            id="product_id"
            value="<?= htmlspecialchars(
                (string) $row["product_id"]
            ) ?>"
            disabled
        >

        <label for="name">Product Name</label>
        <input
            type="text"
            id="name"
            name="name"
            value="<?= htmlspecialchars($row["name"]) ?>"
            required
        >

        <label for="description">Description</label>
        <textarea
            id="description"
            name="description"
            required
        ><?= htmlspecialchars($row["description"]) ?></textarea>

        <label for="price">Price (RM)</label>
        <input
            type="number"
            id="price"
            name="price"
            step="0.01"
            min="0.01"
            value="<?= htmlspecialchars(
                (string) $row["price"]
            ) ?>"
            required
        >

        <label for="expiry_date">Expiry Date</label>
        <input
            type="date"
            id="expiry_date"
            name="expiry_date"
            min="<?= date('Y-m-d') ?>"
            value="<?= htmlspecialchars(
                $row["expiry_date"]
            ) ?>"
            required
        >

        <label for="quantity">Quantity</label>
        <input
            type="number"
            id="quantity"
            name="quantity"
            min="0"
            value="<?= htmlspecialchars(
                (string) $row["quantity"]
            ) ?>"
            required
        >

        <button type="submit" class="update-button">
            Update Product
        </button>

    </form>

</div>

</body>

</html>
