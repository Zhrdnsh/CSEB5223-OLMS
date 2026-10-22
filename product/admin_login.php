
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
</head>
<body>

<h2>Online Local Mart Management System</h2>
<h3>Admin Login</h3>

<?php if ($error !== ""): ?>
    <p class="error-message">
        <?= htmlspecialchars($error) ?>
    </p>
<?php endif; ?>

<form method="POST" action="admin_login.php">
    <label for="username">Username:</label><br>
    <input
        type="text"
        id="username"
        name="username"
        autocomplete="username"
        required
    >
    <br><br>

    <label for="password">Password:</label><br>
    <input
        type="password"
        id="password"
        name="password"
        autocomplete="current-password"
        required
    >
    <br><br>

    <button type="submit">Login</button>
</form>

</body>
</html>
