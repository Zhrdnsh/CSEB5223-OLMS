<?php

require_once "../config/database.php";
require_once "../classes/Product.php";

$message = "";

// Check if product ID is provided
if (!isset($_GET["id"])) {
    die("Product not found.");
}

$productID = $_GET["id"];

// Get existing product data
$sql = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productID);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Product not found.");
}

$row = $result->fetch_assoc();

// Update product
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $product = new Product();

    $product->setProductID($productID);
    $product->setName($_POST["name"]);
    $product->setDescription($_POST["description"]);
    $product->setPrice($_POST["price"]);
    $product->setExpiryDate($_POST["expiry_date"]);
    $product->setQuantity($_POST["quantity"]);

    $name = $product->getName();
    $description = $product->getDescription();
    $price = $product->getPrice();
    $expiryDate = $product->getExpiryDate();
    $quantity = $product->getQuantity();

    $sql = "UPDATE products
            SET name = ?, description = ?, price = ?, expiry_date = ?, quantity = ?
            WHERE product_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssdsii",
        $name,
        $description,
        $price,
        $expiryDate,
        $quantity,
        $productID
    );

    if ($stmt->execute()) {
        $message = "Product updated successfully!";

        // Get updated product data
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

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<h2>Edit Product</h2>

<?php if ($message != ""): ?>
    <p><strong><?php echo $message; ?></strong></p>
<?php endif; ?>

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