<?php

// Connect to the database
// No auth.php because customers do not need to log in
require_once "../config/database.php";

// Initialize search variables
$search = "";
$result = null;
$searched = false;

// Check if the customer submitted a search
if (isset($_GET["search"])) {

    // Remove unnecessary spaces
    $search = trim($_GET["search"]);

    // Only search if the input is not empty
    if ($search !== "") {

        $searched = true;

        // Search products by name only
$sql = "SELECT product_id, name, description, price,
               expiry_date, quantity
        FROM products
        WHERE name LIKE ?
        ORDER BY name ASC";

        $stmt = $conn->prepare($sql);

        // Allow partial product name searches
        $searchName = "%" . $search . "%";

        // Bind parameters to prevent SQL injection
        $stmt->bind_param("s", $searchName);

        // Execute the query
        $stmt->execute();

        // Retrieve matching products
        $result = $stmt->get_result();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Customer Product Search</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<h2>Search Products</h2>

<p>Find products available at our local mart.</p>

<!-- Customer search form -->
<form method="GET" action="search_product.php">

    <label for="search">Enter Product Name:</label>

    <br><br>

    <input
        type="text"
        id="search"
        name="search"
        value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
        required
    >

    <button type="submit">Search</button>

</form>

<br>

<!-- Display results only after a search -->
<?php if ($searched): ?>

    <?php if ($result && $result->num_rows > 0): ?>

        <h3>Search Results</h3>

        <table>

            <tr>
                <th>Product Name</th>
                <th>Description</th>
                <th>Price (RM)</th>
                <th>Expiry Date</th>
                <th>Quantity</th>
                <th>Availability</th>
            </tr>

            <?php while ($row = $result->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?php echo htmlspecialchars($row["name"], ENT_QUOTES, 'UTF-8'); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["description"], ENT_QUOTES, 'UTF-8'); ?>
                    </td>

                    <td>
                        <?php echo number_format((float) $row["price"], 2); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["expiry_date"], ENT_QUOTES, 'UTF-8'); ?>
                    </td>

                    <td>
                        <?php echo (int) $row["quantity"]; ?>
                    </td>

                    <td>
                        <?php
                        if ($row["quantity"] > 0) {
                            echo "In Stock";
                        } else {
                            echo "Out of Stock";
                        }
                        ?>
                    </td>
                </tr>

            <?php endwhile; ?>

        </table>

    <?php else: ?>

        <p class="error-message">
            <strong>Product not found.</strong>
        </p>

    <?php endif; ?>

<?php endif; ?>

</body>
</html>