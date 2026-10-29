
<?php
session_start();

require_once "../config/database.php";

if (isset($_SESSION["admin_id"])) {
    header("Location: admin_dashboard.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $error = "Please enter your username and password.";
    } else {
        $stmt = $conn->prepare(
            "SELECT admin_id, username, password
             FROM admins
             WHERE username = ?"
        );

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if (
            $admin &&
            password_verify($password, $admin["password"])
        ) {
            session_regenerate_id(true);

            $_SESSION["admin_id"] = $admin["admin_id"];
            $_SESSION["admin_username"] = $admin["username"];
            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));

            $stmt->close();
            $conn->close();

            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }

        $stmt->close();
    }
}

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
    body {
        background: #fffaf5;
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        color: #554b47;
    }

    .login-page {
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 30px 20px;
    }

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

    .login-form input {
        width: 100%;
        padding: 13px 15px;
        border: 1px solid #e8d7d9;
        border-radius: 10px;
        background: #fffdfb;
        margin: 0 0 20px;
        font-size: 15px;
    }

    .login-form input:focus {
        outline: 2px solid #d7b0bb;
        border-color: #ad687b;
    }

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

    .back-home {
        display: inline-block;
        margin-top: 25px;
        color: #ad687b;
        font-size: 14px;
        font-weight: bold;
    }

    .back-home:hover {
        color: #76515d;
    }

    .login-card .error-message {
        text-align: left;
        border-radius: 10px;
        font-size: 14px;
    }

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

        <div class="login-logo">OLMS ♡</div>

        <h2>Welcome Back!</h2>

        <p class="login-subtitle">
            Admin Login · Online Local Mart System
        </p>

        <?php if ($error !== ""): ?>
            <p class="error-message">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form method="POST"
              action="admin_login.php"
              class="login-form">

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

        <a href="../index.php" class="back-home">
            ← Back to Homepage
        </a>

    </div>

</div>

</body>

</html>
