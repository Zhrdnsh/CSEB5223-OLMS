<?php

// Connect to the database
require_once "../config/database.php";

// Initialize search variables
$search = "";
$result = null;
$searched = false;

// Check if a search value has been submitted
if (isset($_GET["search"])) {

    // Get and clean the search input
    $search = trim($_GET["search"]);
    $searched = true;

    // Search for a product by partial name or exact Product ID
    $sql = "SELECT * FROM products
            WHERE name LIKE ?
            OR CAST(product_id AS CHAR) = ?";

    $stmt = $conn->prepare($sql);

    // Add wildcards to allow partial product name searches
    $searchName = "%" . $search . "%";

    // Bind search values and execute the query
    $stmt->bind_param("ss", $searchName, $search);
    $stmt->execute();

    $result = $stmt->get_result();
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Search Product</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<h2>Search Product</h2>

<!-- Product Management navigation -->
<nav>
    <a href="add_product.php">Add Product</a>
    <a href="view_product.php">View Products</a>
</nav>

<!-- Search Product form -->
<form method="GET">

    <label>Enter Product Name or Product ID:</label>
    <br><br>

    <input
        type="text"
        name="search"
        value="<?php echo htmlspecialchars($search); ?>"
        required
    >

    <br><br>

    <button type="submit">Search</button>

</form>

<br>

<!-- Display search result after a search is performed -->
<?php if ($searched): ?>

    <?php if ($result && $result->num_rows > 0): ?>

        <h3>Search Result</h3>

        <table>

            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Description</th>
                <th>Price (RM)</th>
                <th>Expiry Date</th>
                <th>Quantity</th>
            </tr>

            <!-- Display all products that match the search -->
            <?php while ($row = $result->fetch_assoc()): ?>

                <tr>
                    <td>
                        <?php echo $row["product_id"]; ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["name"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["description"]); ?>
                    </td>

                    <td>
                        <?php echo number_format($row["price"], 2); ?>
                    </td>

                    <td>
                        <?php echo $row["expiry_date"]; ?>
                    </td>

                    <td>
                        <?php echo $row["quantity"]; ?>
                    </td>
                </tr>

            <?php endwhile; ?>

        </table>

    <?php else: ?>

        <!-- Display message when no matching product is found -->
        <p class="error-message">
            <strong>Product not found.</strong>
        </p>

    <?php endif; ?>

<?php endif; ?>

<br>

<a href="view_product.php">View All Products</a>

</body>
</html>