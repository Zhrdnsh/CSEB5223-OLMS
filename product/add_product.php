<?php

// Connect to the database and include the Product class
require_once "auth.php";
require_once "../config/database.php";
require_once "../classes/Product.php";

$message = "";

// Check if the form has been submitted
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

        // Set product information
        $product->setName($name);
        $product->setDescription($description);
        $product->setPrice($price);
        $product->setExpiryDate($expiryDate);
        $product->setQuantity($quantity);

        // Get product information from the Product object
        $name = $product->getName();
        $description = $product->getDescription();
        $price = $product->getPrice();
        $expiryDate = $product->getExpiryDate();
        $quantity = $product->getQuantity();

        // Prepare SQL statement to insert the product
        $sql = "INSERT INTO products
                (name, description, price, expiry_date, quantity)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        // Bind product values to the SQL statement
        $stmt->bind_param(
            "ssdsi",
            $name,
            $description,
            $price,
            $expiryDate,
            $quantity
        );

        // Execute the INSERT statement
        if ($stmt->execute()) {
            $message = "Product added successfully!";
        } else {
            $message = "Failed to add product.";
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Product</title>
    <link rel="stylesheet" href="../css/style.css">


<style>
    body {
        background: #fffaf5;
        font-family: Arial, sans-serif;
        color: #554b47;
        margin: 0;
        padding: 30px;
    }

    .add-container {
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

    .add-container nav {
        background: #ffffff;
        padding: 18px 22px;
        border-radius: 12px;
        margin-bottom: 25px;
    }

    .add-container nav a {
        color: #a56b7b;
        font-weight: bold;
        margin-right: 18px;
    }

    .add-container nav a:hover {
        color: #76515d;
    }

    .add-form {
        background: #ffffff;
        padding: 35px;
        max-width: none;
        border-radius: 18px;
        border: 1px solid #f0e1e4;
        box-shadow: 0 5px 18px rgba(100, 70, 80, 0.06);
    }

    .add-form label {
        display: block;
        color: #76515d;
        font-weight: bold;
        font-size: 14px;
        margin-bottom: 8px;
    }

    .add-form input,
    .add-form textarea {
        width: 100%;
        padding: 13px;
        margin: 0 0 22px;
        border: 1px solid #e8d7d9;
        border-radius: 10px;
        background: #fffdfb;
        font-size: 15px;
        font-family: Arial, sans-serif;
    }

    .add-form textarea {
        min-height: 110px;
        resize: vertical;
    }

    .add-form input:focus,
    .add-form textarea:focus {
        outline: 2px solid #d7b0bb;
        border-color: #ad687b;
    }

    .add-button {
        background: #a5b99c;
        color: #ffffff;
        padding: 14px 28px;
        border: none;
        border-radius: 10px;
        font-size: 15px;
        font-weight: bold;
        cursor: pointer;
    }

    .add-button:hover {
        background: #829a78;
    }

    .add-container .success-message,
    .add-container .error-message {
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

        .add-form {
            padding: 25px 20px;
        }

        .add-container nav {
            line-height: 2;
        }

        .add-button {
            width: 100%;
        }
    }
</style>

</head>


<body>

<div class="add-container">

    <div class="page-header">
        <h2>Add New Product ♡</h2>
        <p>Add a new item to your local mart inventory.</p>
    </div>

    <nav>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="view_product.php">View Products</a>
        <a href="search_product.php">Search Products</a>
    </nav>

    <?php if ($message != ""): ?>

        <?php if ($message == "Product added successfully!"): ?>
            <p class="success-message">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php else: ?>
            <p class="error-message">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>

    <?php endif; ?>

    <form method="POST" class="add-form">

        <label for="name">Product Name</label>
        <input
            type="text"
            id="name"
            name="name"
            placeholder="Enter product name"
            required
        >

        <label for="description">Description</label>
        <textarea
            id="description"
            name="description"
            placeholder="Enter product description"
            required
        ></textarea>

        <label for="price">Price (RM)</label>
        <input
            type="number"
            id="price"
            name="price"
            step="0.01"
            min="0.01"
            placeholder="0.00"
            required
        >

        <label for="expiry_date">Expiry Date</label>
        <input
            type="date"
            id="expiry_date"
            name="expiry_date"
            min="<?= date('Y-m-d') ?>"
            required
        >

        <label for="quantity">Quantity</label>
        <input
            type="number"
            id="quantity"
            name="quantity"
            min="0"
            placeholder="Enter available quantity"
            required
        >

        <button type="submit" class="add-button">
            Add Product
        </button>

    </form>

</div>

</body>

</html>
