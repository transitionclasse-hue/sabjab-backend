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
    $short_description = $_POST["short_description"];
    $description = $_POST["description"];

    $is_featured = isset($_POST["is_featured"]) ? 1 : 0;
    $is_big_deal = isset($_POST["is_big_deal"]) ? 1 : 0;
    $is_mini_deal = isset($_POST["is_mini_deal"]) ? 1 : 0;
    $is_bestseller = isset($_POST["is_bestseller"]) ? 1 : 0;

    $stmt = $conn->prepare("
      INSERT INTO products
      (wc_product_id, name, price, regular_price, sale_price, stock_status, stock_quantity, image_url, short_description, description, is_featured, is_big_deal, is_mini_deal, is_bestseller)
      VALUES (0, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
      "sdddssisiiii",
      $name,
      $price,
      $regular_price,
      $sale_price,
      $stock_status,
      $stock_quantity,
      $image_url,
      $short_description,
      $description,
      $is_featured,
      $is_big_deal,
      $is_mini_deal,
      $is_bestseller
    );

    $stmt->execute();
    header("Location: products.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Add Product</title></head>
<body>

<h2>Add Product</h2>

<form method="post">
  <input name="name" placeholder="Product Name" required><br><br>

  <input name="price" type="number" step="0.01" placeholder="Price"><br><br>
  <input name="regular_price" type="number" step="0.01" placeholder="Regular Price"><br><br>
  <input name="sale_price" type="number" step="0.01" placeholder="Sale Price"><br><br>

  <select name="stock_status">
    <option value="instock">In Stock</option>
    <option value="outofstock">Out of Stock</option>
  </select><br><br>

  <input name="stock_quantity" type="number" value="0"><br><br>

  <input name="image_url" placeholder="Image URL"><br><br>

  <textarea name="short_description" placeholder="Short Description"></textarea><br><br>
  <textarea name="description" placeholder="Full Description"></textarea><br><br>

  <label><input type="checkbox" name="is_featured"> Featured</label><br>
  <label><input type="checkbox" name="is_big_deal"> Big Deal</label><br>
  <label><input type="checkbox" name="is_mini_deal"> Mini Deal</label><br>
  <label><input type="checkbox" name="is_bestseller"> Bestseller</label><br><br>

  <button type="submit">Save Product</button>
</form>

<br>
<a href="products.php">⬅ Back</a>

</body>
</html>
