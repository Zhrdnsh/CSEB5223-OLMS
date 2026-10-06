<?php

// Connect to the database and include the Product class
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
    } elseif (!is_numeric($price) || $price < 0) {

        $message = "Price must be a valid positive value.";

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
</head>

<body>

<h2>Edit Product</h2>

<!-- Display success or error message -->
<?php if ($message != ""): ?>

    <?php if ($message == "Product updated successfully!"): ?>

        <p class="success-message">
            <?php echo $message; ?>
        </p>

    <?php else: ?>

        <p class="error-message">
            <?php echo $message; ?>
        </p>

    <?php endif; ?>

<?php endif; ?>

<!-- Edit Product form -->
<form method="POST">

    <label>Product ID:</label><br>
    <input
        type="text"
        value="<?php echo $row["product_id"]; ?>"
        disabled
    >
    <br><br>

    <label>Product Name:</label><br>
    <input
        type="text"
        name="name"
        value="<?php echo htmlspecialchars($row["name"]); ?>"
        required
    >
    <br><br>

    <label>Description:</label><br>
    <textarea
        name="description"
        required
    ><?php echo htmlspecialchars($row["description"]); ?></textarea>
    <br><br>

    <label>Price (RM):</label><br>
    <input
        type="number"
        name="price"
        step="0.01"
        min="0"
        value="<?php echo $row["price"]; ?>"
        required
    >
    <br><br>

    <label>Expiry Date:</label><br>
    <input
        type="date"
        name="expiry_date"
        min="<?php echo date('Y-m-d'); ?>"
        value="<?php echo $row["expiry_date"]; ?>"
        required
    >
    <br><br>

    <label>Quantity:</label><br>
    <input
        type="number"
        name="quantity"
        min="0"
        value="<?php echo $row["quantity"]; ?>"
        required
    >
    <br><br>

    <button type="submit">Update Product</button>

</form>

<br>

<a href="view_product.php">Back to Product List</a>

</body>
</html>