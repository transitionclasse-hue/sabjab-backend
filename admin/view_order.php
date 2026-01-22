<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}
include "../db_config.php";

$id = (int)$_GET["id"];

// Order info
$order = $conn->query("SELECT * FROM orders WHERE id=$id")->fetch_assoc();

// Items
$items = $conn->query("SELECT * FROM order_items WHERE order_id=$id");

// Update status
if (isset($_POST["status"])) {
    $status = $_POST["status"];
    $stmt = $conn->prepare("UPDATE orders SET order_status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    header("Location: view_order.php?id=".$id);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Order #<?php echo $id; ?></title>
</head>
<body>

<h2>Order #<?php echo $id; ?></h2>

<p>
<b>User ID:</b> <?php echo $order["user_id"]; ?><br>
<b>Payment:</b> <?php echo $order["payment_method"]; ?><br>
<b>Status:</b> <?php echo $order["order_status"]; ?><br>
<b>Total:</b> ₹<?php echo $order["order_total"]; ?><br>
<b>Final:</b> ₹<?php echo $order["final_amount"]; ?><br>
</p>

<h3>Items</h3>

<table border="1" cellpadding="8">
<tr>
  <th>Product</th>
  <th>Price</th>
  <th>Qty</th>
  <th>Total</th>
</tr>

<?php while($i = $items->fetch_assoc()) { ?>
<tr>
  <td><?php echo htmlspecialchars($i["product_name"]); ?></td>
  <td>₹<?php echo $i["price_at_purchase"]; ?></td>
  <td><?php echo $i["qty"]; ?></td>
  <td>₹<?php echo $i["price_at_purchase"] * $i["qty"]; ?></td>
</tr>
<?php } ?>
</table>

<h3>Change Order Status</h3>

<form method="post">
  <select name="status">
    <?php
    $statuses = ["Processing","Packed","Dispatched","Completed","Cancelled"];
    foreach ($statuses as $s) {
      $sel = ($order["order_status"] == $s) ? "selected" : "";
      echo "<option value='$s' $sel>$s</option>";
    }
    ?>
  </select>
  <button type="submit">Update Status</button>
</form>

<br>
<a href="orders.php">⬅ Back to Orders</a>

</body>
</html>
