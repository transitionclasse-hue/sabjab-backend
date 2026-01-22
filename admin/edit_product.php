<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

include "../db_config.php";

$id = (int)$_GET["id"];

$res = $conn->query("SELECT * FROM products WHERE id=$id");
$product = $res->fetch_assoc();

if ($_POST) {
    $name = $_POST["name"];
    $price = $_POST["price"];
    $regular_price = $_POST["regular_price"];
    $sale_price = $_POST["sale_price"];
    $stock_status = $_POST["stock_status"];
    $stock_quantity = $_POST["stock_quantity"];
    $image_url = $_POST["image_url"];

    $stmt = $conn->prepare("UPDATE products SET 
        name=?, 
        price=?, 
        regular_price=?, 
        sale_price=?, 
        stock_status=?, 
        stock_quantity=?, 
        image_url=?
        WHERE id=?");

    $stmt->bind_param("sdddsssi", $name, $price, $regular_price, $sale_price, $stock_status, $stock_quantity, $image_url, $id);
    $stmt->execute();

    header("Location: products.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Product</title>
</head>
<body>

<h2>Edit Product</h2>

<form method="post">
  <input name="name" value="<?php echo htmlspecialchars($product["name"]); ?>" required><br><br>

  <input name="price" type="number" step="0.01" value="<?php echo $product["price"]; ?>"><br><br>

  <input name="regular_price" type="number" step="0.01" value="<?php echo $product["regular_price"]; ?>"><br><br>

  <input name="sale_price" type="number" step="0.01" value="<?php echo $product["sale_price"]; ?>"><br><br>

  <select name="stock_status">
    <option value="instock" <?php if($product["stock_status"]=="instock") echo "selected"; ?>>In Stock</option>
    <option value="outofstock" <?php if($product["stock_status"]=="outofstock") echo "selected"; ?>>Out of Stock</option>
  </select><br><br>

  <input name="stock_quantity" type="number" value="<?php echo $product["stock_quantity"]; ?>"><br><br>

  <input name="image_url" value="<?php echo $product["image_url"]; ?>"><br><br>

  <button type="submit">Update Product</button>
</form>

<br>
<a href="products.php">⬅ Back</a>

</body>
</html>
