
<?php
// Protect the page and load required files
require_once "../admin/auth.php";
require_once "../config/database.php";
require_once "../classes/Product.php";

// Generate a CSRF token if one does not exist
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$message = "";

// Validate Product ID from URL
$productID = filter_var(
    $_GET["id"] ?? null,
    FILTER_VALIDATE_INT,
    ["options" => ["min_range" => 1]]
);

if ($productID === false || $productID === null) {
    http_response_code(400);
    exit("Invalid Product ID.");
}

// Get existing product information
try {
    $sql = "SELECT * FROM products WHERE product_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productID);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(404);
        exit("Product not found.");
    }

    $row = $result->fetch_assoc();
    $stmt->close();

} catch (mysqli_sql_exception $e) {
    error_log("Fetch Product Error: " . $e->getMessage());
    http_response_code(500);
    exit("Unable to load product information.");
}

// Set initial form values
$name = $row["name"];
$description = $row["description"];
$price = (string) $row["price"];
$expiryDate = $row["expiry_date"];
$quantity = (string) $row["quantity"];

// Process Update Product form
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Retrieve submitted values
    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = trim((string) ($_POST["price"] ?? ""));
    $expiryDate = trim($_POST["expiry_date"] ?? "");
    $quantity = trim((string) ($_POST["quantity"] ?? ""));

    // Validate CSRF token
    $submittedToken = $_POST["csrf_token"] ?? "";

    if (
        !is_string($submittedToken) ||
        !hash_equals($_SESSION["csrf_token"], $submittedToken)
    ) {
        $message = "Invalid security token. Please refresh and try again.";

    // Validate required fields
    } elseif (
        $name === "" ||
        $description === "" ||
        $price === "" ||
        $expiryDate === "" ||
        $quantity === ""
    ) {
        $message = "Please fill in all fields.";

    // Validate product name length
    } elseif (strlen($name) > 255) {
        $message = "Product name must not exceed 255 characters.";

    // Validate price
    } elseif (
        !is_numeric($price) ||
        !is_finite((float) $price) ||
        (float) $price <= 0 ||
        (float) $price > 99999999.99 ||
        !preg_match('/^\d+(\.\d{1,2})?$/', $price)
    ) {
        $message = "Enter a valid price greater than RM0.00 with up to 2 decimal places.";

    // Validate quantity
    } elseif (
        filter_var(
            $quantity,
            FILTER_VALIDATE_INT,
            ["options" => ["min_range" => 0]]
        ) === false
    ) {
        $message = "Quantity must be a whole number of 0 or more.";

    } else {

        // Validate expiry date
        $date = DateTimeImmutable::createFromFormat(
            "!Y-m-d",
            $expiryDate
        );

        if (
            !$date ||
            $date->format("Y-m-d") !== $expiryDate
        ) {
            $message = "Please enter a valid expiry date.";

        } elseif ($expiryDate < date("Y-m-d")) {
            $message = "Expiry date cannot be in the past.";

        } else {

            // Create Product object
            $product = new Product();

            // Set updated product information
            $product->setProductID($productID);
            $product->setName($name);
            $product->setDescription($description);
            $product->setPrice((float) $price);
            $product->setExpiryDate($expiryDate);
            $product->setQuantity((int) $quantity);

            // Retrieve values from Product object
            $name = $product->getName();
            $description = $product->getDescription();
            $priceValue = $product->getPrice();
            $expiryDate = $product->getExpiryDate();
            $quantityValue = $product->getQuantity();

            try {
                // Prepare SQL UPDATE statement
                $sql = "UPDATE products
                        SET name = ?,
                            description = ?,
                            price = ?,
                            expiry_date = ?,
                            quantity = ?
                        WHERE product_id = ?";

                $stmt = $conn->prepare($sql);

                // Bind updated product values
                $stmt->bind_param(
                    "ssdsii",
                    $name,
                    $description,
                    $priceValue,
                    $expiryDate,
                    $quantityValue,
                    $productID
                );

                // Execute UPDATE statement
                $stmt->execute();
                $stmt->close();

                // Redirect after successful update
                header("Location: view_product.php?updated=1");
                exit();

            } catch (mysqli_sql_exception $e) {
                error_log("Update Product Error: " . $e->getMessage());

                $message = "Failed to update product. Please try again.";
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

    <title>Edit Product - OLMS</title>

    <link rel="stylesheet" href="../css/style.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background: #fffaf5;
            font-family: Arial, sans-serif;
            color: #554b47;
            margin: 0;
            padding: 30px;
        }

        .edit-container {
            max-width: 800px;
            margin: 30px auto;
        }

        .page-header {
            background: #fbe6ea;
            padding: 30px;
            border-radius: 18px;
            margin-bottom: 25px;
        }

        .page-header h2 {
            color: #76515d;
            font-size: 30px;
            margin: 0 0 10px;
        }

        .page-header p {
            color: #806d70;
            margin: 0;
        }

        .edit-nav {
            background: #ffffff;
            padding: 18px 22px;
            border-radius: 12px;
            margin-bottom: 25px;
        }

        .edit-nav a {
            color: #a56b7b;
            font-weight: bold;
            margin-right: 18px;
            text-decoration: none;
        }

        .edit-nav a:hover {
            color: #76515d;
        }

        .edit-form {
            background: #ffffff;
            padding: 35px;
            max-width: none;
            border-radius: 18px;
            border: 1px solid #f0e1e4;
            box-shadow: 0 5px 18px rgba(100, 70, 80, 0.06);
        }

        .edit-form label {
            display: block;
            color: #76515d;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .edit-form input,
        .edit-form textarea {
            width: 100%;
            padding: 13px;
            margin: 0 0 22px;
            border: 1px solid #e8d7d9;
            border-radius: 10px;
            background: #fffdfb;
            font-size: 15px;
            font-family: Arial, sans-serif;
        }

        .edit-form input:disabled {
            background: #f6f1ef;
            color: #927e81;
            cursor: not-allowed;
        }

        .edit-form textarea {
            min-height: 110px;
            resize: vertical;
        }

        .edit-form input:focus,
        .edit-form textarea:focus {
            outline: 2px solid #d7b0bb;
            border-color: #ad687b;
        }

        .update-button {
            background: #a5b99c;
            color: #ffffff;
            padding: 14px 28px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        .update-button:hover {
            background: #829a78;
        }

        .edit-container .error-message {
            background: #fbe6ea;
            color: #a04e65;
            border: 1px solid #f2cbd5;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        @media (max-width: 600px) {
            body {
                padding: 15px;
            }

            .page-header {
                padding: 25px;
            }

            .page-header h2 {
                font-size: 25px;
            }

            .edit-form {
                padding: 25px 20px;
            }

            .edit-nav {
                line-height: 2;
            }

            .update-button {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="edit-container">

    <div class="page-header">
        <h2>Edit Product ♡</h2>
        <p>Update your product information and inventory details.</p>
    </div>

    <nav class="edit-nav">
        <a href="../admin/admin_dashboard.php">Dashboard</a>
        <a href="view_product.php">View Products</a>
        <a href="search_product.php">Search Products</a>
    </nav>

    <?php if ($message !== ""): ?>
        <p class="error-message">
            <?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?>
        </p>
    <?php endif; ?>

    <form method="POST" class="edit-form">

        <!-- CSRF Protection -->
        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars(
                $_SESSION["csrf_token"],
                ENT_QUOTES,
                "UTF-8"
            ) ?>"
        >

        <label for="product_id">Product ID</label>
        <input
            type="text"
            id="product_id"
            value="<?= htmlspecialchars(
                (string) $productID,
                ENT_QUOTES,
                "UTF-8"
            ) ?>"
            disabled
        >

        <label for="name">Product Name</label>
        <input
            type="text"
            id="name"
            name="name"
            maxlength="255"
            value="<?= htmlspecialchars($name, ENT_QUOTES, "UTF-8") ?>"
            required
        >

        <label for="description">Description</label>
        <textarea
            id="description"
            name="description"
            required
        ><?= htmlspecialchars($description, ENT_QUOTES, "UTF-8") ?></textarea>

        <label for="price">Price (RM)</label>
        <input
            type="number"
            id="price"
            name="price"
            step="0.01"
            min="0.01"
            value="<?= htmlspecialchars(
                (string) $price,
                ENT_QUOTES,
                "UTF-8"
            ) ?>"
            required
        >

        <label for="expiry_date">Expiry Date</label>
        <input
            type="date"
            id="expiry_date"
            name="expiry_date"
            min="<?= date("Y-m-d") ?>"
            value="<?= htmlspecialchars(
                $expiryDate,
                ENT_QUOTES,
                "UTF-8"
            ) ?>"
            required
        >

        <label for="quantity">Quantity</label>
        <input
            type="number"
            id="quantity"
            name="quantity"
            min="0"
            step="1"
            value="<?= htmlspecialchars(
                (string) $quantity,
                ENT_QUOTES,
                "UTF-8"
            ) ?>"
            required
        >

        <button type="submit" class="update-button">
            Update Product
        </button>

    </form>

</div>

</body>
</html>
