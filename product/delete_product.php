<?php

// Connect to the database
require_once "../config/database.php";

$message = "";

// Check if a Product ID is provided in the URL
if (!isset($_GET["id"])) {
    die("Product not found.");
}

$productID = $_GET["id"];

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

// Delete the product after confirmation
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Prepare SQL statement to delete the selected product
    $sql = "DELETE FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productID);

    // Execute the DELETE statement
    if ($stmt->execute()) {

        // Redirect to Product List after successful deletion
        header("Location: view_product.php?deleted=1");
        exit();

    } else {

        $message = "Failed to delete product.";
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Delete Product</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<h2>Delete Product</h2>

<!-- Display error message if deletion fails -->
<?php if ($message != ""): ?>

    <p class="error-message">
        <strong><?php echo $message; ?></strong>
    </p>

<?php endif; ?>

<p>Are you sure you want to delete the following product?</p>

<!-- Display complete product information before deletion -->
<table>

    <tr>
        <th>Product ID</th>
        <td><?php echo $row["product_id"]; ?></td>
    </tr>

    <tr>
        <th>Product Name</th>
        <td><?php echo htmlspecialchars($row["name"]); ?></td>
    </tr>

    <tr>
        <th>Description</th>
        <td><?php echo htmlspecialchars($row["description"]); ?></td>
    </tr>

    <tr>
        <th>Price (RM)</th>
        <td><?php echo number_format($row["price"], 2); ?></td>
    </tr>

    <tr>
        <th>Expiry Date</th>
        <td><?php echo $row["expiry_date"]; ?></td>
    </tr>

    <tr>
        <th>Quantity</th>
        <td><?php echo $row["quantity"]; ?></td>
    </tr>

</table>

<br>

<!-- Delete confirmation form -->
<form method="POST">

    <button type="submit">Yes, Delete Product</button>

    <a href="view_product.php">Cancel</a>

</form>

</body>
</html>