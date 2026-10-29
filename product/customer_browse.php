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
    body {
        background: #fffaf5;
        color: #554b47;
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    .customer-container {
        max-width: 1100px;
        margin: 40px auto;
        padding: 25px;
    }

    .customer-container h1 {
        color: #76515d;
        font-size: 36px;
        margin-bottom: 10px;
    }

    .customer-container h2 {
        color: #76515d;
        margin-top: 35px;
    }

    .customer-container > p {
        color: #806d70;
    }

    .customer-container nav {
        background: #ffffff;
        padding: 18px 22px;
        border-radius: 12px;
        margin-top: 25px;
        margin-bottom: 30px;
    }

    .customer-container nav a {
        color: #a56b7b;
        font-weight: bold;
        margin-right: 15px;
    }

    .customer-container nav a:hover {
        color: #76515d;
    }

    .search-form {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        background: #fbe6ea;
        padding: 25px;
        border-radius: 15px;
        margin: 25px 0;
        max-width: none;
    }

    .search-form input {
        flex: 1;
        min-width: 200px;
        padding: 13px;
        border: 1px solid #e8c6ce;
        border-radius: 10px;
        margin: 0;
    }

    .search-form button {
        background: #a5b99c;
        color: white;
        padding: 13px 25px;
        border: none;
        border-radius: 10px;
        font-weight: bold;
        cursor: pointer;
    }

    .search-form button:hover {
        background: #829a78;
    }

    .search-form a {
        color: #76515d;
        font-weight: bold;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 22px;
        margin-top: 25px;
    }

    .product-card {
        background: #ffffff;
        border: 1px solid #f0e1e4;
        border-radius: 18px;
        padding: 25px;
        box-shadow: 0 5px 18px rgba(100, 70, 80, 0.06);
        transition: transform 0.2s ease;
    }

    .product-card:hover {
        transform: translateY(-4px);
    }

    .product-card h3 {
        color: #76515d;
        margin-top: 0;
    }

    .product-price {
        color: #ad687b;
        font-size: 22px;
        font-weight: bold;
    }

    .availability {
        color: #718b68;
        font-weight: bold;
    }

    .details-button {
        display: inline-block;
        margin-top: 15px;
        padding: 12px 20px;
        background: #fbe6ea;
        color: #76515d;
        border-radius: 10px;
        font-weight: bold;
        text-decoration: none;
    }

    .details-button:hover {
        background: #f2cbd5;
    }

    @media (max-width: 600px) {
        .customer-container {
            margin: 15px auto;
            padding: 15px;
        }

        .customer-container h1 {
            font-size: 28px;
        }

        .search-form input {
            width: 100%;
        }
    }
</style>

</head>

<body>

<div class="customer-container">

    <h1>Welcome to OLMS</h1>
    <p>Browse the products available in our local mart.</p>


<nav>
    <a href="../index.php">Home</a>
    |
    <a href="customer_browse.php">Browse Products</a>
    |
    <a href="admin_login.php">Admin Login</a>
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