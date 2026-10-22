
<?php
require_once "auth.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - OLMS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<h1>OLMS Admin Dashboard</h1>

<p>
    Welcome,
    <?= htmlspecialchars($_SESSION["admin_username"]) ?>!
</p>

<h2>Product Management</h2>

<nav>
    <a href="add_product.php">Add Product</a>
    <a href="view_product.php">View Products</a>
    <a href="search_product.php">Search Products</a>
</nav>

<h3>Account</h3>

<form action="admin_logout.php" method="POST">
    <input
        type="hidden"
        name="csrf_token"
        value="<?= htmlspecialchars($_SESSION["csrf_token"]) ?>"
    >
    <button type="submit">Logout</button>
</form>

</body>
</html>
