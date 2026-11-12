
<?php

// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "olms";

try {

    // Enable exception-based error handling
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    // Establish database connection
    $conn = new mysqli(
        $host,
        $username,
        $password,
        $database
    );

    // Set character encoding
    $conn->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {

    // Log technical error privately
    error_log("Database Connection Error: " . $e->getMessage());

    // Return a general error response
    http_response_code(500);

    // Do not expose database details to users
    exit("Database connection is currently unavailable. Please try again later.");
}
