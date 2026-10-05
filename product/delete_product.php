<?php

require_once "../config/database.php";

// Check if product ID is provided
if (!isset($_GET["id"])) {
    die("Product not found.");
}

$productID = $_GET["id"];

// Get product information
$sql = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productID);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Product not found.");
}

$row = $result->fetch_assoc();

// Delete product after confirmation
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $sql = "DELETE FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productID);

    if ($stmt->execute()) {
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

<p>Are you sure you want to delete the following product?</p>

<table border="1" cellpadding="10">

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

<form method="POST">
    <button type="submit">Yes, Delete Product</button>

    <a href="view_product.php">Cancel</a>
</form>

</body>
</html>