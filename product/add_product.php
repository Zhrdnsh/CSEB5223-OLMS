<?php

// Connect to the database and include the Product class
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
    } elseif (!is_numeric($price) || $price < 0) {

        $message = "Price must be a valid positive value.";

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
</head>

<body>

<h2>Add Product</h2>

<!-- Display success or error message -->
<?php if ($message != ""): ?>

    <?php if ($message == "Product added successfully!"): ?>

        <p class="success-message">
            <?php echo $message; ?>
        </p>

    <?php else: ?>

        <p class="error-message">
            <?php echo $message; ?>
        </p>

    <?php endif; ?>

<?php endif; ?>

<!-- Product Management navigation -->
<nav>
    <a href="view_product.php">View Products</a>
    <a href="search_product.php">Search Product</a>
</nav>

<!-- Add Product form -->
<form method="POST">

    <label>Product Name:</label><br>
    <input type="text" name="name" required>
    <br><br>

    <label>Description:</label><br>
    <textarea name="description" required></textarea>
    <br><br>

    <label>Price (RM):</label><br>
    <input
        type="number"
        name="price"
        step="0.01"
        min="0"
        required
    >
    <br><br>

    <label>Expiry Date:</label><br>
    <input
        type="date"
        name="expiry_date"
        min="<?php echo date('Y-m-d'); ?>"
        required
    >
    <br><br>

    <label>Quantity:</label><br>
    <input
        type="number"
        name="quantity"
        min="0"
        required
    >
    <br><br>

    <button type="submit">Add Product</button>

</form>

</body>
</html>