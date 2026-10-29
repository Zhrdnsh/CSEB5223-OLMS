
<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admin_login.php");
    exit();
}

$token = $_POST["csrf_token"] ?? "";
$sessionToken = $_SESSION["csrf_token"] ?? "";

if (
    !is_string($token) ||
    !is_string($sessionToken) ||
    $sessionToken === "" ||
    !hash_equals($sessionToken, $token)
) {
    http_response_code(403);
    exit("Invalid logout request. Please return to the dashboard.");
}

// Clear all session variables.
$_SESSION = [];

// Expire the session cookie.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(session_name(), "", [
        "expires" => time() - 42000,
        "path" => $params["path"],
        "domain" => $params["domain"],
        "secure" => $params["secure"],
        "httponly" => $params["httponly"],
        "samesite" => "Lax"
    ]);
}

// Destroy the session.
session_destroy();


header("Location: ../index.php");
exit();
?>
