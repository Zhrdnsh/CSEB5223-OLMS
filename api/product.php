
<?php

// ============================================
// OLMS Product API
// Retrieve and search products using GET
// ============================================

// Set JSON response format
header("Content-Type: application/json; charset=UTF-8");

// Allow only GET requests
if ($_SERVER["REQUEST_METHOD"] !== "GET") {

    header("Allow: GET");
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Method not allowed. Use GET."
    ]);

    exit();
}

// Get and validate search parameter
$searchInput = $_GET["search"] ?? "";

if (!is_string($searchInput)) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid search parameter."
    ]);

    exit();
}

$keyword = trim($searchInput);

// Limit search keyword length
if (strlen($keyword) > 100) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Search keyword is too long."
    ]);

    exit();
}

// Connect to database
// The database file handles connection errors.
require_once "../config/database.php";

try {

    // Retrieve products
    if ($keyword !== "") {

        // Search products by name
        $sql = "SELECT product_id,
                       name,
                       description,
                       price,
                       expiry_date,
                       quantity
                FROM products
                WHERE name LIKE ?
                ORDER BY product_id ASC";

        $stmt = $conn->prepare($sql);

        $searchTerm = "%" . $keyword . "%";

        $stmt->bind_param("s", $searchTerm);
        $stmt->execute();

        $result = $stmt->get_result();

    } else {

        // Retrieve all products
        $sql = "SELECT product_id,
                       name,
                       description,
                       price,
                       expiry_date,
                       quantity
                FROM products
                ORDER BY product_id ASC";

        $result = $conn->query($sql);
    }

    // Store retrieved products
    $products = [];

    while ($row = $result->fetch_assoc()) {

        $products[] = [
            "product_id" => (int) $row["product_id"],
            "name" => $row["name"],
            "description" => $row["description"],
            "price" => (float) $row["price"],
            "expiry_date" => $row["expiry_date"],
            "quantity" => (int) $row["quantity"]
        ];
    }

    // Release database resources
    $result->free();

    if (isset($stmt)) {
        $stmt->close();
    }

    $conn->close();

    // Return successful response
    http_response_code(200);

    echo json_encode([
        "success" => true,
        "message" => "Products retrieved successfully.",
        "products" => $products
    ], JSON_INVALID_UTF8_SUBSTITUTE);

} catch (mysqli_sql_exception $e) {

    // Log database error privately
    error_log("Product API Error: " . $e->getMessage());

    // Return generic JSON error
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to retrieve products. Please try again later."
    ]);
}

?>
