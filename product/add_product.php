<?php

require_once "../config/database.php";
require_once "../classes/Product.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

$name = trim($_POST["name"]);
$description = trim($_POST["description"]);
$price = $_POST["price"];
$expiryDate = $_POST["expiry_date"];
$quantity = $_POST["quantity"];

if ($name == "" || $description == "" || $expiryDate == "") {

    $message = "Please fill in all fields.";

} elseif (!is_numeric($price) || $price < 0) {

    $message = "Price must be a valid positive value.";

} elseif (!is_numeric($quantity) || $quantity < 0) {

    $message = "Quantity cannot be negative.";

} else {

    $product = new Product();

    $product->setName($name);
$product->setDescription($description);
$product->setPrice($price);
$product->setExpiryDate($expiryDate);
$product->setQuantity($quantity);

    $name = $product->getName();
    $description = $product->getDescription();
    $price = $product->getPrice();
    $expiryDate = $product->getExpiryDate();
    $quantity = $product->getQuantity();

    $sql = "INSERT INTO products (name, description, price, expiry_date, quantity)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsi", $name, $description, $price, $expiryDate, $quantity);

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

<nav>
    <a href="view_product.php">View Products</a> 
    <a href="search_product.php">Search Product</a>
</nav>

<form method="POST">

    <label>Product Name:</label><br>
    <input type="text" name="name" required>
    <br><br>

    <label>Description:</label><br>
    <textarea name="description" required></textarea>
    <br><br>

    <label>Price (RM):</label><br>
    <input type="number" name="price" step="0.01" min="0" required>
    <br><br>

    <label>Expiry Date:</label><br>
    <input type="date" name="expiry_date" required>
    <br><br>

    <label>Quantity:</label><br>
<input type="number" name="quantity" min="0" required>
<br><br>

    <button type="submit">Add Product</button>
    <br>


</form>


</body>
</html>