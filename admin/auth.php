
<?php
// Start the session if it is not already active
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Redirect unauthenticated users to Admin Login
if (!isset($_SESSION["admin_id"])) {
    header("Location: /olms-group/admin/admin_login.php");
    exit();
}
