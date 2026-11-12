
<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Connect to database
require_once "../config/database.php";

// Redirect already logged-in admins
if (isset($_SESSION["admin_id"])) {
    header("Location: admin_dashboard.php");
    exit();
}

// Generate CSRF token for login form
if (empty($_SESSION["login_csrf_token"])) {
    $_SESSION["login_csrf_token"] = bin2hex(random_bytes(32));
}

$error = "";

// Process login form
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Retrieve submitted values
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $submittedToken = $_POST["login_csrf_token"] ?? "";

    // Validate CSRF token
    if (
        !is_string($submittedToken) ||
        !hash_equals($_SESSION["login_csrf_token"], $submittedToken)
    ) {
        $error = "Invalid security token. Please refresh and try again.";

    // Validate required fields
    } elseif ($username === "" || $password === "") {
        $error = "Please enter your username and password.";

    } else {

        try {
            // Find admin by username
            $sql = "SELECT admin_id, username, password
                    FROM admins
                    WHERE username = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();

            $result = $stmt->get_result();
            $admin = $result->fetch_assoc();

            $stmt->close();

            // Verify admin credentials
            if (
                $admin &&
                password_verify($password, $admin["password"])
            ) {

                // Prevent session fixation
                session_regenerate_id(true);

                // Store admin session information
                $_SESSION["admin_id"] = $admin["admin_id"];
                $_SESSION["admin_username"] = $admin["username"];

                // Generate token for protected admin forms
                $_SESSION["csrf_token"] = bin2hex(random_bytes(32));

                // Remove login-only token
                unset($_SESSION["login_csrf_token"]);

                // Redirect to admin dashboard
                header("Location: admin_dashboard.php");
                exit();

            } else {
                $error = "Invalid username or password.";
            }

        } catch (mysqli_sql_exception $e) {

            // Log error without exposing database details
            error_log("Admin Login Error: " . $e->getMessage());

            $error = "Unable to log in right now. Please try again later.";
        }
    }
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login - OLMS</title>

    <link rel="stylesheet" href="../css/style.css">

    <style>
        /* General Styling */
        * {
            box-sizing: border-box;
        }

        body {
            background: #fffaf5;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #554b47;
        }

        /* Login Page Layout */
        .login-page {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px 20px;
        }

        /* Login Card */
        .login-card {
            background: #ffffff;
            width: 100%;
            max-width: 440px;
            padding: 45px 40px;
            border-radius: 22px;
            border: 1px solid #f0e1e4;
            box-shadow: 0 10px 30px rgba(100, 70, 80, 0.07);
            text-align: center;
        }

        .login-logo {
            font-size: 28px;
            font-weight: bold;
            color: #ad687b;
            margin-bottom: 12px;
        }

        .login-card h2 {
            color: #76515d;
            font-size: 25px;
            margin-bottom: 10px;
        }

        .login-subtitle {
            color: #927e81;
            font-size: 14px;
            margin-bottom: 30px;
        }

        /* Login Form */
        .login-form {
            padding: 0;
            max-width: none;
            text-align: left;
            background: transparent;
        }

        .login-form label {
            display: block;
            color: #76515d;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .login-form input:not([type="hidden"]) {
            width: 100%;
            padding: 13px 15px;
            border: 1px solid #e8d7d9;
            border-radius: 10px;
            background: #fffdfb;
            margin: 0 0 20px;
            font-size: 15px;
            font-family: Arial, sans-serif;
        }

        .login-form input:focus {
            outline: 2px solid #d7b0bb;
            border-color: #ad687b;
        }

        /* Login Button */
        .login-button {
            width: 100%;
            background: #a5b99c;
            color: #ffffff;
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .login-button:hover {
            background: #829a78;
        }

        /* Error Message */
        .login-card .error-message {
            background: #fbe6ea;
            color: #a04e65;
            border: 1px solid #f2cbd5;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: left;
            font-size: 14px;
        }

        /* Back to Homepage */
        .back-home {
            display: inline-block;
            margin-top: 25px;
            color: #ad687b;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
        }

        .back-home:hover {
            color: #76515d;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .login-card {
                padding: 30px 22px;
            }
        }
    </style>
</head>

<body>

<div class="login-page">

    <div class="login-card">

        <!-- Logo -->
        <div class="login-logo">OLMS ♡</div>

        <h2>Welcome Back!</h2>

        <p class="login-subtitle">
            Admin Login · Online Local Mart System
        </p>

        <!-- Error Message -->
        <?php if ($error !== ""): ?>
            <p class="error-message">
                <?= htmlspecialchars(
                    $error,
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>
            </p>
        <?php endif; ?>

        <!-- Login Form -->
        <form
            method="POST"
            action="admin_login.php"
            class="login-form"
        >

            <!-- CSRF Protection -->
            <input
                type="hidden"
                name="login_csrf_token"
                value="<?= htmlspecialchars(
                    $_SESSION["login_csrf_token"],
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>"
            >

            <label for="username">Username</label>
            <input
                type="text"
                id="username"
                name="username"
                placeholder="Enter your username"
                autocomplete="username"
                required
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter your password"
                autocomplete="current-password"
                required
            >

            <button type="submit" class="login-button">
                Login
            </button>

        </form>

        <!-- Back to Homepage -->
        <a href="../index.php" class="back-home">
            ← Back to Homepage
        </a>

    </div>

</div>

</body>
</html>
