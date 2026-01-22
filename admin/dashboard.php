<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>SabJab Admin Dashboard</title>
</head>
<body>
  <h1>Welcome to SabJab Admin Panel</h1>

  <ul>
    <li><a href="products.php">Manage Products</a></li>
    <li><a href="orders.php">Manage Orders</a></li>
    <li><a href="logout.php">Logout</a></li>
  </ul>

</body>
</html>
