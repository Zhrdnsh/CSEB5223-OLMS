<?php

require_once "../config/database.php";

$sql = "SELECT * FROM products ORDER BY product_id ASC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Product List</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<h2>Product List</h2>

<nav>
    <a href="add_product.php">Add Product</a> |
    <a href="search_product.php">Search Product</a>
</nav>

<?php if (isset($_GET["deleted"])): ?>
    <p class="success-message">
        <strong>Product deleted successfully!</strong>
    </p>
<?php endif; ?>

<br><br>

<table border="1" cellpadding="10">
    <tr>
        <th>Product ID</th>
        <th>Product Name</th>
        <th>Description</th>
        <th>Price (RM)</th>
        <th>Expiry Date</th>
        <th>Quantity</th>
        <th>Actions</th>
    </tr>

    <?php if ($result->num_rows > 0): ?>

        <?php while ($row = $result->fetch_assoc()): ?>


            <tr>
                <td><?php echo $row["product_id"]; ?></td>
                <td><?php echo htmlspecialchars($row["name"]); ?></td>
                <td><?php echo htmlspecialchars($row["description"]); ?></td>
                <td><?php echo number_format($row["price"], 2); ?></td>
                <td><?php echo $row["expiry_date"]; ?></td>
                <td><?php echo $row["quantity"]; ?></td>

                <td>
    <a class="action-link"
       href="edit_product.php?id=<?php echo $row["product_id"]; ?>">
        Edit
    </a>

    <a class="action-link"
       href="delete_product.php?id=<?php echo $row["product_id"]; ?>">
        Delete
    </a>
</td>
            </tr>

        <?php endwhile; ?>

    <?php else: ?>

        <tr>
            <td colspan="7">No products found.</td>
        </tr>

    <?php endif; ?>

</table>

</body>
</html>