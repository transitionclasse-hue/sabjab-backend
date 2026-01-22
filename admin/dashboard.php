<?php
session_start();

// Protect admin panel
if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>SabJab Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
        }
        h1 {
            margin-top: 0;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            margin: 12px 0;
        }
        a {
            display: inline-block;
            padding: 12px 16px;
            background: #222;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            min-width: 250px;
        }
        a:hover {
            background: #000;
        }
        .logout {
            background: #b00020;
        }
        .logout:hover {
            background: #900018;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to SabJab Admin Panel</h1>

        <ul>
            <li><a href="products.php">📦 Manage Products</a></li>
            <li><a href="orders.php">🧾 Manage Orders</a></li>
            <li><a href="minivideo.php">🎥 Manage Mini Video</a></li>
            <li><a class="logout" href="logout.php">🚪 Logout</a></li>
        </ul>
    </div>
</body>
</html>
