
<?php
// Protect this page and load required files
require_once "../admin/auth.php";
require_once "../config/database.php";
require_once "../classes/Product.php";

// Generate a CSRF token for the admin session
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// Default values
$message = "";

$name = "";
$description = "";
$price = "";
$expiryDate = "";
$quantity = "";

// Process the submitted form
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Retrieve and sanitize input values
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

    // Validate product price
    } elseif (
        !is_numeric($price) ||
        !is_finite((float) $price) ||
        (float) $price <= 0 ||
        (float) $price > 99999999.99 ||
        !preg_match('/^\d+(\.\d{1,2})?$/', $price)
    ) {
        $message = "Enter a valid price greater than RM0.00 with up to 2 decimal places.";

    // Validate quantity as a whole number
    } elseif (
        filter_var(
            $quantity,
            FILTER_VALIDATE_INT,
            ["options" => ["min_range" => 0]]
        ) === false
    ) {
        $message = "Quantity must be a whole number of 0 or more.";

    } else {

        // Validate expiry date format
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

            // Create a Product object
            $product = new Product();

            // Set product information
            $product->setName($name);
            $product->setDescription($description);
            $product->setPrice((float) $price);
            $product->setExpiryDate($expiryDate);
            $product->setQuantity((int) $quantity);

            // Retrieve values from the Product object
            $name = $product->getName();
            $description = $product->getDescription();
            $priceValue = $product->getPrice();
            $expiryDate = $product->getExpiryDate();
            $quantityValue = $product->getQuantity();

            try {
                // Prepare the SQL INSERT statement
                $sql = "INSERT INTO products
                        (name, description, price, expiry_date, quantity)
                        VALUES (?, ?, ?, ?, ?)";

                $stmt = $conn->prepare($sql);

                // Bind product values
                $stmt->bind_param(
                    "ssdsi",
                    $name,
                    $description,
                    $priceValue,
                    $expiryDate,
                    $quantityValue
                );

                // Insert product into the database
                $stmt->execute();
                $stmt->close();

                // Redirect after successful insertion
                header("Location: view_product.php?added=1");
                exit();

            } catch (mysqli_sql_exception $e) {

                // Log technical error without exposing it to users
                error_log("Add Product Error: " . $e->getMessage());

                $message = "Failed to add product. Please try again.";
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

    <title>Add Product - OLMS</title>

    <!-- External Stylesheet -->
    <link rel="stylesheet" href="../css/style.css">

    <style>
        /* General Page Styling */
        body {
            background: #fffaf5;
            font-family: Arial, sans-serif;
            color: #554b47;
            margin: 0;
            padding: 30px;
        }

        .add-container {
            max-width: 800px;
            margin: 30px auto;
        }

        /* Page Header */
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

        /* Navigation */
        .add-container nav {
            background: #ffffff;
            padding: 18px 22px;
            border-radius: 12px;
            margin-bottom: 25px;
        }

        .add-container nav a {
            color: #a56b7b;
            font-weight: bold;
            margin-right: 18px;
        }

        .add-container nav a:hover {
            color: #76515d;
        }

        /* Add Product Form */
        .add-form {
            background: #ffffff;
            padding: 35px;
            max-width: none;
            border-radius: 18px;
            border: 1px solid #f0e1e4;
            box-shadow: 0 5px 18px rgba(100, 70, 80, 0.06);
        }

        .add-form label {
            display: block;
            color: #76515d;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .add-form input,
        .add-form textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 13px;
            margin: 0 0 22px;
            border: 1px solid #e8d7d9;
            border-radius: 10px;
            background: #fffdfb;
            font-size: 15px;
            font-family: Arial, sans-serif;
        }

        .add-form textarea {
            min-height: 110px;
            resize: vertical;
        }

        .add-form input:focus,
        .add-form textarea:focus {
            outline: 2px solid #d7b0bb;
            border-color: #ad687b;
        }

        /* Submit Button */
        .add-button {
            background: #a5b99c;
            color: #ffffff;
            padding: 14px 28px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        .add-button:hover {
            background: #829a78;
        }

        /* Error Message */
        .add-container .error-message {
            background: #fbe6ea;
            color: #a04e65;
            border: 1px solid #f2cbd5;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 20px;
        }

        /* Responsive Design */
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

            .add-form {
                padding: 25px 20px;
            }

            .add-container nav {
                line-height: 2;
            }

            .add-button {
                width: 100%;
            }
        }
    </style>

</head>

<body>

<div class="add-container">

    <!-- Page Header -->
    <div class="page-header">
        <h2>Add New Product ♡</h2>
        <p>Add a new item to your local mart inventory.</p>
    </div>

    <!-- Navigation -->
    <nav>
        <a href="../admin/admin_dashboard.php">Dashboard</a>
        <a href="view_product.php">View Products</a>
        <a href="search_product.php">Search Products</a>
    </nav>

    <!-- Error Message -->
    <?php if ($message !== ""): ?>
        <p class="error-message">
            <?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?>
        </p>
    <?php endif; ?>

    <!-- Add Product Form -->
    <form method="POST" class="add-form">

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

        <label for="name">Product Name</label>
        <input
            type="text"
            id="name"
            name="name"
            placeholder="Enter product name"
            maxlength="255"
            value="<?= htmlspecialchars($name, ENT_QUOTES, "UTF-8") ?>"
            required
        >

        <label for="description">Description</label>
        <textarea
            id="description"
            name="description"
            placeholder="Enter product description"
            required
        ><?= htmlspecialchars($description, ENT_QUOTES, "UTF-8") ?></textarea>

        <label for="price">Price (RM)</label>
        <input
            type="number"
            id="price"
            name="price"
            step="0.01"
            min="0.01"
            placeholder="0.00"
            value="<?= htmlspecialchars((string) $price, ENT_QUOTES, "UTF-8") ?>"
            required
        >

        <label for="expiry_date">Expiry Date</label>
        <input
            type="date"
            id="expiry_date"
            name="expiry_date"
            min="<?= date("Y-m-d") ?>"
            value="<?= htmlspecialchars($expiryDate, ENT_QUOTES, "UTF-8") ?>"
            required
        >

        <label for="quantity">Quantity</label>
        <input
            type="number"
            id="quantity"
            name="quantity"
            min="0"
            step="1"
            placeholder="Enter available quantity"
            value="<?= htmlspecialchars((string) $quantity, ENT_QUOTES, "UTF-8") ?>"
            required
        >

        <button type="submit" class="add-button">
            Add Product
        </button>

    </form>

</div>

</body>
</html>
