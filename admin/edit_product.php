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
    $short_description = $_POST["short_description"];
    $description = $_POST["description"];

    $is_featured = isset($_POST["is_featured"]) ? 1 : 0;
    $is_big_deal = isset($_POST["is_big_deal"]) ? 1 : 0;
    $is_mini_deal = isset($_POST["is_mini_deal"]) ? 1 : 0;
    $is_bestseller = isset($_POST["is_bestseller"]) ? 1 : 0;

    $stmt = $conn->prepare("
      UPDATE products SET
        name=?,
        price=?,
        regular_price=?,
        sale_price=?,
        stock_status=?,
        stock_quantity=?,
        image_url=?,
        short_description=?,
        description=?,
        is_featured=?,
        is_big_deal=?,
        is_mini_deal=?,
        is_bestseller=?
      WHERE id=?
    ");

    $stmt->bind_param(
      "sdddsssssiiiii",
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
      $is_bestseller,
      $id
    );

    $stmt->execute();
    header("Location: products.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Edit Product</title></head>
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

  <textarea name="short_description"><?php echo $product["short_description"]; ?></textarea><br><br>
  <textarea name="description"><?php echo $product["description"]; ?></textarea><br><br>

  <label><input type="checkbox" name="is_featured" <?php if($product["is_featured"]) echo "checked"; ?>> Featured</label><br>
  <label><input type="checkbox" name="is_big_deal" <?php if($product["is_big_deal"]) echo "checked"; ?>> Big Deal</label><br>
  <label><input type="checkbox" name="is_mini_deal" <?php if($product["is_mini_deal"]) echo "checked"; ?>> Mini Deal</label><br>
  <label><input type="checkbox" name="is_bestseller" <?php if($product["is_bestseller"]) echo "checked"; ?>> Bestseller</label><br><br>

  <button type="submit">Update Product</button>
</form>

<br>
<a href="products.php">⬅ Back</a>

</body>
</html>
