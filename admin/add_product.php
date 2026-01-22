<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

include "../db_config.php";

if ($_POST) {
    $name = $_POST["name"];
    $price = $_POST["price"];
    $regular_price = $_POST["regular_price"];
    $sale_price = $_POST["sale_price"];
    $stock_status = $_POST["stock_status"];
    $stock_quantity = $_POST["stock_quantity"];
    $image_url = $_POST["image_url"];

    $stmt = $conn->prepare("INSERT INTO products 
    (name, price, regular_price, sale_price, stock_status, stock_quantity, image_url) 
    VALUES (?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("sdddsss", $name, $price, $regular_price, $sale_price, $stock_status, $stock_quantity, $image_url);
    $stmt->execute();

    header("Location: products.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Add Product</title>
</head>
<body>

<h2>Add Product</h2>

<form method="post">
  <input name="name" placeholder="Product Name" required><br><br>
  <input name="price" placeholder="Price" type="number" step="0.01" required><br><br>
  <input name="regular_price" placeholder="Regular Price" type="number" step="0.01"><br><br>
  <input name="sale_price" placeholder="Sale Price" type="number" step="0.01"><br><br>

  <select name="stock_status">
    <option value="instock">In Stock</option>
    <option value="outofstock">Out of Stock</option>
  </select><br><br>

  <input name="stock_quantity" placeholder="Stock Quantity" type="number" value="0"><br><br>

  <input name="image_url" placeholder="Image URL"><br><br>

  <button type="submit">Save Product</button>
</form>

<br>
<a href="products.php">⬅ Back</a>

</body>
</html>
