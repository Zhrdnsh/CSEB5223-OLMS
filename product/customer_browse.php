<?php
require_once "../config/database.php";

// Get the customer's search keyword
$keyword = trim($_GET["search"] ?? "");

// Retrieve products, optionally filtered by name
if ($keyword !== "") {
    $sql = "SELECT * FROM products
            WHERE name LIKE ?
            ORDER BY product_id ASC";

    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $keyword . "%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM products ORDER BY product_id ASC";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Products - OLMS</title>
    <link rel="stylesheet" href="../css/style.css">

    <style>
        .customer-container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 20px;
        }

        .search-form {
            margin: 20px 0;
        }

        .search-form input {
            padding: 10px;
            width: 65%;
            max-width: 350px;
        }

        .search-form button,
        .details-button {
            padding: 10px 15px;
            cursor: pointer;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .product-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 18px;
            background: #fff;
        }

        .product-card h3 {
            margin-top: 0;
        }

        .product-price {
            font-size: 20px;
            font-weight: bold;
        }

        .details-button {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            background: #2563eb;
            color: white;
            border-radius: 4px;
        }

        .details-button:hover {
            background: #1d4ed8;
        }

        .availability {
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="customer-container">

    <h1>Welcome to OLMS</h1>
    <p>Browse the products available in our local mart.</p>

    <nav>
        <a href="customer_browse.php">Browse Products</a>
    </nav>

    <form method="GET" action="customer_browse.php" class="search-form">
        <input
            type="text"
            name="search"
            placeholder="Search product name..."
            value="<?php echo htmlspecialchars($keyword); ?>"
        >
        <button type="submit">Search</button>
        <a href="customer_browse.php">View All</a>
    </form>

    <h2>Product Catalogue</h2>

    <?php if ($result && $result->num_rows > 0): ?>

        <div class="product-grid">

            <?php while ($row = $result->fetch_assoc()): ?>

                <div class="product-card">

                    <h3>
                        <?php echo htmlspecialchars($row["name"]); ?>
                    </h3>

                    <p class="product-price">
                        RM <?php echo number_format((float)$row["price"], 2); ?>
                    </p>

                    <p>
                        <?php
                        $description = $row["description"];
                        echo htmlspecialchars(
                            strlen($description) > 100
                                ? substr($description, 0, 100) . "..."
                                : $description
                        );
                        ?>
                    </p>

                    <p class="availability">
                        <?php
                        if ((int)$row["quantity"] <= 0) {
                            echo "Out of stock";
                        } elseif ($row["expiry_date"] < date("Y-m-d")) {
                            echo "Expired";
                        } else {
                            echo "In stock";
                        }
                        ?>
                    </p>

                    <a
                        class="details-button"
                        href="product_details.php?id=<?php echo (int)$row["product_id"]; ?>"
                    >
                        View Details
                    </a>

                </div>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <p>
            <?php
            echo $keyword !== ""
                ? "No products matched your search."
                : "No products are currently available.";
            ?>
        </p>

    <?php endif; ?>

</div>

</body>
</html>