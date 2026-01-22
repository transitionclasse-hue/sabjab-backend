<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}
include "../db_config.php";

$res = $conn->query("SELECT id, name, price, stock_status, stock_quantity, image_url FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manage Products</title>
</head>
<body>

<h2>Products</h2>
<a href="add_product.php">➕ Add New Product</a>
<br><br>

<table border="1" cellpadding="8">
<tr>
  <th>ID</th>
  <th>Name</th>
  <th>Price</th>
  <th>Stock</th>
  <th>Image</th>
  <th>Actions</th>
</tr>

<?php while($p = $res->fetch_assoc()) { ?>
<tr>
  <td><?php echo $p["id"]; ?></td>
  <td><?php echo htmlspecialchars($p["name"]); ?></td>
  <td>₹<?php echo $p["price"]; ?></td>
  <td><?php echo $p["stock_status"]; ?> (<?php echo $p["stock_quantity"]; ?>)</td>
  <td>
    <?php if (!empty($p["image_url"])) { ?>
      <img src="<?php echo $p["image_url"]; ?>" width="50">
    <?php } ?>
  </td>
  <td>
    <a href="edit_product.php?id=<?php echo $p["id"]; ?>">✏️ Edit</a> |
    <a href="delete_product.php?id=<?php echo $p["id"]; ?>" onclick="return confirm('Delete this product?')">🗑 Delete</a>
  </td>
</tr>
<?php } ?>

</table>

<br>
<a href="dashboard.php">⬅ Back</a>

</body>
</html>
