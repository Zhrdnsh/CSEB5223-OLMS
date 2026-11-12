
<?php
// ============================================
// OLMS Customer Product Browsing
// Retrieve products through the Product API
// ============================================

// Default values
$products = [];
$errorMessage = "";
$keyword = "";

// Retrieve and validate search parameter
$searchInput = $_GET["search"] ?? "";

if (!is_string($searchInput)) {

    $errorMessage = "Invalid search parameter.";

} else {

    $keyword = trim($searchInput);

    // Limit search keyword to 100 characters
    if (strlen($keyword) > 100) {

        $errorMessage = "Search keyword must not exceed 100 characters.";

    } else {

        // Product API endpoint
        $apiUrl = "http://localhost/olms-group/api/product.php";

        // Add search parameter if provided
        if ($keyword !== "") {
            $apiUrl .= "?search=" . urlencode($keyword);
        }

        // Configure API request
        $context = stream_context_create([
            "http" => [
                "method" => "GET",
                "timeout" => 5,
                "ignore_errors" => true
            ]
        ]);

        // Retrieve products from API
        $response = @file_get_contents(
            $apiUrl,
            false,
            $context
        );

        if ($response === false) {

            $errorMessage = "Unable to retrieve products. Please try again later.";

        } else {

            // Retrieve HTTP status code
            $statusCode = 0;

            if (isset($http_response_header[0])) {

                if (preg_match(
                    '/^HTTP\/\S+\s+(\d{3})/',
                    $http_response_header[0],
                    $matches
                )) {
                    $statusCode = (int) $matches[1];
                }
            }

            // Decode JSON response
            $data = json_decode($response, true);

            if (
                $statusCode === 200 &&
                is_array($data) &&
                ($data["success"] ?? false) === true &&
                isset($data["products"]) &&
                is_array($data["products"])
            ) {

                $products = $data["products"];

            } else {

                $errorMessage = "Unable to load product information.";
            }
        }
    }
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
        /* General Styling */
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

        /* Navigation */
        .customer-container nav {
            background: #ffffff;
            padding: 18px 22px;
            border-radius: 12px;
            margin: 25px 0 30px;
        }

        .customer-container nav a {
            color: #a56b7b;
            font-weight: bold;
            margin-right: 15px;
            text-decoration: none;
        }

        .customer-container nav a:hover {
            color: #76515d;
        }

        /* Search Form */
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
            color: #ffffff;
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
            text-decoration: none;
        }

        /* Product Catalogue */
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

        /* Error Message */
        .customer-container .error-message {
            background: #fbe6ea;
            color: #a04e65;
            border: 1px solid #f2cbd5;
            border-radius: 10px;
            padding: 15px 20px;
            margin-top: 20px;
        }

        /* Responsive Design */
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

    <!-- Navigation -->
    <nav>
        <a href="../index.php">Home</a> |
        <a href="customer_browse.php">Browse Products</a> |
        <a href="../admin/admin_login.php">Admin Login</a>
    </nav>

    <!-- Search Form -->
    <form
        method="GET"
        action="customer_browse.php"
        class="search-form"
    >

        <input
            type="text"
            name="search"
            placeholder="Search product name..."
            maxlength="100"
            value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>"
        >

        <button type="submit">Search</button>

        <a href="customer_browse.php">View All</a>

    </form>

    <h2>Product Catalogue</h2>

    <?php if ($errorMessage !== ""): ?>

        <!-- Display error message -->
        <p class="error-message">
            <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
        </p>

    <?php elseif (!empty($products)): ?>

        <!-- Product Grid -->
        <div class="product-grid">

            <?php foreach ($products as $product): ?>

                <div class="product-card">

                    <!-- Product Name -->
                    <h3>
                        <?= htmlspecialchars(
                            $product["name"],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h3>

                    <!-- Product Price -->
                    <p class="product-price">
                        RM <?= number_format(
                            (float) $product["price"],
                            2
                        ) ?>
                    </p>

                    <!-- Product Description -->
                    <p>
                        <?php
                        $description = $product["description"];

                        // Shorten long descriptions
                        if (mb_strlen($description, 'UTF-8') > 100) {
                            $description = mb_substr(
                                $description,
                                0,
                                100,
                                'UTF-8'
                            ) . "...";
                        }

                        echo htmlspecialchars(
                            $description,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>
                    </p>

                    <!-- Product Availability -->
                    <p class="availability">

                        <?php
                        $quantity = (int) $product["quantity"];
                        $expiryDate = $product["expiry_date"];
                        $today = date("Y-m-d");

                        if ($expiryDate < $today) {

                            echo "Expired";

                        } elseif ($quantity <= 0) {

                            echo "Out of stock";

                        } else {

                            echo "In stock";
                        }
                        ?>

                    </p>

                    <!-- View Details -->
                    <a
                        class="details-button"
                        href="product_details.php?id=<?= (int) $product["product_id"] ?>"
                    >
                        View Details
                    </a>

                </div>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <!-- No products found -->
        <p>
            <?= $keyword !== ""
                ? "No products matched your search."
                : "No products are currently available." ?>
        </p>

    <?php endif; ?>

</div>

</body>
</html>
